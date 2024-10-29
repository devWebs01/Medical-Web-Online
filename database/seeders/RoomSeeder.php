<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rooms = [
            [
                'room_number' => '1',
                'price' => '50000',
            ],
            [
                'room_number' => '2',
                'price' => '50000',
            ],
        ];

        foreach ($rooms as $room) {
            DB::table('Rooms')->insert(array_merge($room, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
