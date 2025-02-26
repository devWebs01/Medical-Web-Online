<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Patient>
 */
class PatientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'identity' => fake()->numerify('##########0000'),
            'name' => fake()->name(),
            'gender' => fake()->randomElement(['male', 'female']),
            'dob' => fake()->date(),
            'address' => fake()->streetAddress(),
            'phone' => fake()->phoneNumber(),
        ];
    }
}
