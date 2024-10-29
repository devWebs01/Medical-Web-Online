<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Medication>
 */
class MedicationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->word(),
            'dosage' => $this->faker->randomElement([100, 250, 500, 1000]) . 'mg',  // example in mg
            // 'unit' => $this->faker->randomElement(['tablet', 'capsule', 'ml', 'mg']),
            'price' => $this->faker->randomFloat(2, 5, 1000000),  // example in currency
            'category' => $this->faker->randomElement(['Antibiotic', 'Painkiller', 'Vitamin', 'Antiseptic']),
        ];
    }
}
