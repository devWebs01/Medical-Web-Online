<?php

namespace Database\Seeders;

use App\Models\AdditionalFees;
use Illuminate\Database\Seeder;

class AdditionalFeesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $fees = [
            ['name' => 'Administrasi', 'cost' => 50000],
            ['name' => 'Konsultasi Dokter', 'cost' => 30000],
            ['name' => 'Perawatan', 'cost' => 20000],
            ['name' => 'Perawatan Khusus', 'cost' => 30000],
            ['name' => 'Nutrisi', 'cost' => 45000],
        ];

        foreach ($fees as $fee) {
            AdditionalFees::create($fee);
        }
    }
}
