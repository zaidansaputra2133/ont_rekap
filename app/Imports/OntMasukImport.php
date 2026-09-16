<?php

namespace App\Imports;

use App\Models\OntMasuk;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Throwable;

class OntMasukImport implements ToModel, WithHeadingRow, SkipsOnError
{
    use SkipsErrors;

    private int $importedCount = 0;
    private int $skippedCount  = 0;

    /**
     * Proses setiap baris dari spreadsheet.
     * Baris dengan SN duplikat dilewati secara otomatis.
     */
    public function model(array $row): ?OntMasuk
    {
        $sn = strtoupper(trim($row['serial_number'] ?? ''));

        // Lewati jika SN kosong atau sudah ada
        if (!$sn || OntMasuk::where('serial_number', $sn)->exists()) {
            $this->skippedCount++;
            return null;
        }

        $tanggal = null;
        if (!empty($row['tanggal_masuk'])) {
            try {
                // Tangani format tanggal dari Excel (angka serial atau string)
                if (is_numeric($row['tanggal_masuk'])) {
                    $tanggal = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['tanggal_masuk'])->format('Y-m-d');
                } else {
                    $tanggal = \Carbon\Carbon::parse($row['tanggal_masuk'])->format('Y-m-d');
                }
            } catch (\Exception $e) {
                $tanggal = now()->format('Y-m-d');
            }
        } else {
            $tanggal = now()->format('Y-m-d');
        }

        $this->importedCount++;

        $brand = !empty($row['brand']) ? trim($row['brand']) : OntMasuk::detectBrand($sn);

        return new OntMasuk([
            'serial_number' => $sn,
            'brand'         => $brand ?: null,
            'tanggal_masuk' => $tanggal,
        ]);
    }

    public function importedCount(): int
    {
        return $this->importedCount;
    }

    public function skippedCount(): int
    {
        return $this->skippedCount;
    }
}
