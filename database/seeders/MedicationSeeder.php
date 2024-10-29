<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MedicationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $medications = [
            ['name' => 'Paracetamol', 'dosage' => '500 mg', 'price' => '2000', 'category' => 'Analgesik'],
            ['name' => 'Amoxicillin', 'dosage' => '250 mg', 'price' => '1500', 'category' => 'Antibiotik'],
            ['name' => 'Ibuprofen', 'dosage' => '400 mg', 'price' => '2500', 'category' => 'Anti-inflamasi'],
            ['name' => 'Cetirizine', 'dosage' => '10 mg', 'price' => '3000', 'category' => 'Antihistamin'],
            ['name' => 'Metformin', 'dosage' => '500 mg', 'price' => '5000', 'category' => 'Antidiabetik'],
            ['name' => 'Aspirin', 'dosage' => '300 mg', 'price' => '1800', 'category' => 'Analgesik'],
            ['name' => 'Ciprofloxacin', 'dosage' => '500 mg', 'price' => '4000', 'category' => 'Antibiotik'],
            ['name' => 'Naproxen', 'dosage' => '250 mg', 'price' => '2200', 'category' => 'Anti-inflamasi'],
            ['name' => 'Loratadine', 'dosage' => '10 mg', 'price' => '3500', 'category' => 'Antihistamin'],
            ['name' => 'Simvastatin', 'dosage' => '20 mg', 'price' => '4500', 'category' => 'Antihiperlipidemik'],
            ['name' => 'Abocate', 'dosage' => '25 mg', 'price' => '25000', 'category' => 'Obat'],
            ['name' => 'Infus set', 'dosage' => null, 'price' => '25000', 'category' => 'Peralatan Medis'],
            ['name' => 'Cairan Infus', 'dosage' => null, 'price' => '35000', 'category' => 'Obat'],
            ['name' => 'Neurotropic', 'dosage' => null, 'price' => '40000', 'category' => 'Obat'],
            ['name' => 'Ondansetron', 'dosage' => null, 'price' => '35000', 'category' => 'Obat'],
            ['name' => 'Ranitidin', 'dosage' => null, 'price' => '25000', 'category' => 'Obat'],
            ['name' => 'Gentamisin', 'dosage' => null, 'price' => '30000', 'category' => 'Obat'],
            ['name' => 'Cefotaxim/Ceftriaxon', 'dosage' => null, 'price' => '40000', 'category' => 'Antibiotik'],
            ['name' => 'Dexametason', 'dosage' => null, 'price' => '25000', 'category' => 'Obat'],
            ['name' => 'Pronalges', 'dosage' => null, 'price' => '30000', 'category' => 'Obat'],
            ['name' => 'Obat Makan', 'dosage' => null, 'price' => '20000', 'category' => 'Nutrisi'],
            ['name' => 'Oksigen', 'dosage' => null, 'price' => '30000', 'category' => 'Gas Medis'],
            // Tambahkan 40 data lainnya di sini
            // Contoh data tambahan
            ['name' => 'Omeprazole', 'dosage' => '20 mg', 'price' => '3000', 'category' => 'Antasida'],
            ['name' => 'Levothyroxine', 'dosage' => '50 mcg', 'price' => '5000', 'category' => 'Hormon'],
            // ... (isi dengan data asli lainnya)
        ];

        foreach ($medications as $medication) {
            DB::table('medications')->insert(array_merge($medication, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
