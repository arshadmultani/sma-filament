<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Area;
use App\Models\Headquarter;

class HeadquarterSeeder extends Seeder
{
    public function run(): void
    {
        $headquartersByArea = [
            'Mumbai' => ['Churchgate', 'Dadar', 'Vasai', 'Andheri', 'Borivali'],
            'Pune' => ['Shivajinagar', 'Kothrud', 'Hadapsar', 'Wakad', 'Koregaon Park'],
            'Nagpur' => ['Sitabuldi', 'Dharampeth', 'Sadar', 'Mahal', 'Civil Lines'],
            'Nashik' => ['Panchavati', 'CIDCO', 'Indira Nagar', 'Satpur', 'Ambad'],
            'Aurangabad' => ['CIDCO', 'Nirala Bazar', 'Osmanpura', 'Garkheda', 'Jalna Road'],
            'Bengaluru' => ['MG Road', 'Whitefield', 'Indiranagar', 'Jayanagar', 'Koramangala'],
            'Mysuru' => ['Lakshmipuram', 'Vijayanagar', 'Saraswathipuram', 'Jayalakshmipuram', 'Hebbal'],
            // Add more areas and headquarters as needed
        ];

        foreach ($headquartersByArea as $areaName => $headquarters) {
            $area = Area::where('name', $areaName)->first();
            if ($area) {
                foreach ($headquarters as $hq) {
                    Headquarter::firstOrCreate([
                        'name' => $hq,
                        'area_id' => $area->id,
                    ]);
                }
            }
        }
    }
} 