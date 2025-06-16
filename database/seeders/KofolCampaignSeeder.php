<?php

namespace Database\Seeders;

use App\Models\KofolCampaign;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class KofolCampaignSeeder extends Seeder
{
    public function run(): void
    {
        KofolCampaign::insert([
            [
                'name' => 'Summer Bonanza',
                'description' => 'Special summer campaign for Kofol.',
                'start_date' => Carbon::now()->subDays(10),
                'end_date' => Carbon::now()->addDays(20),
                'is_active' => true,
            ],
            [
                'name' => 'Winter Wellness',
                'description' => 'Winter health campaign for Kofol.',
                'start_date' => Carbon::now()->subDays(30),
                'end_date' => Carbon::now()->addDays(10),
                'is_active' => false,
            ],
            [
                'name' => 'Festive Offer',
                'description' => 'Festive season offer for Kofol products.',
                'start_date' => Carbon::now()->subDays(5),
                'end_date' => Carbon::now()->addDays(25),
                'is_active' => true,
            ],
        ]);
    }
}
