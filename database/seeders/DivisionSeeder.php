<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DivisionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Division::create([
            'name' => 'Pharma',
        ]);

        \App\Models\Division::create([
            'name' => 'Phytonova',
        ]);
    }
}
