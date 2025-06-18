<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $this->call([
            RoleSeeder::class,
            DivisionSeeder::class,
            ZoneSeeder::class,
            RegionSeeder::class,
            AreaSeeder::class,
            HeadquarterSeeder::class,
            UserSeeder::class,
            QualificationSeeder::class,
            // DoctorSeeder::class,
            // ChemistSeeder::class,
            // ProductSeeder::class,
            // KofolCampaignSeeder::class,
            // KofolEntrySeeder::class,
            
        ]);
        $role = 'super_admin';

        $user = User::factory()->create([
            'name' => 'Super Admin',
            'email' => 'arshadrmultani@gmail.com',
            'phone_number' => '1234567890',
            'password' => Hash::make('admin'),
            'division_id' => 1,

        ]);
        $user->assignRole($role);
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'admin@gmail.com',
            'phone_number' => '1234567899',
            'password' => Hash::make('admin'),
            'division_id' => 1,

        ]);
    }
}
