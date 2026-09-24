<?php

namespace Tests\Feature;

use App\Models\OntKeluar;
use App\Models\OntMasuk;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OntKeluarMultiItemTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_store_multiple_ont_keluar_for_one_technician(): void
    {
        $user = User::factory()->create();

        // Create warehouse stock
        OntMasuk::create(['serial_number' => 'ZTEGC111111', 'brand' => 'ZTE', 'tanggal_masuk' => now()]);
        OntMasuk::create(['serial_number' => 'HWTC222222', 'brand' => 'Huawei', 'tanggal_masuk' => now()]);
        OntMasuk::create(['serial_number' => 'FHTT333333', 'brand' => 'Fiberhome', 'tanggal_masuk' => now()]);

        $response = $this->actingAs($user)->post(route('ont-keluar.store'), [
            'nama_teknisi' => 'Teknisi Ahmad',
            'tanggal_keluar' => now()->format('Y-m-d'),
            'serial_number' => "ZTEGC111111\nHWTC222222, FHTT333333",
        ]);

        $response->assertRedirect(route('ont-keluar.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('ont_keluars', [
            'serial_number' => 'ZTEGC111111',
            'nama_teknisi' => 'Teknisi Ahmad',
        ]);
        $this->assertDatabaseHas('ont_keluars', [
            'serial_number' => 'HWTC222222',
            'nama_teknisi' => 'Teknisi Ahmad',
        ]);
        $this->assertDatabaseHas('ont_keluars', [
            'serial_number' => 'FHTT333333',
            'nama_teknisi' => 'Teknisi Ahmad',
        ]);

        $this->assertEquals(3, OntKeluar::where('nama_teknisi', 'Teknisi Ahmad')->count());
    }

    public function test_fails_when_any_sn_is_not_in_warehouse(): void
    {
        $user = User::factory()->create();

        OntMasuk::create(['serial_number' => 'ZTEGC111111', 'brand' => 'ZTE', 'tanggal_masuk' => now()]);

        $response = $this->actingAs($user)->post(route('ont-keluar.store'), [
            'nama_teknisi' => 'Teknisi Budi',
            'tanggal_keluar' => now()->format('Y-m-d'),
            'serial_number' => "ZTEGC111111\nSN_UNKNOWN_999",
        ]);

        $response->assertSessionHasErrors('serial_number');
        $this->assertEquals(0, OntKeluar::count());
    }

    public function test_fails_when_any_sn_was_already_issued(): void
    {
        $user = User::factory()->create();

        OntMasuk::create(['serial_number' => 'ZTEGC111111', 'brand' => 'ZTE', 'tanggal_masuk' => now()]);
        OntMasuk::create(['serial_number' => 'HWTC222222', 'brand' => 'Huawei', 'tanggal_masuk' => now()]);

        OntKeluar::create([
            'serial_number' => 'ZTEGC111111',
            'nama_teknisi' => 'Teknisi Lama',
            'tanggal_keluar' => now(),
        ]);

        $response = $this->actingAs($user)->post(route('ont-keluar.store'), [
            'nama_teknisi' => 'Teknisi Candra',
            'tanggal_keluar' => now()->format('Y-m-d'),
            'serial_number' => "ZTEGC111111\nHWTC222222",
        ]);

        $response->assertSessionHasErrors('serial_number');
        $this->assertEquals(1, OntKeluar::count()); // Only the pre-existing one
    }
}
