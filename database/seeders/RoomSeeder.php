<?php

namespace Database\Seeders;

use App\Models\Room;
use Illuminate\Database\Seeder;

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
            Room::create([
                'room_number' => $room['room_number'],
                'price' => $room['price'],
            ]);
        }
    }
}
