<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Region;
use App\Models\Area;

class AreaSeeder extends Seeder
{
    public function run(): void
    {
        $areasByRegion = [
            'Maharashtra' => [
                'Mumbai', 'Pune', 'Nagpur', 'Nashik', 'Aurangabad', 'Solapur', 'Amravati', 'Kolhapur', 'Sangli', 'Jalgaon',
            ],
            'Karnataka' => [
                'Bengaluru', 'Mysuru', 'Mangaluru', 'Hubballi', 'Belagavi', 'Davanagere', 'Ballari', 'Tumakuru', 'Shivamogga', 'Raichur',
            ],
            // Add more regions and areas as needed
        ];

        foreach ($areasByRegion as $regionName => $areas) {
            $region = Region::where('name', $regionName)->first();
            if ($region) {
                foreach ($areas as $area) {
                    Area::firstOrCreate([
                        'name' => $area,
                        'region_id' => $region->id,
                    ]);
                }
            }
        }
    }
} 