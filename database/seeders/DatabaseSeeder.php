<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\AdditionalFees;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        \App\Models\Patient::factory(10)->create();

        $this->call([
            SettingSeeder::class,
            UserSeeder::class,
            RoomSeeder::class,
            MedicationSeeder::class,
            AdditionalFees::class,
        ]);
    }
}
