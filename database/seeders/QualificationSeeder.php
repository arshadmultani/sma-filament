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
            ['name' => 'BAMS', 'category' => 'Doctor'],
            ['name' => 'BUMS', 'category' => 'Doctor'],
            ['name' => 'BHMS', 'category' => 'Doctor'],
            ['name' => 'MD Ayurveda', 'category' => 'Doctor'],
            ['name' => 'MD Physician', 'category' => 'Doctor'],
            ['name' => 'MBBS DCH', 'category' => 'Doctor'],
            ['name' => 'Other', 'category' => 'Doctor'],
        ];

        foreach ($qualifications as $qualification) {
            Qualification::firstOrCreate([
                'name' => $qualification['name'],
                'category' => $qualification['category'],
            ]);
        }
    }
}
