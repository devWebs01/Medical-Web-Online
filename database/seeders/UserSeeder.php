<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory(20)->create();

        $users = [
            [
                'name' => 'User Admin',
                'email' => 'testingbae66@gmail.com',
                'role' => 'admin',
            ],
            [
                'name' => 'User doctor',
                'email' => 'doctor@testing.com',
                'role' => 'doctor',
            ],
            [
                'name' => 'User owner',
                'email' => 'owner@testing.com',
                'role' => 'owner',
            ],
        ];

        foreach ($users as $user) {
            User::create([
                'name' => $user['name'],
                'email' => $user['email'],
                'password' => Hash::make('password'),
                'role' => $user['role'],
            ]);
        }
    }
}
