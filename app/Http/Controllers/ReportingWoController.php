<?php

namespace App\Http\Controllers;

use App\Models\ReportingWo;
use App\Imports\ReportingWoImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;

class ReportingWoController extends Controller
{
    /**
     * Tampilkan antarmuka Menu Reporting WO + search, filter status & teknisi, pagination.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status');
        $teknisi = $request->input('teknisi');

        $items = ReportingWo::query()
            ->search($search)
            ->filterStatus($status)
            ->filterTeknisi($teknisi)
            ->orderBy('id', 'desc')
            ->paginate(15)
            ->withQueryString();

        $totalWo = ReportingWo::count();
        $totalInstalled = ReportingWo::where('status_wo', 'like', '%selesai%')->count();
        $totalNotInstalled = max(0, $totalWo - $totalInstalled);
        $totalMatch = ReportingWo::where('cek_match', 'SESUAI')->count();

        // Daftar teknisi untuk dropdown filter
        $daftarTeknisi = ReportingWo::select('nama_teknisi')
            ->whereNotNull('nama_teknisi')
            ->distinct()
            ->orderBy('nama_teknisi')
            ->pluck('nama_teknisi');

        return view('reporting-wo.index', compact(
            'items',
            'totalWo',
            'totalInstalled',
            'totalNotInstalled',
            'totalMatch',
            'daftarTeknisi',
            'search',
            'status',
            'teknisi'
        ));
    }

    /**
     * Handle proses upload & import file Excel Reporting WO
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:20480'],
        ], [
            'file.required' => 'Silakan pilih file Excel (.xlsx / .csv) laporan WO.',
            'file.mimes'    => 'Format file harus berupa .xlsx, .xls, atau .csv.',
            'file.max'      => 'Ukuran file maksimal adalah 20MB.',
        ]);

        $import = new ReportingWoImport();
        Excel::import($import, $request->file('file'));

        $imported = $import->importedCount();
        $updated = $import->updatedCount();
        $unregistered = $import->unregisteredSnCount();
        $skipped = $import->skippedCount();

        $parts = [];
        if ($imported > 0) {
            $parts[] = "{$imported} laporan baru berhasil ditambahkan";
        }
        if ($updated > 0) {
            $parts[] = "{$updated} laporan diperbarui";
        }
        if ($unregistered > 0) {
            $unregisteredSns = implode(', ', array_slice($import->unregisteredSns(), 0, 3));
            $more = count($import->unregisteredSns()) > 3 ? '...' : '';
            $parts[] = "{$unregistered} baris dilewati karena SN belum terdaftar di ONT Masuk ({$unregisteredSns}{$more})";
        }
        if ($skipped > 0) {
            $parts[] = "{$skipped} baris kosong/tidak valid dilewati";
        }

        $message = !empty($parts) ? 'Proses impor selesai: ' . implode('. ', $parts) . '.' : 'Tidak ada data yang berhasil diimpor.';

        $flashType = ($imported > 0 || $updated > 0) ? 'success' : ($unregistered > 0 ? 'error' : 'info');

        return redirect()->route('reporting-wo.index')
            ->with($flashType, $message);
    }

    /**
     * Download template file Excel untuk impor Reporting WO.
     */
    public function downloadTemplate()
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template Reporting WO');

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

        // Data sampel representatif menggunakan SN yang valid
        $samples = [
            [
                'WO20264384690',
                '12345678',
                'ZTEGC3FA7280',
                'Budi Santoso',
                '100234',
                'Work Order Selesai',
                date('Y-m-d'),
                'Telkom Akses PT',
                'SEK-01',
                'SESUAI',
            ],
            [
                'WO20264384691',
                '12345679',
                'HWTC882910AA',
                'Ahmad Kurniawan',
                '100235',
                'Work Order Selesai',
                date('Y-m-d'),
                'Telkom Akses PT',
                'SEK-02',
                'SESUAI',
            ],
            [
                'WO20264384692',
                '12345680',
                'FHTT99182301',
                'Doni Prasetyo',
                '100236',
                'Pending Pelanggan Tidak Ada di Tempat',
                null,
                'Telkom Akses PT',
                'SEK-01',
                'Beda',
            ],
        ];
        $sheet->fromArray($samples, null, 'A2');

        // Styling header elegan
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
        $sheet->getRowDimension(1)->setRowHeight(25);

        // Pastikan kolom kode/angka tidak diubah formatnya oleh Excel
        $sheet->getStyle('A2:E100')->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);

        foreach (range('A', 'J') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            $writer->save('php://output');
        }, 'template_reporting_wo.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    /**
     * Hapus satu data laporan WO
     */
    public function destroy(ReportingWo $reportingWo)
    {
        $noOrder = $reportingWo->no_order;
        $reportingWo->delete();

        return redirect()->route('reporting-wo.index')
            ->with('success', "Data laporan Work Order {$noOrder} berhasil dihapus.");
    }
}

