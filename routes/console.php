<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('backup:clean')->dailyAt('02:30');

Schedule::command('backup:run')->dailyAt('03:00'); 
Schedule::command('backup:run')->dailyAt('09:00');  
Schedule::command('backup:run')->dailyAt('15:00'); 
Schedule::command('backup:run')->dailyAt('21:00'); 
