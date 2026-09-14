<?php

namespace App\Imports;

use App\Models\ReportingWo;
use App\Models\OntMasuk;
use App\Models\OntKeluar;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;

class ReportingWoImport implements ToModel, WithHeadingRow, SkipsOnError
{
    use SkipsErrors;

    private int $importedCount = 0;
    private int $updatedCount = 0;
    private int $unregisteredSnCount = 0;
    private int $skippedCount = 0;
    private array $unregisteredSns = [];

    /**
     * Proses setiap baris dari file spreadsheet.
     */
    public function model(array $row): ?ReportingWo
    {
        // Ekstraksi fleksibel kolom no_order
        $noOrder = trim($row['no_order'] ?? $row['no_wo'] ?? $row['nomor_order'] ?? $row['order_id'] ?? '');
        
        // Ekstraksi fleksibel kolom serial_number
        $sn = strtoupper(trim($row['serial_number'] ?? $row['sn'] ?? $row['serial_no'] ?? $row['sn_ont'] ?? ''));

        // Ekstraksi nama teknisi
        $namaTeknisi = trim($row['nama_teknisi'] ?? $row['teknisi'] ?? $row['nama'] ?? '');

        // Jika baris kosong atau tidak ada no_order / SN, lewati
        if (!$noOrder || !$sn) {
            $this->skippedCount++;
            return null;
        }

        // BR-01: Serial Number wajib terdaftar di ont_masuks
        if (!OntMasuk::where('serial_number', $sn)->exists()) {
            $this->unregisteredSnCount++;
            if (!in_array($sn, $this->unregisteredSns)) {
                $this->unregisteredSns[] = $sn;
            }
            return null;
        }

        // Ekstraksi status_wo
        $statusWo = trim($row['status_wo'] ?? $row['status'] ?? 'Work Order Selesai');

        // Ekstraksi cid & nik
        $cid = !empty($row['cid']) ? trim($row['cid']) : (!empty($row['circuit_id']) ? trim($row['circuit_id']) : null);
        $nikTeknisi = !empty($row['nik_teknisi']) ? trim($row['nik_teknisi']) : (!empty($row['nik']) ? trim($row['nik']) : null);
        $vendor = !empty($row['vendor']) ? trim($row['vendor']) : (!empty($row['mitra']) ? trim($row['mitra']) : null);
        $sektor = !empty($row['sektor']) ? trim($row['sektor']) : null;

        // Parsing tanggal_sa
        $tanggalSa = null;
        $tglRaw = $row['tanggal_sa'] ?? $row['tanggal'] ?? $row['tgl_sa'] ?? null;
        if (!empty($tglRaw)) {
            try {
                if (is_numeric($tglRaw)) {
                    $tanggalSa = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($tglRaw)->format('Y-m-d');
                } else {
                    $tanggalSa = \Carbon\Carbon::parse($tglRaw)->format('Y-m-d');
                }
            } catch (\Exception $e) {
                $tanggalSa = now()->format('Y-m-d');
            }
        }

        // PRD 4.C (Auto-Match): Cek kesesuaian dengan ont_keluars
        $cekMatch = null;
        if (!empty($row['cek_match'])) {
            $cekMatch = trim($row['cek_match']);
        } else {
            $ontKeluar = OntKeluar::where('serial_number', $sn)->first();
            if ($ontKeluar && strcasecmp(trim($ontKeluar->nama_teknisi), $namaTeknisi) === 0) {
                $cekMatch = 'SESUAI';
            } elseif ($ontKeluar) {
                $cekMatch = 'Beda';
            } else {
                $cekMatch = 'SESUAI';
            }
        }

        // Cek apakah no_order sudah pernah ada
        $existing = ReportingWo::where('no_order', $noOrder)->first();
        if ($existing) {
            $existing->update([
                'cid'           => $cid,
                'serial_number' => $sn,
                'nama_teknisi'  => $namaTeknisi ?: $existing->nama_teknisi,
                'nik_teknisi'   => $nikTeknisi ?: $existing->nik_teknisi,
                'status_wo'     => $statusWo,
                'tanggal_sa'    => $tanggalSa ?: $existing->tanggal_sa,
                'vendor'        => $vendor ?: $existing->vendor,
                'sektor'        => $sektor ?: $existing->sektor,
                'cek_match'     => $cekMatch ?: $existing->cek_match,
            ]);
            $this->updatedCount++;
            return null;
        }

        $this->importedCount++;

        return new ReportingWo([
            'no_order'      => $noOrder,
            'cid'           => $cid,
            'serial_number' => $sn,
            'nama_teknisi'  => $namaTeknisi,
            'nik_teknisi'   => $nikTeknisi,
            'status_wo'     => $statusWo,
            'tanggal_sa'    => $tanggalSa,
            'vendor'        => $vendor,
            'sektor'        => $sektor,
            'cek_match'     => $cekMatch,
        ]);
    }

    public function importedCount(): int
    {
        return $this->importedCount;
    }

    public function updatedCount(): int
    {
        return $this->updatedCount;
    }

    public function unregisteredSnCount(): int
    {
        return $this->unregisteredSnCount;
    }

    public function skippedCount(): int
    {
        return $this->skippedCount;
    }

    public function unregisteredSns(): array
    {
        return $this->unregisteredSns;
    }
}
