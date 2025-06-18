<?php

namespace Database\Seeders;

use App\Models\Chemist;
use App\Models\Headquarter;
use App\Models\User;
use Illuminate\Database\Seeder;

class ChemistSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::role('DSA')->get();
        foreach ($users as $user) {
            Chemist::factory()->count(50)->create([
                'user_id' => $user->id,
                'headquarter_id' => $user->location_id,
                'status'=>'Approved'
            ]);
        }
    }
}

