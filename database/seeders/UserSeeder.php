<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Region;
use App\Models\Area;
use App\Models\Headquarter;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Ensure roles exist
        $roles = [
            'RSM' => Role::firstOrCreate(['name' => 'RSM']),
            'ASM' => Role::firstOrCreate(['name' => 'ASM']),
            'DSA' => Role::firstOrCreate(['name' => 'DSA']),
        ];

        $regions = Region::take(5)->get();
        $asmCount = 0;
        $dsaCount = 0;

        foreach ($regions as $regionIndex => $region) {
            // Create RSM for this region
            $rsm = User::create([
                'name' => 'RSM User ' . ($regionIndex + 1),
                'email' => 'rsm' . ($regionIndex + 1) . '@example.com',
                'phone_number' => '90000000' . ($regionIndex + 1),
                'division_id' => 1, // adjust as needed
                'password' => Hash::make('password'),
                'location_type' => Region::class,
                'location_id' => $region->id,
            ]);
            $rsm->roles()->sync([$roles['RSM']->id]);

            // Get 5 areas for this region
            $areas = $region->areas()->take(5)->get();
            foreach ($areas as $areaIndex => $area) {
                $asmCount++;
                // Create ASM for this area
                $asm = User::create([
                    'name' => 'ASM User ' . $asmCount,
                    'email' => 'asm' . $asmCount . '@example.com',
                    'phone_number' => '91000000' . $asmCount,
                    'division_id' => 1, // adjust as needed
                    'password' => Hash::make('password'),
                    'location_type' => Area::class,
                    'location_id' => $area->id,
                ]);
                $asm->roles()->sync([$roles['ASM']->id]);

                // Get 3 headquarters for this area
                $headquarters = $area->headquarters()->take(3)->get();
                foreach ($headquarters as $hqIndex => $hq) {
                    $dsaCount++;
                    $dsa = User::create([
                        'name' => 'DSA User ' . $dsaCount,
                        'email' => 'dsa' . $dsaCount . '@example.com',
                        'phone_number' => '92000000' . $dsaCount,
                        'division_id' => 1, // adjust as needed
                        'password' => Hash::make('password'),
                        'location_type' => Headquarter::class,
                        'location_id' => $hq->id,
                    ]);
                    $dsa->roles()->sync([$roles['DSA']->id]);
                }
            }
        }
    }
} 