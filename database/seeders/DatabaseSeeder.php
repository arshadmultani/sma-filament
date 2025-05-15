<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\Hash;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        

        $this->call([
            DivisionSeeder::class,
            RegionSeeder::class,
            AreaSeeder::class,
            HeadquarterSeeder::class,
            DoctorSeeder::class,
            ChemistSeeder::class,
            
            ProductSeeder::class,
        ]);

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'admin@gmail.com',
            'phone_number' => '1234567899',
            'password' => Hash::make('admin'),
            'division_id' => 1,
            'headquarter_id' => 1,
            'region_id' => 1,
            'area_id' => 1,
        ]);
    }
}
