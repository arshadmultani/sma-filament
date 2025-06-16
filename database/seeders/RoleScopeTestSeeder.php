<?php

namespace Database\Seeders;

use App\Models\Area;
use App\Models\Doctor;
use App\Models\Headquarter;
use App\Models\Region;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleScopeTestSeeder extends Seeder
{
    public function run(): void
    {
        // Create roles if not exist
        foreach (['RSM', 'ASM', 'DSA', 'admin', 'super_admin'] as $role) {
            Role::firstOrCreate(['name' => $role]);
        }

        // Create region, areas, headquarters
        $region = Region::firstOrCreate(['name' => 'Region 1']);
        $area1 = Area::firstOrCreate(['name' => 'Area 1', 'region_id' => $region->id]);
        $area2 = Area::firstOrCreate(['name' => 'Area 2', 'region_id' => $region->id]);
        $hq1 = Headquarter::firstOrCreate(['name' => 'HQ 1', 'area_id' => $area1->id]);
        $hq2 = Headquarter::firstOrCreate(['name' => 'HQ 2', 'area_id' => $area1->id]);
        $hq3 = Headquarter::firstOrCreate(['name' => 'HQ 3', 'area_id' => $area2->id]);

        // Create users for each role
        $rsm = User::factory()->create(['name' => 'RSM User', 'location_id' => $region->id]);
        $rsm->assignRole('RSM');

        $asm = User::factory()->create(['name' => 'ASM User', 'location_id' => $area1->id]);
        $asm->assignRole('ASM');

        $dsa = User::factory()->create(['name' => 'DSA User', 'location_id' => $hq1->id]);
        $dsa->assignRole('DSA');

        // Create doctors for each HQ
        Doctor::factory()->create(['name' => 'Doctor 1', 'headquarter_id' => $hq1->id, 'user_id' => $dsa->id]);
        Doctor::factory()->create(['name' => 'Doctor 2', 'headquarter_id' => $hq2->id, 'user_id' => $asm->id]);
        Doctor::factory()->create(['name' => 'Doctor 3', 'headquarter_id' => $hq3->id, 'user_id' => $rsm->id]);
    }
}
