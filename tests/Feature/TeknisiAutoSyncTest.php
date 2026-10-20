<?php

namespace Tests\Feature;

use App\Models\OntKeluar;
use App\Models\OntMasuk;
use App\Models\ReportingWo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TeknisiAutoSyncTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(User::factory()->create([
            'email' => 'admin@sim-ont.local',
        ]));
    }

    public function test_sync_teknisi_names_with_ont_keluar_updates_mismatched_names(): void
    {
        // 1. Buat unit di ONT Masuk
        OntMasuk::create([
            'serial_number' => 'ZTE-AUTO-101',
            'brand' => 'ZTE',
            'tanggal_masuk' => '2026-09-01',
        ]);
        OntMasuk::create([
            'serial_number' => 'ZTE-AUTO-102',
            'brand' => 'ZTE',
            'tanggal_masuk' => '2026-09-01',
        ]);

        // 2. Catat penyerahan di ONT Keluar dengan nama panggilan "zidan"
        OntKeluar::create([
            'serial_number' => 'ZTE-AUTO-101',
            'nama_teknisi' => 'zidan',
            'tanggal_keluar' => '2026-09-05',
        ]);
        OntKeluar::create([
            'serial_number' => 'ZTE-AUTO-102',
            'nama_teknisi' => 'zidan',
            'tanggal_keluar' => '2026-09-05',
        ]);

        // 3. Masukkan data Reporting WO dengan nama resmi "zaidan saputra" untuk SN ZTE-AUTO-101
        ReportingWo::create([
            'no_order' => 'WO-TEST-999',
            'serial_number' => 'ZTE-AUTO-101',
            'nama_teknisi' => 'zaidan saputra',
            'status_wo' => 'Work Order Selesai',
            'tanggal_sa' => '2026-09-10',
            'cek_match' => 'SESUAI',
        ]);

        // Panggil helper sinkronisasi
        $syncedCount = ReportingWo::syncTeknisiNamesWithOntKeluar();

        $this->assertGreaterThanOrEqual(1, $syncedCount);

        // Pastikan semua record ont_keluars milik "zidan" otomatis diselaraskan menjadi "zaidan saputra"
        $this->assertDatabaseMissing('ont_keluars', [
            'nama_teknisi' => 'zidan',
        ]);
        $this->assertEquals(2, OntKeluar::where('nama_teknisi', 'zaidan saputra')->count());
    }

    public function test_dashboard_recap_does_not_duplicate_technician_name(): void
    {
        // Unit masuk
        OntMasuk::create(['serial_number' => 'ZTE-DASH-1', 'brand' => 'ZTE', 'tanggal_masuk' => '2026-09-01']);
        OntMasuk::create(['serial_number' => 'ZTE-DASH-2', 'brand' => 'ZTE', 'tanggal_masuk' => '2026-09-01']);

        // Keluar dicatat "zidan"
        OntKeluar::create(['serial_number' => 'ZTE-DASH-1', 'nama_teknisi' => 'zidan', 'tanggal_keluar' => '2026-09-02']);
        OntKeluar::create(['serial_number' => 'ZTE-DASH-2', 'nama_teknisi' => 'zidan', 'tanggal_keluar' => '2026-09-02']);

        // WO dicatat "zaidan saputra"
        ReportingWo::create([
            'no_order' => 'WO-DASH-01',
            'serial_number' => 'ZTE-DASH-1',
            'nama_teknisi' => 'zaidan saputra',
            'status_wo' => 'Work Order Selesai',
            'tanggal_sa' => '2026-09-05',
            'cek_match' => 'SESUAI',
        ]);

        // Kunjungi Dashboard
        $response = $this->get(route('dashboard'));
        $response->assertStatus(200);

        // Ambil data rekap dari view
        $rekapTeknisi = $response->viewData('rekapTeknisi');

        // Pastikan hanya ada 1 teknisi (tidak doubling menjadi zidan dan zaidan saputra)
        $this->assertCount(1, $rekapTeknisi);
        $this->assertEquals('zaidan saputra', $rekapTeknisi[0]['nama']);
        $this->assertEquals(2, $rekapTeknisi[0]['total_dibawa']);
        $this->assertEquals(1, $rekapTeknisi[0]['installed']);
        $this->assertEquals(1, $rekapTeknisi[0]['not_installed']);
    }

    public function test_ont_keluar_standardizes_technician_name_casing(): void
    {
        // Unit masuk
        OntMasuk::create(['serial_number' => 'ZTE-STD-1', 'brand' => 'ZTE', 'tanggal_masuk' => '2026-09-01']);

        // Master Reporting WO punya nama resmi dengan Title Case "Zaidan Saputra"
        ReportingWo::create([
            'no_order' => 'WO-STD-01',
            'serial_number' => 'ZTE-STD-1',
            'nama_teknisi' => 'Zaidan Saputra',
            'status_wo' => 'Work Order Selesai',
            'tanggal_sa' => '2026-09-02',
            'cek_match' => 'SESUAI',
        ]);

        // Unit baru masuk
        OntMasuk::create(['serial_number' => 'ZTE-STD-2', 'brand' => 'ZTE', 'tanggal_masuk' => '2026-09-03']);

        // Input penyerahan dengan huruf kecil atau spasi berlebih "zaidan   saputra"
        $response = $this->post(route('ont-keluar.store'), [
            'nama_teknisi' => 'zaidan   saputra',
            'tanggal_keluar' => '2026-09-03',
            'serial_number' => 'ZTE-STD-2',
        ]);

        $response->assertRedirect(route('ont-keluar.index'));

        // Di database ont_keluars harus distandarisasi ke "Zaidan Saputra"
        $this->assertDatabaseHas('ont_keluars', [
            'serial_number' => 'ZTE-STD-2',
            'nama_teknisi' => 'Zaidan Saputra',
        ]);
    }

    public function test_ont_keluar_index_provides_unique_official_technician_list(): void
    {
        // Tambahkan variasi nama dengan huruf besar-kecil berbeda
        OntMasuk::create(['serial_number' => 'SN-LIST-1', 'brand' => 'ZTE', 'tanggal_masuk' => '2026-09-01']);
        OntMasuk::create(['serial_number' => 'SN-LIST-2', 'brand' => 'ZTE', 'tanggal_masuk' => '2026-09-01']);

        OntKeluar::create(['serial_number' => 'SN-LIST-1', 'nama_teknisi' => 'Zaidan Saputra', 'tanggal_keluar' => '2026-09-02']);
        ReportingWo::create([
            'no_order' => 'WO-LIST-1',
            'serial_number' => 'SN-LIST-2',
            'nama_teknisi' => 'zaidan saputra',
            'status_wo' => 'Work Order Selesai',
        ]);

        $response = $this->get(route('ont-keluar.index'));
        $response->assertStatus(200);

        $daftarTeknisi = $response->viewData('daftarTeknisi');

        // Pastikan hanya muncul 1 entri "Zaidan Saputra" (tidak ganda karena case)
        $this->assertCount(1, $daftarTeknisi);
    }
}
