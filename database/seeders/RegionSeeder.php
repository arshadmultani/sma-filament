<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Region;
use App\Models\Zone;

class RegionSeeder extends Seeder
{
    public function run(): void
    {
        $zones = Zone::pluck('id', 'name');
        $regionZoneMap = [
            'North' => [
                'Delhi', 'Uttar Pradesh', 'Punjab', 'Haryana', 'Himachal Pradesh', 'Uttarakhand', 'Jammu and Kashmir', 'Chhattisgarh', 'Jharkhand', 'Bihar', 'Assam', 'Tripura', 'Meghalaya', 'Manipur', 'Nagaland', 'Arunachal Pradesh',
            ],
            'South' => [
                'Karnataka', 'Tamil Nadu', 'Andhra Pradesh', 'Telangana', 'Kerala', 'Goa',
            ],
            'East' => [
                'West Bengal', 'Odisha',
            ],
            'West' => [
                'Maharashtra', 'Gujarat', 'Rajasthan', 'Madhya Pradesh',
            ],
        ];
        foreach ($regionZoneMap as $zoneName => $regions) {
            foreach ($regions as $region) {
                Region::firstOrCreate([
                    'name' => $region
                ], [
                    'zone_id' => $zones[$zoneName] ?? null
                ]);
            }
        }
    }
} 