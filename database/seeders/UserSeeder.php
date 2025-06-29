<?php

namespace Database\Seeders;

use App\Models\Area;
use App\Models\Headquarter;
use App\Models\Region;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        try {
            $batchSize = 1000;
            $usersToInsert = [];
            $existingEmails = User::pluck('email')->map(fn($e) => strtolower($e))->toArray();
            $divisions = \App\Models\Division::all();
            foreach ($divisions as $division) {
                $headquarters = \App\Models\Headquarter::where('division_id', $division->id)->get();
                foreach ($headquarters as $headquarter) {
                    for ($i = 0; $i < 3; $i++) {
                        $fakeUser = User::factory()->make();
                        $email = strtolower($fakeUser->email);
                        if (in_array($email, $existingEmails)) {
                            // Ensure unique email
                            $fakeUser->email = fake()->unique()->safeEmail();
                            $email = strtolower($fakeUser->email);
                        }
                        $existingEmails[] = $email;
                        $usersToInsert[] = [
                            'user' => [
                                'email' => $fakeUser->email,
                                'phone_number' => $fakeUser->phone_number,
                                'division_id' => $division->id,
                                'location_type' => \App\Models\Headquarter::class,
                                'location_id' => $headquarter->id,
                                'password' => $fakeUser->password,
                                'created_at' => now(),
                                'updated_at' => now(),
                                'name' => $division->name.'-'.$headquarter->name.' DSA',
                            ],
                            'role' => 'DSA',
                        ];
                        if (count($usersToInsert) >= $batchSize) {
                            $this->insertAndAssignRoles($usersToInsert);
                            $usersToInsert = [];
                        }
                    }
                }
            }
            // For each Area, create one ASM user
            $areas = \App\Models\Area::all();
            foreach ($areas as $area) {
                $fakeUser = User::factory()->make();
                $email = strtolower($fakeUser->email);
                if (in_array($email, $existingEmails)) {
                    $fakeUser->email = fake()->unique()->safeEmail();
                    $email = strtolower($fakeUser->email);
                }
                $existingEmails[] = $email;
                $usersToInsert[] = [
                    'user' => [
                        'email' => $fakeUser->email,
                        'phone_number' => $fakeUser->phone_number,
                        'division_id' => $area->division_id,
                        'location_type' => \App\Models\Area::class,
                        'location_id' => $area->id,
                        'password' => $fakeUser->password,
                        'created_at' => now(),
                        'updated_at' => now(),
                        'name' => $division->name.'-'.$area->name.' ASM',
                    ],
                    'role' => 'ASM',
                ];
                if (count($usersToInsert) >= $batchSize) {
                    $this->insertAndAssignRoles($usersToInsert);
                    $usersToInsert = [];
                }
            }
            // For each Region, create one RSM user
            $regions = \App\Models\Region::all();
            foreach ($regions as $region) {
                $fakeUser = User::factory()->make();
                $email = strtolower($fakeUser->email);
                if (in_array($email, $existingEmails)) {
                    $fakeUser->email = fake()->unique()->safeEmail();
                    $email = strtolower($fakeUser->email);
                }
                $existingEmails[] = $email;
                $usersToInsert[] = [
                    'user' => [
                        'email' => $fakeUser->email,
                        'phone_number' => $fakeUser->phone_number,
                        'division_id' => $region->division_id,
                        'location_type' => \App\Models\Region::class,
                        'location_id' => $region->id,
                        'password' => $fakeUser->password,
                        'created_at' => now(),
                        'updated_at' => now(),
                        'name' => $division->name.'-'.$region->name.' RSM',
                    ],
                    'role' => 'RSM',
                ];
                if (count($usersToInsert) >= $batchSize) {
                    $this->insertAndAssignRoles($usersToInsert);
                    $usersToInsert = [];
                }
            }
            // For each Zone, create one ZSM user
            $zones = \App\Models\Zone::all();
            foreach ($zones as $zone) {
                $fakeUser = User::factory()->make();
                $email = strtolower($fakeUser->email);
                if (in_array($email, $existingEmails)) {
                    $fakeUser->email = fake()->unique()->safeEmail();
                    $email = strtolower($fakeUser->email);
                }
                $existingEmails[] = $email;
                $usersToInsert[] = [
                    'user' => [
                        'email' => $fakeUser->email,
                        'phone_number' => $fakeUser->phone_number,
                        'division_id' => $zone->division_id,
                        'location_type' => \App\Models\Zone::class,
                        'location_id' => $zone->id,
                        'password' => $fakeUser->password,
                        'created_at' => now(),
                        'updated_at' => now(),
                        'name' => $division->name.'-'.$zone->name.' ZSM',

                    ],
                    'role' => 'ZSM',
                ];
                if (count($usersToInsert) >= $batchSize) {
                    $this->insertAndAssignRoles($usersToInsert);
                    $usersToInsert = [];
                }
            }
            if (!empty($usersToInsert)) {
                $this->insertAndAssignRoles($usersToInsert);
            }
        } catch (\Throwable $e) {
            logger()->error('UserSeeder run() failed: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
        }
    }

    private function insertAndAssignRoles(array $usersWithRoles): void
    {
        try {
            foreach ($usersWithRoles as $item) {
                try {
                    // Only insert if user does not exist
                    $user = User::where('email', $item['user']['email'])->first();
                    if (!$user) {
                        $user = User::create($item['user']);
                    }
                    // Only assign role if not already assigned
                    if ($user && !empty($item['role']) && !$user->hasRole($item['role'])) {
                        $user->assignRole($item['role']);
                    }
                } catch (\Throwable $e) {
                    logger()->error('Failed to insert or assign role for user: ' . ($item['user']['email'] ?? 'unknown') . ' - ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
                }
            }
        } catch (\Throwable $e) {
            logger()->error('Batch insert or role assignment failed: ' . $e->getMessage(), ['trace' => $e->getTraceAsString(), 'users' => $usersWithRoles]);
        }
    }
}
