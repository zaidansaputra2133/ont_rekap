<?php

namespace App\Http\Controllers;

use App\Models\OntKeluar;
use App\Models\OntMasuk;
use Illuminate\Http\Request;

class OntKeluarController extends Controller
{
    /**
     * Tampilkan daftar transaksi ONT Keluar + search, filter status, pagination.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status');

        $items = OntKeluar::query()
            ->search($search)
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

        // Ambil daftar nama teknisi yang sudah ada untuk saran pengetikan cepat
        $daftarTeknisi = OntKeluar::select('nama_teknisi')
            ->distinct()
            ->whereNotNull('nama_teknisi')
            ->orderBy('nama_teknisi')
            ->pluck('nama_teknisi');

        return view('ont-keluar.index', compact('items', 'totalKeluar', 'search', 'status', 'availableOnts', 'daftarTeknisi'));
    }

    /**
     * Catat penyerahan ONT ke teknisi.
     * Validasi: SN wajib ada di ont_masuks & belum pernah keluar.
     */
    public function store(Request $request)
    {
        $request->validate([
            'serial_number' => ['required', 'string', 'max:100'],
            'nama_teknisi'  => ['required', 'string', 'max:150'],
            'tanggal_keluar'=> ['required', 'date'],
        ], [
            'serial_number.required'  => 'Serial Number wajib diisi.',
            'nama_teknisi.required'   => 'Nama teknisi wajib diisi.',
            'tanggal_keluar.required' => 'Tanggal penyerahan wajib diisi.',
        ]);

        $sn = strtoupper(trim($request->serial_number));

        // BR-01: SN harus ada di ont_masuks
        $ontMasuk = OntMasuk::where('serial_number', $sn)->first();
        if (!$ontMasuk) {
            return back()
                ->withInput()
                ->withErrors(['serial_number' => "Serial Number {$sn} tidak ditemukan di data gudang. Pastikan unit sudah diinput di ONT Masuk."]);
        }

        // BR-02: Satu SN hanya boleh keluar satu kali
        $sudahKeluar = OntKeluar::where('serial_number', $sn)->exists();
        if ($sudahKeluar) {
            return back()
                ->withInput()
                ->withErrors(['serial_number' => "Serial Number {$sn} sudah pernah tercatat keluar sebelumnya."]);
        }

        OntKeluar::create([
            'serial_number'  => $sn,
            'nama_teknisi'   => trim($request->nama_teknisi),
            'tanggal_keluar' => $request->tanggal_keluar,
            'keterangan'     => null,
            'catatan'        => null,
        ]);

        return redirect()->route('ont-keluar.index')
            ->with('success', "Penyerahan unit {$sn} ke {$request->nama_teknisi} berhasil dicatat.");
    }

    /**
     * Update status kondisi perangkat (Normal / Rusak) dan catatan kerusakan.
     */
    public function updateStatus(Request $request)
    {
        $request->validate([
            'id'          => ['required', 'exists:ont_keluars,id'],
            'keterangan'  => ['nullable', 'in:,Rusak'],
            'catatan'     => ['nullable', 'string', 'max:2000'],
        ]);

        $ontKeluar = OntKeluar::findOrFail($request->id);

        $keterangan = $request->keterangan === 'Rusak' ? 'Rusak' : null;
        $catatan    = $keterangan === 'Rusak' ? $request->catatan : null;

        $ontKeluar->update([
            'keterangan' => $keterangan,
            'catatan'    => $catatan,
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
