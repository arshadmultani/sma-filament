<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Specialty;

class SpecialtySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $specialties = [
            ['name' => 'Gynecologist'],
            ['name' => 'Pediatrician'],
            ['name' => 'Dermatologist'],
            ['name' => 'Orthopedic'],
            ['name' => 'Urologist'],
            ['name' => 'Dentist'],
            ['name' => 'GP Non-MBBS'],
            ['name' => 'GP MBBS'],
            
        ];

        foreach ($specialties as $specialty) {
            Specialty::firstOrCreate([
                'name' => $specialty['name'],
            ]);
        }
    }
}
