<?php

namespace Database\Seeders;

use App\Models\Area;
use App\Models\Region;
use Illuminate\Database\Seeder;

class AreaSeeder extends Seeder
{
    public function run(): void
    {
     Area::truncate();

     $regions=Region::all()->keyBy(function($region){
        return strtolower($region->name);
    });

    $csvFile=fopen(base_path('csv/phytonova/areaseeder.csv'),'r');
    $areasToInsert=[];
    $missingRegions=[];
    $batchSize=1000;

    fgetcsv($csvFile, 2000, ',', '"', '\\');

    while(($data=fgetcsv($csvFile,2000,',','"','\\'))!==false){
        $areaName=trim($data[0]);
        $regionName=trim($data[1]);
        $regionKey=strtolower($regionName);

        if(isset($regions[$regionKey])){
            $areasToInsert[]=[
                'name'=>$areaName,
                'region_id'=>$regions[$regionKey]->id,
                'created_at'=>now(),
                'updated_at'=>now(),
            ];
            if(count($areasToInsert)>= $batchSize){
                Area::insert($areasToInsert);
                $areasToInsert=[];
            }
        }else{
            $missingRegions[]=$regionName;
        }
        
    }
    if(!empty($areasToInsert)){
        Area::insert($areasToInsert);
    }

    fclose($csvFile);

    if(!empty($missingRegions)){
        logger()->warning('Regions not found: ' . implode(', ', array_unique($missingRegions)));
    }
}
}
