<?php

namespace App\Http\Controllers;

use App\Models\OntKeluar;
use App\Models\OntMasuk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OntKeluarController extends Controller
{
    /**
     * Tampilkan daftar transaksi ONT Keluar + search, filter status, pagination.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status');
        $teknisi = $request->input('teknisi');
        $tanggal = $request->input('tanggal');

        $items = OntKeluar::query()
            ->search($search)
            ->filterTeknisi($teknisi)
            ->filterTanggal($tanggal)
            ->filterStatus($status)
            ->orderBy('created_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        $totalKeluar = OntKeluar::count();

        // Ambil unit ONT di gudang yang BELUM PERNAH keluar (stok yang tersedia untuk diserahkan)
        $availableOnts = OntMasuk::whereNotIn('serial_number', function ($query) {
            $query->select('serial_number')->from('ont_keluars');
        })
            ->orderBy('created_at', 'desc')
            ->get(['serial_number', 'brand', 'tanggal_masuk']);

        // Pastikan brand terisi (auto-detect jika di DB belum ada brand)
        foreach ($availableOnts as $ont) {
            if (empty($ont->brand)) {
                $ont->brand = OntMasuk::detectBrand($ont->serial_number) ?? 'Lainnya';
            }
        }

        // Ambil daftar brand unik yang tersedia di stok gudang
        $availableBrands = $availableOnts->pluck('brand')->filter()->unique()->values()->all();

        // Ambil daftar nama teknisi yang sudah ada untuk saran pengetikan cepat
        $daftarTeknisi = OntKeluar::select('nama_teknisi')
            ->distinct()
            ->whereNotNull('nama_teknisi')
            ->orderBy('nama_teknisi')
            ->pluck('nama_teknisi');

        return view('ont-keluar.index', compact('items', 'totalKeluar', 'search', 'status', 'teknisi', 'tanggal', 'availableOnts', 'availableBrands', 'daftarTeknisi'));
    }

    /**
     * Catat penyerahan 1 atau lebih unit ONT sekaligus ke teknisi.
     * Validasi: SN wajib ada di ont_masuks & belum pernah keluar.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_teknisi' => ['required', 'string', 'max:150'],
            'tanggal_keluar' => ['required', 'date'],
            'serial_number' => ['required_without:serial_numbers'],
        ], [
            'nama_teknisi.required' => 'Nama teknisi wajib diisi.',
            'tanggal_keluar.required' => 'Tanggal penyerahan wajib diisi.',
            'serial_number.required_without' => 'Serial Number wajib diisi.',
        ]);

        $rawInput = $request->input('serial_number') ?? $request->input('serial_numbers');

        if (is_array($rawInput)) {
            $snList = $rawInput;
        } else {
            // Split berdasarkan baris baru, koma, titik koma, atau spasi
            $snList = preg_split('/[\r\n,;\s]+/', (string) $rawInput, -1, PREG_SPLIT_NO_EMPTY);
        }

        // Clean & normalize (uppercase, trim, unique, non-empty)
        $snList = array_values(array_unique(array_filter(array_map(function ($sn) {
            return strtoupper(trim($sn));
        }, $snList))));

        if (empty($snList)) {
            return back()
                ->withInput()
                ->withErrors(['serial_number' => 'Serial Number wajib diisi. Masukkan setidaknya 1 Serial Number.']);
        }

        // BR-01: SN harus ada di ont_masuks
        $validMasukSns = OntMasuk::whereIn('serial_number', $snList)->pluck('serial_number')->toArray();
        $invalidMasuk = array_diff($snList, $validMasukSns);

        // BR-02: Satu SN hanya boleh keluar satu kali
        $sudahKeluarSns = OntKeluar::whereIn('serial_number', $snList)->pluck('serial_number')->toArray();
        $alreadyIssued = array_intersect($snList, $sudahKeluarSns);

        $errorMessages = [];
        if (! empty($invalidMasuk)) {
            $errorMessages[] = 'SN tidak ditemukan di stok gudang (ONT Masuk): '.implode(', ', $invalidMasuk).'.';
        }
        if (! empty($alreadyIssued)) {
            $errorMessages[] = 'SN sudah pernah tercatat keluar sebelumnya: '.implode(', ', $alreadyIssued).'.';
        }

        if (! empty($errorMessages)) {
            return back()
                ->withInput()
                ->withErrors(['serial_number' => implode(' ', $errorMessages)]);
        }

        DB::transaction(function () use ($snList, $request) {
            $namaTeknisi = trim($request->nama_teknisi);
            $tanggalKeluar = $request->tanggal_keluar;

            foreach ($snList as $sn) {
                OntKeluar::create([
                    'serial_number' => $sn,
                    'nama_teknisi' => $namaTeknisi,
                    'tanggal_keluar' => $tanggalKeluar,
                    'keterangan' => null,
                    'catatan' => null,
                ]);
            }
        });

        $count = count($snList);
        $snSummary = $count <= 3 ? implode(', ', $snList) : implode(', ', array_slice($snList, 0, 3)).' (+ '.($count - 3).' unit lainnya)';

        $successMsg = $count === 1
            ? "Penyerahan unit {$snList[0]} ke {$request->nama_teknisi} berhasil dicatat."
            : "Penyerahan {$count} unit ONT ({$snSummary}) ke {$request->nama_teknisi} berhasil dicatat.";

        return redirect()->route('ont-keluar.index')->with('success', $successMsg);
    }

    /**
     * Update status kondisi perangkat (Normal / Rusak) dan catatan kerusakan.
     */
    public function updateStatus(Request $request)
    {
        $request->validate([
            'id' => ['required', 'exists:ont_keluars,id'],
            'keterangan' => ['nullable', 'in:,Rusak'],
            'catatan' => ['nullable', 'string', 'max:2000'],
        ]);

        $ontKeluar = OntKeluar::findOrFail($request->id);

        $keterangan = $request->keterangan === 'Rusak' ? 'Rusak' : null;
        $catatan = $keterangan === 'Rusak' ? $request->catatan : null;

        $ontKeluar->update([
            'keterangan' => $keterangan,
            'catatan' => $catatan,
        ]);

        return redirect()->route('ont-keluar.index')
            ->with('success', "Status kondisi SN {$ontKeluar->serial_number} berhasil diperbarui.");
    }

    /**
     * Hapus transaksi ONT Keluar.
     */
    public function destroy(OntKeluar $ontKeluar)
    {
        $sn = $ontKeluar->serial_number;
        $ontKeluar->delete();

        return redirect()->route('ont-keluar.index')
            ->with('success', "Transaksi keluar SN {$sn} berhasil dihapus.");
    }
}
