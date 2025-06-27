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

            $divisions = \App\Models\Division::all()->keyBy(fn($d) => strtolower($d->name));
            $zones = \App\Models\Zone::all()->groupBy('division_id');
            $regions = \App\Models\Region::all()->groupBy('division_id');
            $areas = \App\Models\Area::all()->groupBy('division_id');
            $headquarters = \App\Models\Headquarter::all()->groupBy('division_id');
            $existingEmails = User::pluck('email')->map(fn($e) => strtolower($e))->toArray();

            $csvFile = fopen(base_path('csv/phytonova/pharma-user.csv'), 'r');
            $usersToInsert = [];
            $batchSize = 1000;
            $missingDivisions = [];
            $missingLocations = [];
            $invalidRoles = [];
            $skippedEmails = [];

            $header = fgetcsv($csvFile, 2000, ',', '"', '\\');
            while (($data = fgetcsv($csvFile, 2000, ',', '"', '\\')) !== false) {
                $row = array_combine($header, $data);
                $email = strtolower(trim($row['Email'] ?? ''));
                if (empty($email) || in_array($email, $existingEmails)) {
                    $skippedEmails[] = $email;
                    continue;
                }
                $existingEmails[] = $email;

                $divisionName = strtolower(trim($row['Division'] ?? ''));
                $division = $divisions[$divisionName] ?? null;
                if (!$division) {
                    $missingDivisions[] = $row['Division'] ?? '';
                    continue;
                }

                $roleName = trim($row['Role'] ?? '');
                $roleConfig = [
                    'ZSM' => ['model' => \App\Models\Zone::class, 'id_key' => 'Zone', 'collection' => $zones],
                    'RSM' => ['model' => \App\Models\Region::class, 'id_key' => 'Region', 'collection' => $regions],
                    'ASM' => ['model' => \App\Models\Area::class, 'id_key' => 'Area', 'collection' => $areas],
                    'DSA' => ['model' => \App\Models\Headquarter::class, 'id_key' => 'Headquarter', 'collection' => $headquarters],
                ];
                if (!isset($roleConfig[$roleName])) {
                    $invalidRoles[] = $roleName;
                    continue;
                }
                $config = $roleConfig[$roleName];
                $divisionId = $division->id;
                $locationName = '';
                // Fallback logic for ASM and RSM
                if ($roleName === 'ASM') {
                    $locationName = strtolower(trim($row['Area'] ?? ''));
                    if (empty($locationName) && !empty($row['Region'])) {
                        $locationName = strtolower(trim($row['Region']));
                        if (isset($regions[$divisionId])) {
                            $location = $regions[$divisionId]->first(fn($loc) => strtolower($loc->name) === $locationName);
                            if ($location) {
                                $config['model'] = \App\Models\Region::class;
                                $config['collection'] = $regions;
                                $config['id_key'] = 'Region';
                            }
                        }
                    }
                } elseif ($roleName === 'RSM') {
                    $locationName = strtolower(trim($row['Region'] ?? ''));
                    if (empty($locationName) && !empty($row['Zone'])) {
                        $locationName = strtolower(trim($row['Zone']));
                        if (isset($zones[$divisionId])) {
                            $location = $zones[$divisionId]->first(fn($loc) => strtolower($loc->name) === $locationName);
                            if ($location) {
                                $config['model'] = \App\Models\Zone::class;
                                $config['collection'] = $zones;
                                $config['id_key'] = 'Zone';
                            }
                        }
                    }
                } else {
                    $locationName = strtolower(trim($row[$config['id_key']] ?? ''));
                }
                $location = null;
                if (!empty($locationName) && isset($config['collection'][$divisionId])) {
                    $location = $config['collection'][$divisionId]->first(function ($loc) use ($locationName) {
                        return strtolower($loc->name) === $locationName;
                    });
                }
                if (!$location) {
                    $missingLocations[] = $row[$config['id_key']] ?? '';
                    continue;
                }

                $usersToInsert[] = [
                    'user' => [
                        'name' => $row['Name'] ?? '',
                        'email' => $row['Email'] ?? '',
                        'phone_number' => $row['Phone'] ?? '',
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
