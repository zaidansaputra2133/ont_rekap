<?php

namespace Database\Seeders;

use App\Models\OntMasuk;
use Illuminate\Database\Seeder;

class OntMasukDummySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $samples = [
            // ZTE (10 Unit)
            ['serial_number' => 'ZTEGC3FA7280', 'brand' => 'ZTE'],
            ['serial_number' => 'ZTEGC3FA7281', 'brand' => 'ZTE'],
            ['serial_number' => 'ZTEGC3FA7282', 'brand' => 'ZTE'],
            ['serial_number' => 'ZTEGC3FA7283', 'brand' => 'ZTE'],
            ['serial_number' => 'ZTEGC3FA7284', 'brand' => 'ZTE'],
            ['serial_number' => 'ZTEGC3FA7285', 'brand' => 'ZTE'],
            ['serial_number' => 'ZTEGC3FA7286', 'brand' => 'ZTE'],
            ['serial_number' => 'ZTEGC3FA7287', 'brand' => 'ZTE'],
            ['serial_number' => 'ZTEGC3FA7288', 'brand' => 'ZTE'],
            ['serial_number' => 'ZTEGC3FA7289', 'brand' => 'ZTE'],

            // Huawei (10 Unit)
            ['serial_number' => 'HWTC882910AA', 'brand' => 'Huawei'],
            ['serial_number' => 'HWTC882910AB', 'brand' => 'Huawei'],
            ['serial_number' => 'HWTC882910AC', 'brand' => 'Huawei'],
            ['serial_number' => 'HWTC882910AD', 'brand' => 'Huawei'],
            ['serial_number' => 'HWTC882910AE', 'brand' => 'Huawei'],
            ['serial_number' => 'HWTC882910AF', 'brand' => 'Huawei'],
            ['serial_number' => 'HWTC882910AG', 'brand' => 'Huawei'],
            ['serial_number' => 'HWTC882910AH', 'brand' => 'Huawei'],
            ['serial_number' => 'HWTC882910AI', 'brand' => 'Huawei'],
            ['serial_number' => 'HWTC882910AJ', 'brand' => 'Huawei'],

            // Fiberhome (8 Unit)
            ['serial_number' => 'FHTT99182301', 'brand' => 'Fiberhome'],
            ['serial_number' => 'FHTT99182302', 'brand' => 'Fiberhome'],
            ['serial_number' => 'FHTT99182303', 'brand' => 'Fiberhome'],
            ['serial_number' => 'FHTT99182304', 'brand' => 'Fiberhome'],
            ['serial_number' => 'FHTT99182305', 'brand' => 'Fiberhome'],
            ['serial_number' => 'FHTT99182306', 'brand' => 'Fiberhome'],
            ['serial_number' => 'FHTT99182307', 'brand' => 'Fiberhome'],
            ['serial_number' => 'FHTT99182308', 'brand' => 'Fiberhome'],

            // Nokia (5 Unit)
            ['serial_number' => 'ALCL98765431', 'brand' => 'Nokia'],
            ['serial_number' => 'ALCL98765432', 'brand' => 'Nokia'],
            ['serial_number' => 'ALCL98765433', 'brand' => 'Nokia'],
            ['serial_number' => 'ALCL98765434', 'brand' => 'Nokia'],
            ['serial_number' => 'ALCL98765435', 'brand' => 'Nokia'],
        ];

        foreach ($samples as $item) {
            OntMasuk::firstOrCreate(
                ['serial_number' => $item['serial_number']],
                [
                    'brand' => $item['brand'],
                    'tanggal_masuk' => now()->format('Y-m-d'),
                ]
            );
        }
    }
}
