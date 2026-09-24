<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

require __DIR__.'/vendor/autoload.php';

$spreadsheet = new Spreadsheet;
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('ONT Masuk');

$headers = ['serial_number', 'brand', 'tanggal_masuk'];
$sheet->fromArray([$headers], null, 'A1');

$rows = [
    ['ZTEGC44882201', 'ZTE', date('Y-m-d')],
    ['HWTC99221102', 'Huawei', date('Y-m-d')],
    ['FHTT55667703', 'Fiberhome', date('Y-m-d')],
    ['NOKG11223304', 'Nokia', date('Y-m-d')],
];
$sheet->fromArray($rows, null, 'A2');

$headerStyle = [
    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['rgb' => 'B91C1C'],
    ],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
        'vertical' => Alignment::VERTICAL_CENTER,
    ],
];
$sheet->getStyle('A1:C1')->applyFromArray($headerStyle);
$sheet->getRowDimension(1)->setRowHeight(26);
$sheet->getStyle('A2:A5')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);

foreach (range('A', 'C') as $col) {
    $sheet->getColumnDimension($col)->setAutoSize(true);
}

$writer = new Xlsx($spreadsheet);
$target = __DIR__.'/contoh_ont_masuk_valid.xlsx';
$writer->save($target);

echo "File berhasil dibuat: {$target}\n";
