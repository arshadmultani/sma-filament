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
            User::truncate();

            $divisions = \App\Models\Division::all()->keyBy(fn($d) => strtolower($d->name));
            $zones = \App\Models\Zone::all()->keyBy(fn($z) => strtolower($z->name));
            $regions = \App\Models\Region::all()->keyBy(fn($r) => strtolower($r->name));
            $areas = \App\Models\Area::all()->keyBy(fn($a) => strtolower($a->name));
            $headquarters = \App\Models\Headquarter::all()->keyBy(fn($h) => strtolower($h->name));
            $existingEmails = User::pluck('email')->map(fn($e) => strtolower($e))->toArray();

            $csvFile = fopen(base_path('csv/phytonova/userseeder.csv'), 'r');
            $usersToInsert = [];
            $batchSize = 1000;
            $missingDivisions = [];
            $missingLocations = [];
            $invalidRoles = [];
            $skippedEmails = [];

            $header = fgetcsv($csvFile, 2000, ',', '"', '\\');
            while (($data = fgetcsv($csvFile, 2000, ',', '"', '\\')) !== false) {
                $row = array_combine($header, $data);
                $email = strtolower(trim($row['Email']));
                if (in_array($email, $existingEmails)) {
                    $skippedEmails[] = $email;
                    continue;
                }
                $existingEmails[] = $email;

                $divisionName = strtolower(trim($row['Division']));
                $division = $divisions[$divisionName] ?? null;
                if (!$division) {
                    $missingDivisions[] = $row['Division'];
                    continue;
                }

                $roleName = trim($row['Role']);
                $roleConfig = [
                    'ZSM' => ['model' => \App\Models\Zone::class, 'id_key' => 'ZONE', 'collection' => $zones],
                    'RSM' => ['model' => \App\Models\Region::class, 'id_key' => 'Region', 'collection' => $regions],
                    'ASM' => ['model' => \App\Models\Area::class, 'id_key' => 'AREA', 'collection' => $areas],
                    'DSA' => ['model' => \App\Models\Headquarter::class, 'id_key' => 'Headquarter', 'collection' => $headquarters],
                ];
                if (!isset($roleConfig[$roleName])) {
                    $invalidRoles[] = $roleName;
                    continue;
                }
                $config = $roleConfig[$roleName];
                $locationName = strtolower(trim($row[$config['id_key']]));
                $location = $config['collection'][$locationName] ?? null;
                if (!$location) {
                    $missingLocations[] = $row[$config['id_key']];
                    continue;
                }

                $usersToInsert[] = [
                    'user' => [
                        'name' => $row['Name'],
                        'email' => $row['Email'],
                        'phone_number' => $row['Phone Number'],
                        'division_id' => $division->id,
                        'location_type' => $config['model'],
                        'location_id' => $location->id,
                        'password' => Hash::make(env('DEFAULT_USER_PASSWORD')),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                    'role' => $roleName,
                ];

                if (count($usersToInsert) >= $batchSize) {
                    $this->insertAndAssignRoles($usersToInsert);
                    $usersToInsert = [];
                }
            }
            if (!empty($usersToInsert)) {
                $this->insertAndAssignRoles($usersToInsert);
            }
            fclose($csvFile);

            if (!empty($missingDivisions)) {
                logger()->warning('Divisions not found: ' . implode(', ', array_unique($missingDivisions)));
            }
            if (!empty($missingLocations)) {
                logger()->warning('Locations not found: ' . implode(', ', array_unique($missingLocations)));
            }
            if (!empty($invalidRoles)) {
                logger()->warning('Invalid roles: ' . implode(', ', array_unique($invalidRoles)));
            }
            if (!empty($skippedEmails)) {
                logger()->info('Skipped duplicate emails: ' . implode(', ', array_unique($skippedEmails)));
            }

            // Add super_admin user
            // try {
            //     $superAdmin = User::updateOrCreate(
            //         [
            //             'email' => 'arshadrmultani@gmail.com',
            //         ],
            //         [
            //             'name' => 'arm',
            //             'password' => (config('app.default_user_password')),
            //             'division_id' => 1,
            //         ]
            //     );
            //     $superAdmin->syncRoles(['super_admin']);
            // } catch (\Throwable $e) {
            //     logger()->error('Failed to create super_admin: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            // }
        } catch (\Throwable $e) {
            logger()->error('UserSeeder run() failed: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
        }
    }

    private function insertAndAssignRoles(array $usersWithRoles): void
    {
        try {
            $userRows = array_map(fn($item) => $item['user'], $usersWithRoles);
            User::insert($userRows);
            // Assign roles after insert
            foreach ($usersWithRoles as $item) {
                try {
                    $user = User::where('email', $item['user']['email'])->first();
                    if ($user && !empty($item['role'])) {
                        $user->syncRoles([$item['role']]);
                    }
                } catch (\Throwable $e) {
                    logger()->error('Failed to assign role for user: ' . ($item['user']['email'] ?? 'unknown') . ' - ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
                }
            }
        } catch (\Throwable $e) {
            logger()->error('Batch insert or role assignment failed: ' . $e->getMessage(), ['trace' => $e->getTraceAsString(), 'users' => $usersWithRoles]);
        }
    }
}
