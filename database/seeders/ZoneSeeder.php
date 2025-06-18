<?php

namespace Database\Seeders;

use App\Models\Zone;
use Illuminate\Database\Seeder;

class ZoneSeeder extends Seeder
{
    public function run(): void
    {
        Zone::truncate();
        $csvFile=fopen(base_path('csv/phytonova/zoneseeder.csv'),'r');
        $firstLine=true;
        while(($data=fgetcsv($csvFile,2000,','))!==false){
            if(!$firstLine){
                Zone::create([
                    'name'=>$data[0],
                ]);
            }
            $firstLine=false;
        }
        fclose($csvFile);
    }
}
