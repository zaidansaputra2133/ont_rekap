<?php

namespace Database\Seeders;

use App\Models\OntKeluar;
use App\Models\OntMasuk;
use App\Models\ReportingWo;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * OntDummySeeder
 *
 * Menyemai data dummy realistis yang saling terhubung:
 *   1. ont_masuks   → 50 unit ONT masuk gudang (4 merek)
 *   2. ont_keluars  → 40 unit diserahkan ke 5 teknisi (2 rusak)
 *   3. reporting_wos→ 38 laporan WO (35 selesai, 3 diproses, 3 cek_match Beda)
 */
class OntDummySeeder extends Seeder
{
    public function run(): void
    {
        // ----------------------------------------------------------------
        // 0. Bersihkan data lama (disable FK checks agar truncate aman)
        // ----------------------------------------------------------------
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        ReportingWo::truncate();
        OntKeluar::truncate();
        OntMasuk::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // ----------------------------------------------------------------
        // 1. ONT MASUK — 50 unit dari 4 merek, masuk Sep–Okt 2026
        // ----------------------------------------------------------------
        OntMasuk::insert($this->ontMasukRows());

        // ----------------------------------------------------------------
        // 2. ONT KELUAR — 40 unit diserahkan ke 5 teknisi
        // ----------------------------------------------------------------
        OntKeluar::insert($this->ontKeluarRows());

        // ----------------------------------------------------------------
        // 3. REPORTING WO — 38 laporan WO dari 5 teknisi
        // ----------------------------------------------------------------
        ReportingWo::insert($this->reportingWoRows());

        $this->command->info('✅ OntDummySeeder selesai:');
        $this->command->info('   • ONT Masuk   : '.OntMasuk::count().' unit');
        $this->command->info('   • ONT Keluar  : '.OntKeluar::count().' transaksi');
        $this->command->info('   • Reporting WO: '.ReportingWo::count().' laporan');
    }

    // ====================================================================
    // 50 Unit ONT Masuk Gudang (15 ZTE, 15 Fiberhome, 10 Nokia, 10 Huawei)
    // ====================================================================
    private function ontMasukRows(): array
    {
        $now = now()->toDateTimeString();

        return [
            // --- ZTE (15 unit) ---
            ['serial_number' => 'ZTEGC3FA7280', 'brand' => 'ZTE',       'tanggal_masuk' => '2026-09-01', 'created_at' => $now, 'updated_at' => $now],
            ['serial_number' => 'ZTEGC3FA7281', 'brand' => 'ZTE',       'tanggal_masuk' => '2026-09-01', 'created_at' => $now, 'updated_at' => $now],
            ['serial_number' => 'ZTEGC3FA7282', 'brand' => 'ZTE',       'tanggal_masuk' => '2026-09-02', 'created_at' => $now, 'updated_at' => $now],
            ['serial_number' => 'ZTEGC3FA7283', 'brand' => 'ZTE',       'tanggal_masuk' => '2026-09-02', 'created_at' => $now, 'updated_at' => $now],
            ['serial_number' => 'ZTEGC3FA7284', 'brand' => 'ZTE',       'tanggal_masuk' => '2026-09-05', 'created_at' => $now, 'updated_at' => $now],
            ['serial_number' => 'ZTEGC3FA7285', 'brand' => 'ZTE',       'tanggal_masuk' => '2026-09-05', 'created_at' => $now, 'updated_at' => $now],
            ['serial_number' => 'ZTEGC3FA7286', 'brand' => 'ZTE',       'tanggal_masuk' => '2026-09-08', 'created_at' => $now, 'updated_at' => $now],
            ['serial_number' => 'ZTEGC3FA7287', 'brand' => 'ZTE',       'tanggal_masuk' => '2026-09-08', 'created_at' => $now, 'updated_at' => $now],
            ['serial_number' => 'ZTEGC3FA7288', 'brand' => 'ZTE',       'tanggal_masuk' => '2026-09-10', 'created_at' => $now, 'updated_at' => $now],
            ['serial_number' => 'ZTEGC3FA7289', 'brand' => 'ZTE',       'tanggal_masuk' => '2026-09-10', 'created_at' => $now, 'updated_at' => $now],
            ['serial_number' => 'ZTEGC3FA7290', 'brand' => 'ZTE',       'tanggal_masuk' => '2026-09-15', 'created_at' => $now, 'updated_at' => $now],
            ['serial_number' => 'ZTEGC3FA7291', 'brand' => 'ZTE',       'tanggal_masuk' => '2026-09-15', 'created_at' => $now, 'updated_at' => $now],
            ['serial_number' => 'ZTEGC3FA7292', 'brand' => 'ZTE',       'tanggal_masuk' => '2026-09-20', 'created_at' => $now, 'updated_at' => $now],
            ['serial_number' => 'ZTEGC3FA7293', 'brand' => 'ZTE',       'tanggal_masuk' => '2026-09-22', 'created_at' => $now, 'updated_at' => $now],
            ['serial_number' => 'ZTEGC3FA7294', 'brand' => 'ZTE',       'tanggal_masuk' => '2026-09-25', 'created_at' => $now, 'updated_at' => $now],

            // --- Fiberhome (15 unit) ---
            ['serial_number' => 'FHTTDF3A1100', 'brand' => 'Fiberhome', 'tanggal_masuk' => '2026-09-03', 'created_at' => $now, 'updated_at' => $now],
            ['serial_number' => 'FHTTDF3A1101', 'brand' => 'Fiberhome', 'tanggal_masuk' => '2026-09-03', 'created_at' => $now, 'updated_at' => $now],
            ['serial_number' => 'FHTTDF3A1102', 'brand' => 'Fiberhome', 'tanggal_masuk' => '2026-09-06', 'created_at' => $now, 'updated_at' => $now],
            ['serial_number' => 'FHTTDF3A1103', 'brand' => 'Fiberhome', 'tanggal_masuk' => '2026-09-06', 'created_at' => $now, 'updated_at' => $now],
            ['serial_number' => 'FHTTDF3A1104', 'brand' => 'Fiberhome', 'tanggal_masuk' => '2026-09-09', 'created_at' => $now, 'updated_at' => $now],
            ['serial_number' => 'FHTTDF3A1105', 'brand' => 'Fiberhome', 'tanggal_masuk' => '2026-09-09', 'created_at' => $now, 'updated_at' => $now],
            ['serial_number' => 'FHTTDF3A1106', 'brand' => 'Fiberhome', 'tanggal_masuk' => '2026-09-12', 'created_at' => $now, 'updated_at' => $now],
            ['serial_number' => 'FHTTDF3A1107', 'brand' => 'Fiberhome', 'tanggal_masuk' => '2026-09-12', 'created_at' => $now, 'updated_at' => $now],
            ['serial_number' => 'FHTTDF3A1108', 'brand' => 'Fiberhome', 'tanggal_masuk' => '2026-09-16', 'created_at' => $now, 'updated_at' => $now],
            ['serial_number' => 'FHTTDF3A1109', 'brand' => 'Fiberhome', 'tanggal_masuk' => '2026-09-16', 'created_at' => $now, 'updated_at' => $now],
            ['serial_number' => 'FHTTDF3A1110', 'brand' => 'Fiberhome', 'tanggal_masuk' => '2026-09-18', 'created_at' => $now, 'updated_at' => $now],
            ['serial_number' => 'FHTTDF3A1111', 'brand' => 'Fiberhome', 'tanggal_masuk' => '2026-09-18', 'created_at' => $now, 'updated_at' => $now],
            ['serial_number' => 'FHTTDF3A1112', 'brand' => 'Fiberhome', 'tanggal_masuk' => '2026-09-23', 'created_at' => $now, 'updated_at' => $now],
            ['serial_number' => 'FHTTDF3A1113', 'brand' => 'Fiberhome', 'tanggal_masuk' => '2026-09-26', 'created_at' => $now, 'updated_at' => $now],
            ['serial_number' => 'FHTTDF3A1114', 'brand' => 'Fiberhome', 'tanggal_masuk' => '2026-09-29', 'created_at' => $now, 'updated_at' => $now],

            // --- Nokia (10 unit) ---
            ['serial_number' => 'ALCLAB0C2201', 'brand' => 'Nokia',     'tanggal_masuk' => '2026-09-04', 'created_at' => $now, 'updated_at' => $now],
            ['serial_number' => 'ALCLAB0C2202', 'brand' => 'Nokia',     'tanggal_masuk' => '2026-09-04', 'created_at' => $now, 'updated_at' => $now],
            ['serial_number' => 'ALCLAB0C2203', 'brand' => 'Nokia',     'tanggal_masuk' => '2026-09-11', 'created_at' => $now, 'updated_at' => $now],
            ['serial_number' => 'ALCLAB0C2204', 'brand' => 'Nokia',     'tanggal_masuk' => '2026-09-11', 'created_at' => $now, 'updated_at' => $now],
            ['serial_number' => 'ALCLAB0C2205', 'brand' => 'Nokia',     'tanggal_masuk' => '2026-09-17', 'created_at' => $now, 'updated_at' => $now],
            ['serial_number' => 'ALCLAB0C2206', 'brand' => 'Nokia',     'tanggal_masuk' => '2026-09-17', 'created_at' => $now, 'updated_at' => $now],
            ['serial_number' => 'ALCLAB0C2207', 'brand' => 'Nokia',     'tanggal_masuk' => '2026-09-24', 'created_at' => $now, 'updated_at' => $now],
            ['serial_number' => 'ALCLAB0C2208', 'brand' => 'Nokia',     'tanggal_masuk' => '2026-09-24', 'created_at' => $now, 'updated_at' => $now],
            ['serial_number' => 'ALCLAB0C2209', 'brand' => 'Nokia',     'tanggal_masuk' => '2026-09-30', 'created_at' => $now, 'updated_at' => $now],
            ['serial_number' => 'ALCLAB0C2210', 'brand' => 'Nokia',     'tanggal_masuk' => '2026-09-30', 'created_at' => $now, 'updated_at' => $now],

            // --- Huawei (10 unit) ---
            ['serial_number' => 'HWTC48FA0011', 'brand' => 'Huawei',   'tanggal_masuk' => '2026-10-01', 'created_at' => $now, 'updated_at' => $now],
            ['serial_number' => 'HWTC48FA0012', 'brand' => 'Huawei',   'tanggal_masuk' => '2026-10-01', 'created_at' => $now, 'updated_at' => $now],
            ['serial_number' => 'HWTC48FA0013', 'brand' => 'Huawei',   'tanggal_masuk' => '2026-10-03', 'created_at' => $now, 'updated_at' => $now],
            ['serial_number' => 'HWTC48FA0014', 'brand' => 'Huawei',   'tanggal_masuk' => '2026-10-03', 'created_at' => $now, 'updated_at' => $now],
            ['serial_number' => 'HWTC48FA0015', 'brand' => 'Huawei',   'tanggal_masuk' => '2026-10-06', 'created_at' => $now, 'updated_at' => $now],
            ['serial_number' => 'HWTC48FA0016', 'brand' => 'Huawei',   'tanggal_masuk' => '2026-10-06', 'created_at' => $now, 'updated_at' => $now],
            ['serial_number' => 'HWTC48FA0017', 'brand' => 'Huawei',   'tanggal_masuk' => '2026-10-08', 'created_at' => $now, 'updated_at' => $now],
            ['serial_number' => 'HWTC48FA0018', 'brand' => 'Huawei',   'tanggal_masuk' => '2026-10-08', 'created_at' => $now, 'updated_at' => $now],
            ['serial_number' => 'HWTC48FA0019', 'brand' => 'Huawei',   'tanggal_masuk' => '2026-10-10', 'created_at' => $now, 'updated_at' => $now],
            ['serial_number' => 'HWTC48FA0020', 'brand' => 'Huawei',   'tanggal_masuk' => '2026-10-10', 'created_at' => $now, 'updated_at' => $now],
        ];
    }

    // ====================================================================
    // 40 Unit ONT Keluar ke 5 Teknisi
    // 10 unit sisa di gudang: ZTEGC3FA7292-94, FHTTDF3A1112-14, HWTC48FA0017-20
    // ====================================================================
    private function ontKeluarRows(): array
    {
        $now = now()->toDateTimeString();

        return [
            // ---- Budi Santoso (8 unit, 1 rusak) ----
            ['serial_number' => 'ZTEGC3FA7280', 'nama_teknisi' => 'Budi Santoso',    'tanggal_keluar' => '2026-09-02', 'keterangan' => null,    'catatan' => null,                               'created_at' => $now, 'updated_at' => $now],
            ['serial_number' => 'ZTEGC3FA7281', 'nama_teknisi' => 'Budi Santoso',    'tanggal_keluar' => '2026-09-02', 'keterangan' => null,    'catatan' => null,                               'created_at' => $now, 'updated_at' => $now],
            ['serial_number' => 'FHTTDF3A1100', 'nama_teknisi' => 'Budi Santoso',    'tanggal_keluar' => '2026-09-04', 'keterangan' => null,    'catatan' => null,                               'created_at' => $now, 'updated_at' => $now],
            ['serial_number' => 'FHTTDF3A1101', 'nama_teknisi' => 'Budi Santoso',    'tanggal_keluar' => '2026-09-04', 'keterangan' => null,    'catatan' => null,                               'created_at' => $now, 'updated_at' => $now],
            ['serial_number' => 'ALCLAB0C2201', 'nama_teknisi' => 'Budi Santoso',    'tanggal_keluar' => '2026-09-05', 'keterangan' => null,    'catatan' => null,                               'created_at' => $now, 'updated_at' => $now],
            ['serial_number' => 'ALCLAB0C2202', 'nama_teknisi' => 'Budi Santoso',    'tanggal_keluar' => '2026-09-05', 'keterangan' => null,    'catatan' => null,                               'created_at' => $now, 'updated_at' => $now],
            ['serial_number' => 'ZTEGC3FA7282', 'nama_teknisi' => 'Budi Santoso',    'tanggal_keluar' => '2026-09-10', 'keterangan' => 'Rusak', 'catatan' => 'Port LAN rusak tidak terdeteksi',  'created_at' => $now, 'updated_at' => $now],
            ['serial_number' => 'ZTEGC3FA7283', 'nama_teknisi' => 'Budi Santoso',    'tanggal_keluar' => '2026-09-10', 'keterangan' => null,    'catatan' => null,                               'created_at' => $now, 'updated_at' => $now],

            // ---- Agus Prasetyo (8 unit, 1 rusak) ----
            ['serial_number' => 'ZTEGC3FA7284', 'nama_teknisi' => 'Agus Prasetyo',   'tanggal_keluar' => '2026-09-06', 'keterangan' => null,    'catatan' => null,                               'created_at' => $now, 'updated_at' => $now],
            ['serial_number' => 'ZTEGC3FA7285', 'nama_teknisi' => 'Agus Prasetyo',   'tanggal_keluar' => '2026-09-06', 'keterangan' => null,    'catatan' => null,                               'created_at' => $now, 'updated_at' => $now],
            ['serial_number' => 'FHTTDF3A1102', 'nama_teknisi' => 'Agus Prasetyo',   'tanggal_keluar' => '2026-09-07', 'keterangan' => null,    'catatan' => null,                               'created_at' => $now, 'updated_at' => $now],
            ['serial_number' => 'FHTTDF3A1103', 'nama_teknisi' => 'Agus Prasetyo',   'tanggal_keluar' => '2026-09-07', 'keterangan' => null,    'catatan' => null,                               'created_at' => $now, 'updated_at' => $now],
            ['serial_number' => 'FHTTDF3A1104', 'nama_teknisi' => 'Agus Prasetyo',   'tanggal_keluar' => '2026-09-11', 'keterangan' => null,    'catatan' => null,                               'created_at' => $now, 'updated_at' => $now],
            ['serial_number' => 'FHTTDF3A1105', 'nama_teknisi' => 'Agus Prasetyo',   'tanggal_keluar' => '2026-09-11', 'keterangan' => null,    'catatan' => null,                               'created_at' => $now, 'updated_at' => $now],
            ['serial_number' => 'ALCLAB0C2203', 'nama_teknisi' => 'Agus Prasetyo',   'tanggal_keluar' => '2026-09-13', 'keterangan' => null,    'catatan' => null,                               'created_at' => $now, 'updated_at' => $now],
            ['serial_number' => 'ALCLAB0C2204', 'nama_teknisi' => 'Agus Prasetyo',   'tanggal_keluar' => '2026-09-13', 'keterangan' => 'Rusak', 'catatan' => 'Indikator mati sejak diterima',    'created_at' => $now, 'updated_at' => $now],

            // ---- Rizky Firmansyah (8 unit) ----
            ['serial_number' => 'ZTEGC3FA7286', 'nama_teknisi' => 'Rizky Firmansyah', 'tanggal_keluar' => '2026-09-09', 'keterangan' => null,    'catatan' => null,                               'created_at' => $now, 'updated_at' => $now],
            ['serial_number' => 'ZTEGC3FA7287', 'nama_teknisi' => 'Rizky Firmansyah', 'tanggal_keluar' => '2026-09-09', 'keterangan' => null,    'catatan' => null,                               'created_at' => $now, 'updated_at' => $now],
            ['serial_number' => 'FHTTDF3A1106', 'nama_teknisi' => 'Rizky Firmansyah', 'tanggal_keluar' => '2026-09-13', 'keterangan' => null,    'catatan' => null,                               'created_at' => $now, 'updated_at' => $now],
            ['serial_number' => 'FHTTDF3A1107', 'nama_teknisi' => 'Rizky Firmansyah', 'tanggal_keluar' => '2026-09-13', 'keterangan' => null,    'catatan' => null,                               'created_at' => $now, 'updated_at' => $now],
            ['serial_number' => 'ALCLAB0C2205', 'nama_teknisi' => 'Rizky Firmansyah', 'tanggal_keluar' => '2026-09-18', 'keterangan' => null,    'catatan' => null,                               'created_at' => $now, 'updated_at' => $now],
            ['serial_number' => 'ALCLAB0C2206', 'nama_teknisi' => 'Rizky Firmansyah', 'tanggal_keluar' => '2026-09-18', 'keterangan' => null,    'catatan' => null,                               'created_at' => $now, 'updated_at' => $now],
            ['serial_number' => 'HWTC48FA0011', 'nama_teknisi' => 'Rizky Firmansyah', 'tanggal_keluar' => '2026-10-02', 'keterangan' => null,    'catatan' => null,                               'created_at' => $now, 'updated_at' => $now],
            ['serial_number' => 'HWTC48FA0012', 'nama_teknisi' => 'Rizky Firmansyah', 'tanggal_keluar' => '2026-10-02', 'keterangan' => null,    'catatan' => null,                               'created_at' => $now, 'updated_at' => $now],

            // ---- Deni Kurniawan (8 unit) ----
            ['serial_number' => 'ZTEGC3FA7288', 'nama_teknisi' => 'Deni Kurniawan',  'tanggal_keluar' => '2026-09-11', 'keterangan' => null,    'catatan' => null,                               'created_at' => $now, 'updated_at' => $now],
            ['serial_number' => 'ZTEGC3FA7289', 'nama_teknisi' => 'Deni Kurniawan',  'tanggal_keluar' => '2026-09-11', 'keterangan' => null,    'catatan' => null,                               'created_at' => $now, 'updated_at' => $now],
            ['serial_number' => 'FHTTDF3A1108', 'nama_teknisi' => 'Deni Kurniawan',  'tanggal_keluar' => '2026-09-17', 'keterangan' => null,    'catatan' => null,                               'created_at' => $now, 'updated_at' => $now],
            ['serial_number' => 'FHTTDF3A1109', 'nama_teknisi' => 'Deni Kurniawan',  'tanggal_keluar' => '2026-09-17', 'keterangan' => null,    'catatan' => null,                               'created_at' => $now, 'updated_at' => $now],
            ['serial_number' => 'ALCLAB0C2207', 'nama_teknisi' => 'Deni Kurniawan',  'tanggal_keluar' => '2026-09-25', 'keterangan' => null,    'catatan' => null,                               'created_at' => $now, 'updated_at' => $now],
            ['serial_number' => 'ALCLAB0C2208', 'nama_teknisi' => 'Deni Kurniawan',  'tanggal_keluar' => '2026-09-25', 'keterangan' => null,    'catatan' => null,                               'created_at' => $now, 'updated_at' => $now],
            ['serial_number' => 'HWTC48FA0013', 'nama_teknisi' => 'Deni Kurniawan',  'tanggal_keluar' => '2026-10-04', 'keterangan' => null,    'catatan' => null,                               'created_at' => $now, 'updated_at' => $now],
            ['serial_number' => 'HWTC48FA0014', 'nama_teknisi' => 'Deni Kurniawan',  'tanggal_keluar' => '2026-10-04', 'keterangan' => null,    'catatan' => null,                               'created_at' => $now, 'updated_at' => $now],

            // ---- Fajar Hidayat (8 unit) ----
            ['serial_number' => 'ZTEGC3FA7290', 'nama_teknisi' => 'Fajar Hidayat',   'tanggal_keluar' => '2026-09-16', 'keterangan' => null,    'catatan' => null,                               'created_at' => $now, 'updated_at' => $now],
            ['serial_number' => 'ZTEGC3FA7291', 'nama_teknisi' => 'Fajar Hidayat',   'tanggal_keluar' => '2026-09-16', 'keterangan' => null,    'catatan' => null,                               'created_at' => $now, 'updated_at' => $now],
            ['serial_number' => 'FHTTDF3A1110', 'nama_teknisi' => 'Fajar Hidayat',   'tanggal_keluar' => '2026-09-19', 'keterangan' => null,    'catatan' => null,                               'created_at' => $now, 'updated_at' => $now],
            ['serial_number' => 'FHTTDF3A1111', 'nama_teknisi' => 'Fajar Hidayat',   'tanggal_keluar' => '2026-09-19', 'keterangan' => null,    'catatan' => null,                               'created_at' => $now, 'updated_at' => $now],
            ['serial_number' => 'ALCLAB0C2209', 'nama_teknisi' => 'Fajar Hidayat',   'tanggal_keluar' => '2026-10-01', 'keterangan' => null,    'catatan' => null,                               'created_at' => $now, 'updated_at' => $now],
            ['serial_number' => 'ALCLAB0C2210', 'nama_teknisi' => 'Fajar Hidayat',   'tanggal_keluar' => '2026-10-01', 'keterangan' => null,    'catatan' => null,                               'created_at' => $now, 'updated_at' => $now],
            ['serial_number' => 'HWTC48FA0015', 'nama_teknisi' => 'Fajar Hidayat',   'tanggal_keluar' => '2026-10-07', 'keterangan' => null,    'catatan' => null,                               'created_at' => $now, 'updated_at' => $now],
            ['serial_number' => 'HWTC48FA0016', 'nama_teknisi' => 'Fajar Hidayat',   'tanggal_keluar' => '2026-10-07', 'keterangan' => null,    'catatan' => null,                               'created_at' => $now, 'updated_at' => $now],
        ];
    }

    // ====================================================================
    // 38 Laporan Work Order
    //
    // Logika cek_match:
    //   SESUAI → nama_teknisi WO == nama_teknisi ont_keluars (unit sama)
    //   Beda   → unit dibawa teknisi A tapi dipasang teknisi B di lapangan
    //   null   → WO belum selesai / unit rusak (tidak bisa dikonfirmasi)
    //
    // Ringkasan: 35 WO Selesai, 3 WO Diproses, 3 cek_match Beda
    // Unit rusak (ZTEGC3FA7282, ALCLAB0C2204) tidak muncul di WO
    // ====================================================================
    private function reportingWoRows(): array
    {
        $now = now()->toDateTimeString();

        return [
            // ============================================================
            // Budi Santoso — 7 WO Selesai
            // ZTEGC3FA7282 rusak → tidak ada WO
            // WO38: ZTEGC3FA7292 (masuk 20 Sep, keluar langsung ke Budi)
            // ============================================================
            ['no_order' => 'WO20264384001', 'cid' => 'CID-JAT-100001', 'serial_number' => 'ZTEGC3FA7280', 'nama_teknisi' => 'Budi Santoso',    'nik_teknisi' => '6201012', 'status_wo' => 'Work Order Selesai',  'tanggal_sa' => '2026-09-03', 'vendor' => 'PT Sinergi Mitra', 'sektor' => 'JAT-01', 'cek_match' => 'SESUAI', 'created_at' => $now, 'updated_at' => $now],
            ['no_order' => 'WO20264384002', 'cid' => 'CID-JAT-100002', 'serial_number' => 'ZTEGC3FA7281', 'nama_teknisi' => 'Budi Santoso',    'nik_teknisi' => '6201012', 'status_wo' => 'Work Order Selesai',  'tanggal_sa' => '2026-09-03', 'vendor' => 'PT Sinergi Mitra', 'sektor' => 'JAT-01', 'cek_match' => 'SESUAI', 'created_at' => $now, 'updated_at' => $now],
            ['no_order' => 'WO20264384003', 'cid' => 'CID-JAT-100003', 'serial_number' => 'FHTTDF3A1100', 'nama_teknisi' => 'Budi Santoso',    'nik_teknisi' => '6201012', 'status_wo' => 'Work Order Selesai',  'tanggal_sa' => '2026-09-05', 'vendor' => 'PT Sinergi Mitra', 'sektor' => 'JAT-01', 'cek_match' => 'SESUAI', 'created_at' => $now, 'updated_at' => $now],
            ['no_order' => 'WO20264384004', 'cid' => 'CID-JAT-100004', 'serial_number' => 'FHTTDF3A1101', 'nama_teknisi' => 'Budi Santoso',    'nik_teknisi' => '6201012', 'status_wo' => 'Work Order Selesai',  'tanggal_sa' => '2026-09-06', 'vendor' => 'PT Sinergi Mitra', 'sektor' => 'JAT-01', 'cek_match' => 'SESUAI', 'created_at' => $now, 'updated_at' => $now],
            ['no_order' => 'WO20264384005', 'cid' => 'CID-JAT-100005', 'serial_number' => 'ALCLAB0C2201', 'nama_teknisi' => 'Budi Santoso',    'nik_teknisi' => '6201012', 'status_wo' => 'Work Order Selesai',  'tanggal_sa' => '2026-09-07', 'vendor' => 'PT Sinergi Mitra', 'sektor' => 'JAT-02', 'cek_match' => 'SESUAI', 'created_at' => $now, 'updated_at' => $now],
            ['no_order' => 'WO20264384006', 'cid' => 'CID-JAT-100006', 'serial_number' => 'ZTEGC3FA7283', 'nama_teknisi' => 'Budi Santoso',    'nik_teknisi' => '6201012', 'status_wo' => 'Work Order Selesai',  'tanggal_sa' => '2026-09-12', 'vendor' => 'PT Sinergi Mitra', 'sektor' => 'JAT-02', 'cek_match' => 'SESUAI', 'created_at' => $now, 'updated_at' => $now],
            ['no_order' => 'WO20264384038', 'cid' => 'CID-JAT-100038', 'serial_number' => 'ZTEGC3FA7292', 'nama_teknisi' => 'Budi Santoso',    'nik_teknisi' => '6201012', 'status_wo' => 'Work Order Selesai',  'tanggal_sa' => '2026-09-21', 'vendor' => 'PT Sinergi Mitra', 'sektor' => 'JAT-01', 'cek_match' => 'SESUAI', 'created_at' => $now, 'updated_at' => $now],

            // ============================================================
            // Agus Prasetyo — 6 WO Selesai
            // ALCLAB0C2204 rusak → tidak ada WO
            // ALCLAB0C2203 dibawa Agus, dipasang Budi → cek_match Beda
            // ============================================================
            ['no_order' => 'WO20264384007', 'cid' => 'CID-JAT-100007', 'serial_number' => 'ZTEGC3FA7284', 'nama_teknisi' => 'Agus Prasetyo',   'nik_teknisi' => '6201023', 'status_wo' => 'Work Order Selesai',  'tanggal_sa' => '2026-09-08', 'vendor' => 'PT Sinergi Mitra', 'sektor' => 'JAT-02', 'cek_match' => 'SESUAI', 'created_at' => $now, 'updated_at' => $now],
            ['no_order' => 'WO20264384008', 'cid' => 'CID-JAT-100008', 'serial_number' => 'ZTEGC3FA7285', 'nama_teknisi' => 'Agus Prasetyo',   'nik_teknisi' => '6201023', 'status_wo' => 'Work Order Selesai',  'tanggal_sa' => '2026-09-08', 'vendor' => 'PT Sinergi Mitra', 'sektor' => 'JAT-02', 'cek_match' => 'SESUAI', 'created_at' => $now, 'updated_at' => $now],
            ['no_order' => 'WO20264384009', 'cid' => 'CID-JAT-100009', 'serial_number' => 'FHTTDF3A1102', 'nama_teknisi' => 'Agus Prasetyo',   'nik_teknisi' => '6201023', 'status_wo' => 'Work Order Selesai',  'tanggal_sa' => '2026-09-09', 'vendor' => 'PT Sinergi Mitra', 'sektor' => 'JAT-03', 'cek_match' => 'SESUAI', 'created_at' => $now, 'updated_at' => $now],
            ['no_order' => 'WO20264384010', 'cid' => 'CID-JAT-100010', 'serial_number' => 'FHTTDF3A1103', 'nama_teknisi' => 'Agus Prasetyo',   'nik_teknisi' => '6201023', 'status_wo' => 'Work Order Selesai',  'tanggal_sa' => '2026-09-09', 'vendor' => 'PT Sinergi Mitra', 'sektor' => 'JAT-03', 'cek_match' => 'SESUAI', 'created_at' => $now, 'updated_at' => $now],
            ['no_order' => 'WO20264384011', 'cid' => 'CID-JAT-100011', 'serial_number' => 'FHTTDF3A1104', 'nama_teknisi' => 'Agus Prasetyo',   'nik_teknisi' => '6201023', 'status_wo' => 'Work Order Selesai',  'tanggal_sa' => '2026-09-12', 'vendor' => 'PT Sinergi Mitra', 'sektor' => 'JAT-03', 'cek_match' => 'SESUAI', 'created_at' => $now, 'updated_at' => $now],
            ['no_order' => 'WO20264384037', 'cid' => 'CID-JAT-100037', 'serial_number' => 'FHTTDF3A1105', 'nama_teknisi' => 'Agus Prasetyo',   'nik_teknisi' => '6201023', 'status_wo' => 'Work Order Selesai',  'tanggal_sa' => '2026-09-13', 'vendor' => 'PT Sinergi Mitra', 'sektor' => 'JAT-03', 'cek_match' => 'SESUAI', 'created_at' => $now, 'updated_at' => $now],
            // ALCLAB0C2203 dibawa Agus, tapi dipasang Budi di lokasi lain
            ['no_order' => 'WO20264384012', 'cid' => 'CID-JAT-100012', 'serial_number' => 'ALCLAB0C2203', 'nama_teknisi' => 'Budi Santoso',    'nik_teknisi' => '6201012', 'status_wo' => 'Work Order Selesai',  'tanggal_sa' => '2026-09-15', 'vendor' => 'PT Sinergi Mitra', 'sektor' => 'JAT-03', 'cek_match' => 'Beda',   'created_at' => $now, 'updated_at' => $now],

            // ============================================================
            // Rizky Firmansyah — 6 WO Selesai + 1 WO Diproses
            // FHTTDF3A1106 dibawa Rizky, dipasang Deni → cek_match Beda
            // ============================================================
            ['no_order' => 'WO20264384013', 'cid' => 'CID-JAT-100013', 'serial_number' => 'ZTEGC3FA7286', 'nama_teknisi' => 'Rizky Firmansyah', 'nik_teknisi' => '6201034', 'status_wo' => 'Work Order Selesai',  'tanggal_sa' => '2026-09-10', 'vendor' => 'PT Sinergi Mitra', 'sektor' => 'JAT-04', 'cek_match' => 'SESUAI', 'created_at' => $now, 'updated_at' => $now],
            ['no_order' => 'WO20264384014', 'cid' => 'CID-JAT-100014', 'serial_number' => 'ZTEGC3FA7287', 'nama_teknisi' => 'Rizky Firmansyah', 'nik_teknisi' => '6201034', 'status_wo' => 'Work Order Selesai',  'tanggal_sa' => '2026-09-11', 'vendor' => 'PT Sinergi Mitra', 'sektor' => 'JAT-04', 'cek_match' => 'SESUAI', 'created_at' => $now, 'updated_at' => $now],
            // FHTTDF3A1106 dibawa Rizky, dipasang Deni
            ['no_order' => 'WO20264384015', 'cid' => 'CID-JAT-100015', 'serial_number' => 'FHTTDF3A1106', 'nama_teknisi' => 'Deni Kurniawan',  'nik_teknisi' => '6201045', 'status_wo' => 'Work Order Selesai',  'tanggal_sa' => '2026-09-15', 'vendor' => 'PT Sinergi Mitra', 'sektor' => 'JAT-04', 'cek_match' => 'Beda',   'created_at' => $now, 'updated_at' => $now],
            ['no_order' => 'WO20264384016', 'cid' => 'CID-JAT-100016', 'serial_number' => 'FHTTDF3A1107', 'nama_teknisi' => 'Rizky Firmansyah', 'nik_teknisi' => '6201034', 'status_wo' => 'Work Order Selesai',  'tanggal_sa' => '2026-09-16', 'vendor' => 'PT Sinergi Mitra', 'sektor' => 'JAT-04', 'cek_match' => 'SESUAI', 'created_at' => $now, 'updated_at' => $now],
            ['no_order' => 'WO20264384017', 'cid' => 'CID-JAT-100017', 'serial_number' => 'ALCLAB0C2205', 'nama_teknisi' => 'Rizky Firmansyah', 'nik_teknisi' => '6201034', 'status_wo' => 'Work Order Selesai',  'tanggal_sa' => '2026-09-20', 'vendor' => 'PT Sinergi Mitra', 'sektor' => 'JAT-05', 'cek_match' => 'SESUAI', 'created_at' => $now, 'updated_at' => $now],
            ['no_order' => 'WO20264384018', 'cid' => 'CID-JAT-100018', 'serial_number' => 'ALCLAB0C2206', 'nama_teknisi' => 'Rizky Firmansyah', 'nik_teknisi' => '6201034', 'status_wo' => 'Work Order Selesai',  'tanggal_sa' => '2026-09-21', 'vendor' => 'PT Sinergi Mitra', 'sektor' => 'JAT-05', 'cek_match' => 'SESUAI', 'created_at' => $now, 'updated_at' => $now],
            ['no_order' => 'WO20264384019', 'cid' => 'CID-JAT-100019', 'serial_number' => 'HWTC48FA0011', 'nama_teknisi' => 'Rizky Firmansyah', 'nik_teknisi' => '6201034', 'status_wo' => 'Work Order Selesai',  'tanggal_sa' => '2026-10-03', 'vendor' => 'PT Sinergi Mitra', 'sektor' => 'JAT-05', 'cek_match' => 'SESUAI', 'created_at' => $now, 'updated_at' => $now],
            // WO masih diproses
            ['no_order' => 'WO20264384020', 'cid' => 'CID-JAT-100020', 'serial_number' => 'HWTC48FA0012', 'nama_teknisi' => 'Rizky Firmansyah', 'nik_teknisi' => '6201034', 'status_wo' => 'Work Order Diproses', 'tanggal_sa' => null,         'vendor' => 'PT Sinergi Mitra', 'sektor' => 'JAT-05', 'cek_match' => null,    'created_at' => $now, 'updated_at' => $now],

            // ============================================================
            // Deni Kurniawan — 6 WO Selesai + 1 WO Diproses
            // FHTTDF3A1108 dibawa Deni, dipasang Fajar → cek_match Beda
            // ============================================================
            ['no_order' => 'WO20264384021', 'cid' => 'CID-JAT-100021', 'serial_number' => 'ZTEGC3FA7288', 'nama_teknisi' => 'Deni Kurniawan',  'nik_teknisi' => '6201045', 'status_wo' => 'Work Order Selesai',  'tanggal_sa' => '2026-09-13', 'vendor' => 'PT Mitra Akses', 'sektor' => 'JAT-06', 'cek_match' => 'SESUAI', 'created_at' => $now, 'updated_at' => $now],
            ['no_order' => 'WO20264384022', 'cid' => 'CID-JAT-100022', 'serial_number' => 'ZTEGC3FA7289', 'nama_teknisi' => 'Deni Kurniawan',  'nik_teknisi' => '6201045', 'status_wo' => 'Work Order Selesai',  'tanggal_sa' => '2026-09-13', 'vendor' => 'PT Mitra Akses', 'sektor' => 'JAT-06', 'cek_match' => 'SESUAI', 'created_at' => $now, 'updated_at' => $now],
            // FHTTDF3A1108 dibawa Deni, dipasang Fajar di lokasi lain
            ['no_order' => 'WO20264384023', 'cid' => 'CID-JAT-100023', 'serial_number' => 'FHTTDF3A1108', 'nama_teknisi' => 'Fajar Hidayat',   'nik_teknisi' => '6201056', 'status_wo' => 'Work Order Selesai',  'tanggal_sa' => '2026-09-20', 'vendor' => 'PT Mitra Akses', 'sektor' => 'JAT-06', 'cek_match' => 'Beda',   'created_at' => $now, 'updated_at' => $now],
            ['no_order' => 'WO20264384024', 'cid' => 'CID-JAT-100024', 'serial_number' => 'FHTTDF3A1109', 'nama_teknisi' => 'Deni Kurniawan',  'nik_teknisi' => '6201045', 'status_wo' => 'Work Order Selesai',  'tanggal_sa' => '2026-09-19', 'vendor' => 'PT Mitra Akses', 'sektor' => 'JAT-06', 'cek_match' => 'SESUAI', 'created_at' => $now, 'updated_at' => $now],
            ['no_order' => 'WO20264384025', 'cid' => 'CID-JAT-100025', 'serial_number' => 'ALCLAB0C2207', 'nama_teknisi' => 'Deni Kurniawan',  'nik_teknisi' => '6201045', 'status_wo' => 'Work Order Selesai',  'tanggal_sa' => '2026-09-27', 'vendor' => 'PT Mitra Akses', 'sektor' => 'JAT-07', 'cek_match' => 'SESUAI', 'created_at' => $now, 'updated_at' => $now],
            ['no_order' => 'WO20264384026', 'cid' => 'CID-JAT-100026', 'serial_number' => 'ALCLAB0C2208', 'nama_teknisi' => 'Deni Kurniawan',  'nik_teknisi' => '6201045', 'status_wo' => 'Work Order Selesai',  'tanggal_sa' => '2026-09-27', 'vendor' => 'PT Mitra Akses', 'sektor' => 'JAT-07', 'cek_match' => 'SESUAI', 'created_at' => $now, 'updated_at' => $now],
            ['no_order' => 'WO20264384027', 'cid' => 'CID-JAT-100027', 'serial_number' => 'HWTC48FA0013', 'nama_teknisi' => 'Deni Kurniawan',  'nik_teknisi' => '6201045', 'status_wo' => 'Work Order Selesai',  'tanggal_sa' => '2026-10-05', 'vendor' => 'PT Mitra Akses', 'sektor' => 'JAT-07', 'cek_match' => 'SESUAI', 'created_at' => $now, 'updated_at' => $now],
            // WO masih diproses
            ['no_order' => 'WO20264384028', 'cid' => 'CID-JAT-100028', 'serial_number' => 'HWTC48FA0014', 'nama_teknisi' => 'Deni Kurniawan',  'nik_teknisi' => '6201045', 'status_wo' => 'Work Order Diproses', 'tanggal_sa' => null,         'vendor' => 'PT Mitra Akses', 'sektor' => 'JAT-07', 'cek_match' => null,    'created_at' => $now, 'updated_at' => $now],

            // ============================================================
            // Fajar Hidayat — 7 WO Selesai + 1 WO Diproses
            // ============================================================
            ['no_order' => 'WO20264384029', 'cid' => 'CID-JAT-100029', 'serial_number' => 'ZTEGC3FA7290', 'nama_teknisi' => 'Fajar Hidayat',   'nik_teknisi' => '6201056', 'status_wo' => 'Work Order Selesai',  'tanggal_sa' => '2026-09-17', 'vendor' => 'PT Mitra Akses', 'sektor' => 'JAT-08', 'cek_match' => 'SESUAI', 'created_at' => $now, 'updated_at' => $now],
            ['no_order' => 'WO20264384030', 'cid' => 'CID-JAT-100030', 'serial_number' => 'ZTEGC3FA7291', 'nama_teknisi' => 'Fajar Hidayat',   'nik_teknisi' => '6201056', 'status_wo' => 'Work Order Selesai',  'tanggal_sa' => '2026-09-17', 'vendor' => 'PT Mitra Akses', 'sektor' => 'JAT-08', 'cek_match' => 'SESUAI', 'created_at' => $now, 'updated_at' => $now],
            ['no_order' => 'WO20264384031', 'cid' => 'CID-JAT-100031', 'serial_number' => 'FHTTDF3A1110', 'nama_teknisi' => 'Fajar Hidayat',   'nik_teknisi' => '6201056', 'status_wo' => 'Work Order Selesai',  'tanggal_sa' => '2026-09-20', 'vendor' => 'PT Mitra Akses', 'sektor' => 'JAT-08', 'cek_match' => 'SESUAI', 'created_at' => $now, 'updated_at' => $now],
            ['no_order' => 'WO20264384032', 'cid' => 'CID-JAT-100032', 'serial_number' => 'FHTTDF3A1111', 'nama_teknisi' => 'Fajar Hidayat',   'nik_teknisi' => '6201056', 'status_wo' => 'Work Order Selesai',  'tanggal_sa' => '2026-09-21', 'vendor' => 'PT Mitra Akses', 'sektor' => 'JAT-08', 'cek_match' => 'SESUAI', 'created_at' => $now, 'updated_at' => $now],
            ['no_order' => 'WO20264384033', 'cid' => 'CID-JAT-100033', 'serial_number' => 'ALCLAB0C2209', 'nama_teknisi' => 'Fajar Hidayat',   'nik_teknisi' => '6201056', 'status_wo' => 'Work Order Selesai',  'tanggal_sa' => '2026-10-02', 'vendor' => 'PT Mitra Akses', 'sektor' => 'JAT-09', 'cek_match' => 'SESUAI', 'created_at' => $now, 'updated_at' => $now],
            ['no_order' => 'WO20264384034', 'cid' => 'CID-JAT-100034', 'serial_number' => 'ALCLAB0C2210', 'nama_teknisi' => 'Fajar Hidayat',   'nik_teknisi' => '6201056', 'status_wo' => 'Work Order Selesai',  'tanggal_sa' => '2026-10-02', 'vendor' => 'PT Mitra Akses', 'sektor' => 'JAT-09', 'cek_match' => 'SESUAI', 'created_at' => $now, 'updated_at' => $now],
            ['no_order' => 'WO20264384035', 'cid' => 'CID-JAT-100035', 'serial_number' => 'HWTC48FA0015', 'nama_teknisi' => 'Fajar Hidayat',   'nik_teknisi' => '6201056', 'status_wo' => 'Work Order Selesai',  'tanggal_sa' => '2026-10-08', 'vendor' => 'PT Mitra Akses', 'sektor' => 'JAT-09', 'cek_match' => 'SESUAI', 'created_at' => $now, 'updated_at' => $now],
            // WO masih diproses
            ['no_order' => 'WO20264384036', 'cid' => 'CID-JAT-100036', 'serial_number' => 'HWTC48FA0016', 'nama_teknisi' => 'Fajar Hidayat',   'nik_teknisi' => '6201056', 'status_wo' => 'Work Order Diproses', 'tanggal_sa' => null,         'vendor' => 'PT Mitra Akses', 'sektor' => 'JAT-09', 'cek_match' => null,    'created_at' => $now, 'updated_at' => $now],
        ];
    }
}
