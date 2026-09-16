<?php

namespace App\Http\Controllers;

use App\Models\OntMasuk;
use App\Imports\OntMasukImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class OntMasukController extends Controller
{
    /**
     * Tampilkan daftar ONT Masuk dengan pencarian, filter brand, dan pagination.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $brand  = $request->input('brand');

        $items = OntMasuk::query()
            ->search($search)
            ->filterBrand($brand)
            ->orderBy('created_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        $totalCount = OntMasuk::count();

        return view('ont-masuk.index', compact('items', 'totalCount', 'search', 'brand'));
    }

    /**
     * Simpan 1 unit ONT baru (input manual).
     */
    public function store(Request $request)
    {
        $request->validate([
            'serial_number' => ['required', 'string', 'max:100', 'unique:ont_masuks,serial_number'],
            'tanggal_masuk' => ['required', 'date'],
            'brand'         => ['nullable', 'string', 'max:50'],
        ], [
            'serial_number.required' => 'Serial Number wajib diisi.',
            'serial_number.unique'   => 'Serial Number ini sudah terdaftar di sistem.',
            'tanggal_masuk.required' => 'Tanggal penerimaan wajib diisi.',
        ]);

        $sn = strtoupper(trim($request->serial_number));
        $brand = $request->brand ?: OntMasuk::detectBrand($sn);

        OntMasuk::create([
            'serial_number' => $sn,
            'brand'         => $brand ?: null,
            'tanggal_masuk' => $request->tanggal_masuk,
        ]);

        return redirect()->route('ont-masuk.index')
            ->with('success', "Unit ONT {$sn}" . ($brand ? " ({$brand})" : '') . " berhasil ditambahkan ke inventaris.");
    }

    /**
     * Import massal dari file Excel / CSV.
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:10240'],
        ], [
            'file.required' => 'Pilih file Excel/CSV terlebih dahulu.',
            'file.mimes'    => 'Format file harus .xlsx, .xls, atau .csv.',
            'file.max'      => 'Ukuran file maksimal 10 MB.',
        ]);

        $import = new OntMasukImport();
        Excel::import($import, $request->file('file'));

        $imported = $import->importedCount();
        $skipped  = $import->skippedCount();

        $message = "Import selesai. {$imported} unit berhasil ditambahkan.";
        if ($skipped > 0) {
            $message .= " {$skipped} baris dilewati (SN duplikat).";
        }

        return redirect()->route('ont-masuk.index')->with('success', $message);
    }

    /**
     * Download template file Excel untuk impor ONT Masuk.
     */
    public function downloadTemplate()
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template ONT Masuk');

        // Header kolom
        $headers = ['serial_number', 'brand', 'tanggal_masuk'];
        $sheet->fromArray([$headers], null, 'A1');

        // Data sampel
        $samples = [
            ['ZTEGC3FA7280', 'ZTE', date('Y-m-d')],
            ['HWTC882910AA', 'Huawei', date('Y-m-d')],
            ['FHTT99182301', 'Fiberhome', date('Y-m-d')],
        ];
        $sheet->fromArray($samples, null, 'A2');

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
        $sheet->getStyle('A1:C1')->applyFromArray($headerStyle);
        $sheet->getRowDimension(1)->setRowHeight(25);

        // Pastikan kolom serial_number terbaca sebagai text
        $sheet->getStyle('A2:A100')->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);

        foreach (range('A', 'C') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            $writer->save('php://output');
        }, 'template_ont_masuk.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    /**
     * Hapus 1 unit ONT dari inventaris.
     */
    public function destroy(OntMasuk $ontMasuk)
    {
        $sn = $ontMasuk->serial_number;
        $ontMasuk->delete();

        return redirect()->route('ont-masuk.index')
            ->with('success', "Unit ONT dengan SN {$sn} berhasil dihapus.");
    }
}

