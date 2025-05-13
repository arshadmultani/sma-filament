<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Chemist;
use App\Models\Headquarter;

class ChemistSeeder extends Seeder
{
    public function run(): void
    {
        $headquarterIds = Headquarter::pluck('id')->toArray();

        for ($i = 0; $i < 100; $i++) {
            Chemist::create([
                'name' => fake()->name(),
                'phone' => fake()->phoneNumber(),
                'email' => fake()->unique()->safeEmail(),
                'address' => fake()->address(),
                'headquarter_id' => fake()->randomElement($headquarterIds),
            ]);
        }
    }
} 