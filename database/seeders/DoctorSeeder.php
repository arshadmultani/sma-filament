<?php

namespace Database\Seeders;

use App\Models\Doctor;
use App\Models\Headquarter;
use App\Models\Qualification;
use App\Models\User;
use App\Models\Specialty;
use Illuminate\Database\Seeder;

class DoctorSeeder extends Seeder
{
    public function run(): void
    {
        $qualificationIds = Qualification::where('category', 'Doctor')->pluck('id')->toArray();
        $headquarterIds = Headquarter::pluck('id')->toArray();
        $specialtyIds = Specialty::pluck('id')->toArray();

        $types = ['Ayurvedic', 'Allopathic'];
        $supportTypes = ['Prescribing', 'Dispensing'];

        // Get all users with DSA role
        $users = User::role('DSA')->get();

        foreach ($users as $user) {
            Doctor::factory()->count(50)->create([
                'user_id' => $user->id,
                'qualification_id' => fake()->randomElement($qualificationIds),
                'specialty_id' => fake()->randomElement($specialtyIds),
                'headquarter_id' => $user->location_id,
                'type' => fake()->randomElement($types),
                'support_type' => fake()->randomElement($supportTypes),
                'status'=>'Approved'
            ]);
        }
    }
}
