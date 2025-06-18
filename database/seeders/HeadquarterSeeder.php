<?php

namespace Database\Seeders;

use App\Models\Area;
use App\Models\Headquarter;
use Illuminate\Database\Seeder;

class HeadquarterSeeder extends Seeder
{
    public function run(): void
    {
        Headquarter::truncate();

        $areas = Area::all()->keyBy(function ($area) {
            return strtolower($area->name);
        });
        $csvFile = fopen(base_path('csv/phytonova/headquarterseeder.csv'), 'r');
        $headquartersToInsert = [];
        $missingAreas = [];
        $batchSize = 1000;

        fgetcsv($csvFile, 2000, ',', '"', '\\');

        while (($data = fgetcsv($csvFile, 2000, ',', '"', '\\')) !== false) {
            $headquarterName = trim($data[0]);
            $areaName = trim($data[1]);
            $areaKey = strtolower($areaName);

            if (isset($areas[$areaKey])) {
                $headquartersToInsert[] = [
                    'name' => $headquarterName,
                    'area_id' => $areas[$areaKey]->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                if (count($headquartersToInsert) >= $batchSize) {
                    Headquarter::insert($headquartersToInsert);
                    $headquartersToInsert = [];
                }
            } else {
                $missingAreas[] = $areaName;
            }
        }
        if(!empty($headquartersToInsert)){
            Headquarter::insert($headquartersToInsert);
        }
        fclose($csvFile);
        if(!empty($missingAreas)){
            logger()->warning('Areas not found: ' . implode(', ', array_unique($missingAreas)));
        }
    }
}
