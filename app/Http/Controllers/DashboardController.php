<?php

namespace App\Http\Controllers;

use App\Models\OntMasuk;
use App\Models\OntKeluar;
use App\Models\ReportingWo;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Dashboard Utama: Menampilkan ringkasan metrik dan tabel rekapitulasi per teknisi
     * Sesuai spesifikasi PRD Modul 4.D dan Business Rules BR-01 s/d BR-04.
     */
    public function index()
    {
        // --- 1. Metric Cards Utama (PRD 4.D #1) ---
        $totalMasuk  = OntMasuk::count();
        $totalKeluar = OntKeluar::count();
        $totalRusak  = OntKeluar::where('keterangan', 'Rusak')->count();
        $sisaGudang  = max(0, $totalMasuk - $totalKeluar);

        // INSTALLED: Total unit unik berstatus 'Work Order Selesai' di reporting_wos
        $totalInstalled = ReportingWo::where('status_wo', 'like', '%selesai%')
            ->distinct('serial_number')
            ->count('serial_number');

        // NOT INSTALLED: Selisih unit yang berada di teknisi namun belum selesai dipasang
        $totalNotInstalled = max(0, $totalKeluar - $totalInstalled);

        // --- 2. Tabel Rekapitulasi per Teknisi (PRD 4.D #2) ---
        // Kumpulkan daftar personil teknisi yang ada di ont_keluars dan reporting_wos
        $teknisiKeluar = OntKeluar::select('nama_teknisi')->whereNotNull('nama_teknisi')->distinct()->pluck('nama_teknisi');
        $teknisiWo     = ReportingWo::select('nama_teknisi')->whereNotNull('nama_teknisi')->distinct()->pluck('nama_teknisi');
        $semuaTeknisi  = $teknisiKeluar->merge($teknisiWo)->filter()->unique()->sort()->values();

        $rekapTeknisi = [];
        foreach ($semuaTeknisi as $nama) {
            $totalDiambil = OntKeluar::where('nama_teknisi', $nama)->count();
            $rusak        = OntKeluar::where('nama_teknisi', $nama)->where('keterangan', 'Rusak')->count();
            $snsDiambil   = OntKeluar::where('nama_teknisi', $nama)->pluck('serial_number')->toArray();

            // Hitung unit INSTALLED milik teknisi ini:
            // Dicocokkan dari SN yang pernah diserahkan ke teknisi ini ATAU dari nama teknisi di laporan WO
            $installed = ReportingWo::where(function ($q) use ($nama, $snsDiambil) {
                    $q->where('nama_teknisi', $nama);
                    if (!empty($snsDiambil)) {
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
                'nama'          => $nama,
                'installed'     => $installed,
                'not_installed' => $notInstalled,
                'rusak'         => $rusak,
                'total_dibawa'  => $totalDibawa,
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
}
