<?php

namespace Database\Seeders;

use App\Models\Doctor;
use App\Models\Headquarter;
use App\Models\Qualification;
use App\Models\User;
use Illuminate\Database\Seeder;

class DoctorSeeder extends Seeder
{
    public function run(): void
    {
        $qualificationIds = Qualification::where('category', 'Doctor')->pluck('id')->toArray();
        $headquarterIds = Headquarter::pluck('id')->toArray();

        // Get 10 random users
        $users = User::inRandomOrder()->take(10)->get();
        $types = ['Ayurvedic', 'Allopathic'];
        $supportTypes = ['Prescribing', 'Dispensing'];
        foreach ($users as $user) {
            for ($i = 0; $i < 10; $i++) {
                Doctor::create([
                    'name' => fake()->name(),
                    'email' => fake()->unique()->safeEmail(),
                    'phone' => fake()->phoneNumber(),
                    'qualification_id' => fake()->randomElement($qualificationIds),
                    'profile_photo' => 'https://i.pravatar.cc/150?img='.rand(1, 70),
                    'user_id' => $user->id,
                    'headquarter_id' => fake()->randomElement($headquarterIds),
                    'attachment' => [
                        'https://picsum.photos/200/300?random='.rand(1, 100),
                        'https://picsum.photos/200/300?random='.rand(101, 200),
                    ],
                    'address' => fake()->address(),
                    'town' => fake()->city(),
                    'type' => fake()->randomElement($types),
                    'support_type' => fake()->randomElement($supportTypes),
                ]);
            }
        }
    }
}
