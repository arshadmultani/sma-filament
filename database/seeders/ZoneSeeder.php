<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Zone;

class ZoneSeeder extends Seeder
{
    public function run(): void
    {
        $zones = ['North', 'South', 'East', 'West'];
        foreach ($zones as $zone) {
            Zone::firstOrCreate(['name' => $zone]);
        }
    }
} 