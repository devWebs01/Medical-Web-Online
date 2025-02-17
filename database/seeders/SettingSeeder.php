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
            'name' => 'Klinik Dokter Eva Elvita Syofyan',
            'telp' => '6287885044122',
            'whatsApp' => '6287885044122',
            'address' => 'Lorem ipsum dolor sit amet consectetur adipiscing elit luctus nec habitasse',
        ]);
    }
}
