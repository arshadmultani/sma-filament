<?php

namespace Database\Seeders;

use App\Models\Area;
use App\Models\Region;
use Illuminate\Database\Seeder;

class AreaSeeder extends Seeder
{
    public function run(): void
    {
     // Area::truncate(); // REMOVE THIS LINE TO PREVENT DELETING EXISTING AREAS

     $regions=Region::all()->keyBy(function($region){
        return strtolower($region->name);
    });

     // Pre-load all divisions into memory with case-insensitive keys
     $divisions=\App\Models\Division::all()->keyBy(function($division){
        return strtolower($division->name);
    });

    $csvFile=fopen(base_path('csv/phytonova/pharma-area.csv'),'r');
    $areasToInsert=[];
    $missingRegions=[];
    $missingDivisions=[];
    $batchSize=1000;

    fgetcsv($csvFile, 2000, ',', '"', '\\');

    while(($data=fgetcsv($csvFile,2000,',','"','\\'))!==false){
        $regionName=trim($data[0]);
        $areaName=trim($data[1]);
        $divisionName=trim($data[2]);
        $regionKey=strtolower($regionName);
        $divisionKey=strtolower($divisionName);

        if(isset($regions[$regionKey]) && isset($divisions[$divisionKey])){
            $regionId = $regions[$regionKey]->id;
            $divisionId = $divisions[$divisionKey]->id;
            // Check if area already exists
            $exists = \App\Models\Area::where('name', $areaName)
                ->where('region_id', $regionId)
                ->where('division_id', $divisionId)
                ->exists();
            if (!$exists) {
                $areasToInsert[]=[
                    'name'=>$areaName,
                    'region_id'=>$regionId,
                    'division_id'=>$divisionId,
                    'created_at'=>now(),
                    'updated_at'=>now(),
                ];
            }
            if(count($areasToInsert)>= $batchSize){
                \App\Models\Area::insert($areasToInsert);
                $areasToInsert=[];
            }
        }else{
            if(!isset($regions[$regionKey])){
                $missingRegions[]=$regionName;
            }
            if(!isset($divisions[$divisionKey])){
                $missingDivisions[]=$divisionName;
            }
        }
        
    }
    if(!empty($areasToInsert)){
        \App\Models\Area::insert($areasToInsert);
    }

    fclose($csvFile);

    if(!empty($missingRegions)){
        logger()->warning('Regions not found: ' . implode(', ', array_unique($missingRegions)));
    }
    if(!empty($missingDivisions)){
        logger()->warning('Divisions not found: ' . implode(', ', array_unique($missingDivisions)));
    }
}
}
