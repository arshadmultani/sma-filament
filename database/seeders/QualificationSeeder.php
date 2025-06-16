<?php

namespace Database\Seeders;

use App\Models\Qualification;
use Illuminate\Database\Seeder;

class QualificationSeeder extends Seeder
{
    public function run(): void
    {
        $qualifications = [
            ['name' => 'MBBS', 'category' => 'Doctor'],
            ['name' => 'MD', 'category' => 'Doctor'],
            ['name' => 'BDS', 'category' => 'Doctor'],
            ['name' => 'PhD', 'category' => 'User'],
            ['name' => 'MSc', 'category' => 'User'],
            ['name' => 'BSc', 'category' => 'User'],
        ];

        foreach ($qualifications as $qualification) {
            Qualification::firstOrCreate([
                'name' => $qualification['name'],
                'category' => $qualification['category'],
            ]);
        }
    }
}
