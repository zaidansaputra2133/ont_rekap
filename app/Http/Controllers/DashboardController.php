<?php

namespace App\Http\Controllers;

use App\Models\OntKeluar;
use App\Models\OntMasuk;
use App\Models\ReportingWo;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Dashboard Utama: Menampilkan ringkasan metrik dan tabel rekapitulasi per teknisi
     * Sesuai spesifikasi PRD Modul 4.D dan Business Rules BR-01 s/d BR-04.
     */
    public function index()
    {
        // --- 1. Metric Cards Utama (PRD 4.D #1) ---
        $totalMasuk = OntMasuk::count();
        $totalKeluar = OntKeluar::count();
        $totalRusak = OntKeluar::where('keterangan', 'Rusak')->count();
        $sisaGudang = max(0, $totalMasuk - $totalKeluar);

        // INSTALLED: Total unit unik berstatus 'Work Order Selesai' di reporting_wos
        $totalInstalled = ReportingWo::where('status_wo', 'like', '%selesai%')
            ->distinct('serial_number')
            ->count('serial_number');

        // NOT INSTALLED: Selisih unit yang berada di teknisi namun belum selesai dipasang
        $totalNotInstalled = max(0, $totalKeluar - $totalInstalled);

        // --- 2. Tabel Rekapitulasi per Teknisi (PRD 4.D #2) ---
        // Kumpulkan daftar personil teknisi yang ada di ont_keluars dan reporting_wos
        $teknisiKeluar = OntKeluar::select('nama_teknisi')->whereNotNull('nama_teknisi')->distinct()->pluck('nama_teknisi');
        $teknisiWo = ReportingWo::select('nama_teknisi')->whereNotNull('nama_teknisi')->distinct()->pluck('nama_teknisi');
        $semuaTeknisi = $teknisiKeluar->merge($teknisiWo)->filter()->unique()->sort()->values();

        $rekapTeknisi = [];
        foreach ($semuaTeknisi as $nama) {
            $totalDiambil = OntKeluar::where('nama_teknisi', $nama)->count();
            $rusak = OntKeluar::where('nama_teknisi', $nama)->where('keterangan', 'Rusak')->count();
            $snsDiambil = OntKeluar::where('nama_teknisi', $nama)->pluck('serial_number')->toArray();

            // Hitung unit INSTALLED milik teknisi ini:
            // Dicocokkan dari SN yang pernah diserahkan ke teknisi ini ATAU dari nama teknisi di laporan WO
            $installed = ReportingWo::where(function ($q) use ($nama, $snsDiambil) {
                $q->where('nama_teknisi', $nama);
                if (! empty($snsDiambil)) {
                    $q->orWhereIn('serial_number', $snsDiambil);
                }
            })
                ->where('status_wo', 'like', '%selesai%')
                ->distinct('serial_number')
                ->count('serial_number');

            // Total unit fisik yang dibawa teknisi
            $totalDibawa = max($totalDiambil, $installed);

            // NOT INSTALLED: Sesuai formula PRD 4.D (Total Dibawa - INSTALLED)
            $notInstalled = max(0, $totalDibawa - $installed);

            $rekapTeknisi[] = [
                'nama' => $nama,
                'installed' => $installed,
                'not_installed' => $notInstalled,
                'rusak' => $rusak,
                'total_dibawa' => $totalDibawa,
                'rasio' => $totalDibawa > 0 ? (int) round($installed / $totalDibawa * 100) : 0,
            ];
        }

        // Urutkan teknisi berdasarkan akumulasi unit terbanyak
        usort($rekapTeknisi, function ($a, $b) {
            if ($b['total_dibawa'] === $a['total_dibawa']) {
                return $b['installed'] <=> $a['installed'];
            }

            return $b['total_dibawa'] <=> $a['total_dibawa'];
        });

        $totalTeknisi = count($rekapTeknisi);

        return view('dashboard', compact(
            'totalMasuk',
            'totalKeluar',
            'totalInstalled',
            'totalNotInstalled',
            'totalRusak',
            'sisaGudang',
            'rekapTeknisi',
            'totalTeknisi'
        ));
    }

    /**
     * AJAX endpoint: data chart ONT Masuk & Keluar
     *
     * Query params:
     *   mode  = daily | weekly | monthly
     *   date  = YYYY-MM-DD  (untuk mode daily → tampilkan 30 hari terakhir sejak date)
     *   month = YYYY-MM     (untuk mode daily → hanya hari dalam bulan tsb)
     *   year  = YYYY        (untuk mode monthly → 12 bulan dalam tahun tsb)
     */
    public function chartData(Request $request): JsonResponse
    {
        $mode = $request->input('mode', 'daily');
        $year = (int) $request->input('year', now()->year);
        $month = $request->input('month'); // format YYYY-MM

        $labels = [];
        $masuk = [];
        $keluar = [];

        if ($mode === 'daily') {
            // Tampilkan setiap hari dalam bulan yang dipilih
            if ($month) {
                [$y, $m] = explode('-', $month);
                $start = Carbon::createFromDate((int) $y, (int) $m, 1)->startOfDay();
                $end = $start->copy()->endOfMonth();
            } else {
                // Default: 30 hari terakhir
                $end = now()->endOfDay();
                $start = now()->subDays(29)->startOfDay();
            }

            $masukRaw = OntMasuk::selectRaw('DATE(tanggal_masuk) as tgl, COUNT(*) as total')
                ->whereBetween('tanggal_masuk', [$start, $end])
                ->groupBy('tgl')->pluck('total', 'tgl');

            $keluarRaw = OntKeluar::selectRaw('DATE(tanggal_keluar) as tgl, COUNT(*) as total')
                ->whereBetween('tanggal_keluar', [$start, $end])
                ->groupBy('tgl')->pluck('total', 'tgl');

            $cursor = $start->copy();
            while ($cursor->lte($end)) {
                $key = $cursor->toDateString();
                $labels[] = $cursor->format('d M');
                $masuk[] = (int) ($masukRaw[$key] ?? 0);
                $keluar[] = (int) ($keluarRaw[$key] ?? 0);
                $cursor->addDay();
            }

        } elseif ($mode === 'weekly') {
            if ($month) {
                [$y, $m] = explode('-', $month);
                $startMonth = Carbon::createFromDate((int) $y, (int) $m, 1)->startOfDay();
                $daysInMonth = $startMonth->daysInMonth;
                $monthName = $startMonth->translatedFormat('M');

                $weekRanges = [
                    [1, 7],
                    [8, 14],
                    [15, 21],
                    [22, 28],
                ];
                if ($daysInMonth > 28) {
                    $weekRanges[] = [29, $daysInMonth];
                }

                foreach ($weekRanges as $idx => $range) {
                    $wStart = Carbon::createFromDate((int) $y, (int) $m, $range[0])->startOfDay();
                    $wEnd = Carbon::createFromDate((int) $y, (int) $m, $range[1])->endOfDay();

                    $labels[] = 'Mg '.($idx + 1).' ('.sprintf('%02d', $range[0]).'–'.sprintf('%02d', $range[1]).' '.$monthName.')';
                    $masuk[] = (int) OntMasuk::whereBetween('tanggal_masuk', [$wStart, $wEnd])->count();
                    $keluar[] = (int) OntKeluar::whereBetween('tanggal_keluar', [$wStart, $wEnd])->count();
                }
            } else {
                // Tampilkan 12 minggu terakhir
                $end = now()->endOfWeek();
                $start = now()->subWeeks(11)->startOfWeek();

                $masukRaw = OntMasuk::selectRaw('YEARWEEK(tanggal_masuk, 1) as yw, COUNT(*) as total')
                    ->whereBetween('tanggal_masuk', [$start, $end])
                    ->groupBy('yw')->pluck('total', 'yw');

                $keluarRaw = OntKeluar::selectRaw('YEARWEEK(tanggal_keluar, 1) as yw, COUNT(*) as total')
                    ->whereBetween('tanggal_keluar', [$start, $end])
                    ->groupBy('yw')->pluck('total', 'yw');

                $cursor = $start->copy()->startOfWeek();
                for ($i = 0; $i < 12; $i++) {
                    $yw = $cursor->format('oW'); // ISO year+week e.g. 202438
                    $labels[] = $cursor->format('d M').'–'.$cursor->copy()->endOfWeek()->format('d M');
                    $masuk[] = (int) ($masukRaw[$yw] ?? 0);
                    $keluar[] = (int) ($keluarRaw[$yw] ?? 0);
                    $cursor->addWeek();
                }
            }

        } else {
            // monthly: 12 bulan dalam tahun yang dipilih
            $masukRaw = OntMasuk::selectRaw('MONTH(tanggal_masuk) as bln, COUNT(*) as total')
                ->whereYear('tanggal_masuk', $year)
                ->groupBy('bln')->pluck('total', 'bln');

            $keluarRaw = OntKeluar::selectRaw('MONTH(tanggal_keluar) as bln, COUNT(*) as total')
                ->whereYear('tanggal_keluar', $year)
                ->groupBy('bln')->pluck('total', 'bln');

            $bulanNames = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
            for ($m = 1; $m <= 12; $m++) {
                $labels[] = $bulanNames[$m - 1];
                $masuk[] = (int) ($masukRaw[$m] ?? 0);
                $keluar[] = (int) ($keluarRaw[$m] ?? 0);
            }
        }

        return response()->json(compact('labels', 'masuk', 'keluar'));
    }
}
