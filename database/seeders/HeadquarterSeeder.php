<?php

namespace Database\Seeders;

use App\Models\Area;
use App\Models\Headquarter;
use Illuminate\Database\Seeder;

class HeadquarterSeeder extends Seeder
{
    public function run(): void
    {
        $areas = Area::all()->keyBy(function ($area) {
            return strtolower($area->name);
        });
        $divisions = \App\Models\Division::all()->keyBy(function ($division) {
            return strtolower($division->name);
        });
        $csvFile = fopen(base_path('csv/phytonova/pharma-hq.csv'), 'r');
        $headquartersToInsert = [];
        $missingAreas = [];
        $missingDivisions = [];
        $batchSize = 1000;

        fgetcsv($csvFile, 2000, ',', '"', '\\');

        while (($data = fgetcsv($csvFile, 2000, ',', '"', '\\')) !== false) {
            $headquarterName = trim($data[0]);
            $areaName = trim($data[1]);
            $divisionName = trim($data[2]);
            $areaKey = strtolower($areaName);
            $divisionKey = strtolower($divisionName);

            if (isset($areas[$areaKey]) && isset($divisions[$divisionKey])) {
                $areaId = $areas[$areaKey]->id;
                $divisionId = $divisions[$divisionKey]->id;
                // Check if headquarter already exists
                $exists = \App\Models\Headquarter::where('name', $headquarterName)
                    ->where('area_id', $areaId)
                    ->where('division_id', $divisionId)
                    ->exists();
                if (!$exists) {
                    $headquartersToInsert[] = [
                        'name' => $headquarterName,
                        'area_id' => $areaId,
                        'division_id' => $divisionId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
                if (count($headquartersToInsert) >= $batchSize) {
                    \App\Models\Headquarter::insert($headquartersToInsert);
                    $headquartersToInsert = [];
                }
            } else {
                if (!isset($areas[$areaKey])) {
                    $missingAreas[] = $areaName;
                }
                if (!isset($divisions[$divisionKey])) {
                    $missingDivisions[] = $divisionName;
                }
            }
        }
        if(!empty($headquartersToInsert)){
            \App\Models\Headquarter::insert($headquartersToInsert);
        }
        fclose($csvFile);
        if(!empty($missingAreas)){
            logger()->warning('Areas not found: ' . implode(', ', array_unique($missingAreas)));
        }
        if(!empty($missingDivisions)){
            logger()->warning('Divisions not found: ' . implode(', ', array_unique($missingDivisions)));
        }
    }
}
