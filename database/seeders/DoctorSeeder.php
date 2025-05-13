<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Doctor;
use App\Models\Headquarter;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DoctorSeeder extends Seeder
{
    public function run(): void
    {
        $headquarterIds = Headquarter::pluck('id')->toArray();
        $degrees = ['MBBS', 'BAHM', 'BAMS', 'MD'];

        for ($i = 0; $i < 100; $i++) {
            $user = User::factory()->create();
            Doctor::create([
                'name' => fake()->name(),
                'email' => fake()->unique()->safeEmail(),
                'phone' => fake()->phoneNumber(),
                'degree' => $degrees[array_rand($degrees)],
                'profile_photo' => 'https://i.pravatar.cc/150?img=' . rand(1, 70),
                'user_id' => $user->id,
                'headquarter_id' => fake()->randomElement($headquarterIds),
            ]);
        }
    }
} 