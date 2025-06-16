<?php

namespace Database\Seeders;

use App\Models\Zone;
use Illuminate\Database\Seeder;

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
