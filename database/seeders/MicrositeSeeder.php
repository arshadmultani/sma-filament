<?php

namespace Database\Seeders;

use App\Models\Doctor;
use App\Models\User;
use App\Models\Campaign;
use App\Models\Microsite;
use Illuminate\Database\Seeder;

class MicrositeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::role('DSA')->get();
        $campaigns = Campaign::where('allowed_entry_type', 'microsite')->get();

        foreach ($users as $user) {
            // Get doctors from the same headquarter as the user
            $doctors = Doctor::where('headquarter_id', $user->location_id)->get();

            for ($i = 0; $i < 10; $i++) {
                if ($doctors->isEmpty()) {
                    continue;
                }

                $doctor = $doctors->random();
                $campaign = $campaigns->first();

                // Generate a unique URL for the microsite
                $firstName = explode(' ', $doctor->name)[0];
                $slug = \Illuminate\Support\Str::slug($firstName);
                do {
                    $random = \Illuminate\Support\Str::lower(\Illuminate\Support\Str::random(5));
                    $url = $slug . '-' . $random;
                } while (Microsite::where('url', $url)->exists());

                $microsite = Microsite::create([
                    'doctor_id' => $doctor->id,
                    'url' => $url,
                    'is_active' => false,
                    'status' => 'Pending',
                    'user_id' => $user->id,
                ]);

                // Create the CampaignEntry link
                $microsite->campaignEntry()->create([
                    'campaign_id'   => $campaign->id,
                    'customer_id'   => $doctor->id,
                    'customer_type' => $doctor->getMorphClass(),
                ]);
            }
        }
    }
}
