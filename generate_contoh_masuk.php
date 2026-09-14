<?php

require __DIR__ . '/vendor/autoload.php';

$spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
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
        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
        'startColor' => ['rgb' => 'B91C1C'],
    ],
    'alignment' => [
        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
    ],
];
$sheet->getStyle('A1:C1')->applyFromArray($headerStyle);
$sheet->getRowDimension(1)->setRowHeight(26);
$sheet->getStyle('A2:A5')->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);

foreach (range('A', 'C') as $col) {
    $sheet->getColumnDimension($col)->setAutoSize(true);
}

$writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
$target = __DIR__ . '/contoh_ont_masuk_valid.xlsx';
$writer->save($target);

echo "File berhasil dibuat: {$target}\n";
