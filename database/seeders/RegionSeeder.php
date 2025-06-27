<?php

namespace Database\Seeders;

use App\Models\Region;
use App\Models\Zone;
use Illuminate\Database\Seeder;

class RegionSeeder extends Seeder
{
    public function run(): void
{
    // Pre-load all zones into memory with case-insensitive keys
    $zones = Zone::all()->keyBy(function ($zone) {
        return strtolower($zone->name);
    });
    // Pre-load all divisions into memory with case-insensitive keys
    $divisions = \App\Models\Division::all()->keyBy(function ($division) {
        return strtolower($division->name);
    });

    $csvFile = fopen(base_path('csv/phytonova/pharma-region.csv'), 'r');
    $regionsToInsert = [];
    $missingZones = [];
    $missingDivisions = [];
    $batchSize = 1000;

    // Skip header row
    fgetcsv($csvFile, 2000, ',', '"', '\\');

    while (($data = fgetcsv($csvFile, 2000, ',', '"', '\\')) !== false) {
        $regionName = trim($data[1]);
        $zoneName = trim($data[0]);
        $divisionName = trim($data[2]);
        $zoneKey = strtolower($zoneName);
        $divisionKey = strtolower($divisionName);

        if (isset($zones[$zoneKey]) && isset($divisions[$divisionKey])) {
            $zoneId = $zones[$zoneKey]->id;
            $divisionId = $divisions[$divisionKey]->id;
            // Check if region already exists
            $exists = \App\Models\Region::where('name', $regionName)
                ->where('zone_id', $zoneId)
                ->where('division_id', $divisionId)
                ->exists();
            if (!$exists) {
                $regionsToInsert[] = [
                    'name' => $regionName,
                    'zone_id' => $zoneId,
                    'division_id' => $divisionId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            if (count($regionsToInsert) >= $batchSize) {
                \App\Models\Region::insert($regionsToInsert);
                $regionsToInsert = [];
            }
        } else {
            if (!isset($zones[$zoneKey])) {
                $missingZones[] = $zoneName;
            }
            if (!isset($divisions[$divisionKey])) {
                $missingDivisions[] = $divisionName;
            }
        }
    }

    // Insert remaining records
    if (!empty($regionsToInsert)) {
        \App\Models\Region::insert($regionsToInsert);
    }

    fclose($csvFile);

    // Log all missing zones and divisions at once
    if (!empty($missingZones)) {
        logger()->warning('Zones not found: ' . implode(', ', array_unique($missingZones)));
    }
    if (!empty($missingDivisions)) {
        logger()->warning('Divisions not found: ' . implode(', ', array_unique($missingDivisions)));
    }
}
}
