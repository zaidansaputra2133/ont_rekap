<?php

require __DIR__ . '/vendor/autoload.php';

$spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('Laporan WO');

// Header kolom sesuai PRD Tabel 3
$headers = [
    'no_order',
    'cid',
    'serial_number',
    'nama_teknisi',
    'nik_teknisi',
    'status_wo',
    'tanggal_sa',
    'vendor',
    'sektor',
    'cek_match',
];
$sheet->fromArray([$headers], null, 'A1');

// Baris data valid yang pasti cocok dengan database saat ini:
// SN ZTEGC9384938243 cocok dengan teknisi 'Wahyu' di ont_keluars (SESUAI)
// SN ZTEGC3FA7280, HWTC882910AA, FHTT99182301 terdaftar di ont_masuks
$rows = [
    [
        'WO202610001',
        'CID98231',
        'ZTEGC9384938243',
        'Wahyu',
        'TK-001',
        'Work Order Selesai',
        date('Y-m-d'),
        'Telkom Akses PT',
        'SEK-01',
        'SESUAI',
    ],
    [
        'WO202610002',
        'CID98232',
        'ZTEGC3FA7280',
        'Wahyu',
        'TK-001',
        'Work Order Selesai',
        date('Y-m-d'),
        'Telkom Akses PT',
        'SEK-01',
        'SESUAI',
    ],
    [
        'WO202610003',
        'CID98233',
        'HWTC882910AA',
        'Ahmad Kurniawan',
        'TK-002',
        'Work Order Selesai',
        date('Y-m-d'),
        'Telkom Akses PT',
        'SEK-02',
        'SESUAI',
    ],
    [
        'WO202610004',
        'CID98234',
        'FHTT99182301',
        'Ahmad Kurniawan',
        'TK-002',
        'Kendala Jalur Kabel Putus',
        null,
        'Telkom Akses PT',
        'SEK-02',
        'Beda',
    ],
];
$sheet->fromArray($rows, null, 'A2');

// Styling header
$headerStyle = [
    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
    'fill' => [
        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
        'startColor' => ['rgb' => 'B91C1C'],
    ],
    'alignment' => [
        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
    ],
];
$sheet->getStyle('A1:J1')->applyFromArray($headerStyle);
$sheet->getRowDimension(1)->setRowHeight(26);

// Format teks untuk kolom agar tidak diformat angka otomatis oleh Excel
$sheet->getStyle('A2:E5')->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);

// Set auto column width
foreach (range('A', 'J') as $col) {
    $sheet->getColumnDimension($col)->setAutoSize(true);
}

$writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
$target = __DIR__ . '/contoh_laporan_wo_valid.xlsx';
$writer->save($target);

echo "File berhasil dibuat: {$target}\n";
