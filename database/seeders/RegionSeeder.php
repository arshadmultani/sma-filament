<?php

namespace Database\Seeders;

use App\Models\Region;
use App\Models\Zone;
use Illuminate\Database\Seeder;

class RegionSeeder extends Seeder
{
    public function run(): void
{
    Region::truncate();

    // Pre-load all zones into memory with case-insensitive keys
    $zones = Zone::all()->keyBy(function ($zone) {
        return strtolower($zone->name);
    });

    $csvFile = fopen(base_path('csv/phytonova/regionseeder.csv'), 'r');
    $regionsToInsert = [];
    $missingZones = [];
    $batchSize = 1000;

    // Skip header row
    fgetcsv($csvFile, 2000, ',', '"', '\\');

    while (($data = fgetcsv($csvFile, 2000, ',', '"', '\\')) !== false) {
        $regionName = trim($data[0]);
        $zoneName = trim($data[1]);
        $zoneKey = strtolower($zoneName);

        if (isset($zones[$zoneKey])) {
            $regionsToInsert[] = [
                'name' => $regionName,
                'zone_id' => $zones[$zoneKey]->id,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            // Batch insert when we reach the batch size
            if (count($regionsToInsert) >= $batchSize) {
                Region::insert($regionsToInsert);
                $regionsToInsert = [];
            }
        } else {
            $missingZones[] = $zoneName;
        }
    }

    // Insert remaining records
    if (!empty($regionsToInsert)) {
        Region::insert($regionsToInsert);
    }

    fclose($csvFile);

    // Log all missing zones at once
    if (!empty($missingZones)) {
        logger()->warning('Zones not found: ' . implode(', ', array_unique($missingZones)));
    }
}
}
