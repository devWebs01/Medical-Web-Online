<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Setting::create([
            'name' => 'ABC',
            'telp' => '08978301766',
            'whatsApp' => '08978301766',
            'address' => 'Lorem ipsum dolor sit amet consectetur adipiscing elit luctus nec habitasse,',
        ]);
    }
}
