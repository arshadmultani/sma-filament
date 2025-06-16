<?php

namespace Database\Seeders;

use App\Models\Chemist;
use App\Models\Headquarter;
use Illuminate\Database\Seeder;

class ChemistSeeder extends Seeder
{
    public function run(): void
    {
        $headquarterIds = Headquarter::pluck('id')->toArray();
        $types = ['Ayurvedic', 'Allopathic'];
        for ($i = 0; $i < 100; $i++) {
            Chemist::create([
                'name' => fake()->name(),
                'phone' => fake()->phoneNumber(),
                'email' => fake()->unique()->safeEmail(),
                'address' => fake()->address(),
                'town' => fake()->city(),
                'headquarter_id' => fake()->randomElement($headquarterIds),
                'type' => fake()->randomElement($types),
                'user_id' => fake()->randomElement(\App\Models\User::pluck('id')->toArray()),
            ]);
        }
    }
}
