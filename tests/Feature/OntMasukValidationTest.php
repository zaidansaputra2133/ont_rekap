<?php

namespace Tests\Feature;

use App\Models\OntMasuk;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OntMasukValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_rejects_random_text_serial_number_on_store(): void
    {
        $user = \App\Models\User::factory()->create();

        $response = $this->actingAs($user)->post(route('ont-masuk.store'), [
            'serial_number' => 'sifjiasjfi',
            'tanggal_masuk' => now()->toDateString(),
        ]);

        $response->assertSessionHasErrors('serial_number');
        $this->assertDatabaseMissing('ont_masuks', [
            'serial_number' => 'SIFJIASJFI',
        ]);
    }

    public function test_accepts_valid_prefixes_with_any_subsequent_characters(): void
    {
        $user = \App\Models\User::factory()->create();

        $validSns = [
            'ZTE12345678',
            'zte-custom-number-99',
            'FHTT112233',
            'fhtt-bebas-angka-456',
            'ALCL556677',
            'HWTC889900',
            '48575443AABB',
        ];

        foreach ($validSns as $sn) {
            $response = $this->actingAs($user)->post(route('ont-masuk.store'), [
                'serial_number' => $sn,
                'tanggal_masuk' => now()->toDateString(),
            ]);

            $response->assertSessionHasNoErrors();
            $this->assertDatabaseHas('ont_masuks', [
                'serial_number' => strtoupper(trim($sn)),
            ]);
        }
    }

    public function test_rejects_random_text_on_update(): void
    {
        $user = \App\Models\User::factory()->create();

        $ont = OntMasuk::create([
            'serial_number' => 'ZTEORIGINAL01',
            'brand' => 'ZTE',
            'tanggal_masuk' => now()->toDateString(),
        ]);

        $response = $this->actingAs($user)->put(route('ont-masuk.update', $ont), [
            'serial_number' => 'sifjiasjfi',
            'tanggal_masuk' => now()->toDateString(),
        ]);

        $response->assertSessionHasErrors('serial_number');
        $this->assertDatabaseHas('ont_masuks', [
            'id' => $ont->id,
            'serial_number' => 'ZTEORIGINAL01',
        ]);
    }

    public function test_import_skips_invalid_prefix_rows(): void
    {
        $import = new \App\Imports\OntMasukImport();
        $validRow = ['serial_number' => 'ZTEVALID999', 'brand' => 'ZTE', 'tanggal_masuk' => '2026-10-21'];
        $invalidRow = ['serial_number' => 'sifjiasjfi', 'brand' => '', 'tanggal_masuk' => '2026-10-21'];

        $result1 = $import->model($validRow);
        $result2 = $import->model($invalidRow);

        $this->assertNotNull($result1);
        $this->assertNull($result2);
        $this->assertEquals(1, $import->importedCount());
        $this->assertEquals(1, $import->skippedCount());
    }
}

