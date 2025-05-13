<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Region;

class RegionSeeder extends Seeder
{
    public function run(): void
    {
        $regions = [
            'Maharashtra',
            'Karnataka',
            'Tamil Nadu',
            'Uttar Pradesh',
            'West Bengal',
            'Gujarat',
            'Rajasthan',
            'Madhya Pradesh',
            'Andhra Pradesh',
            'Telangana',
            'Bihar',
            'Punjab',
            'Odisha',
            'Kerala',
            'Assam',
            'Jharkhand',
            'Chhattisgarh',
            'Haryana',
            'Delhi',
            'Jammu and Kashmir',
            'Uttarakhand',
            'Himachal Pradesh',
            'Tripura',
            'Meghalaya',
            'Manipur',
            'Nagaland',
            'Goa',
            'Arunachal Pradesh',
        ];
        foreach ($regions as $region) {
            Region::firstOrCreate(['name' => $region]);
        }
    }
} 