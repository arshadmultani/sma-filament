<?php

namespace App\Listeners;

use App\Models\CampaignEntry;
use Illuminate\Queue\InteractsWithQueue;
use App\Events\CustomerHeadquarterUpdated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Exceptions;

class UpdateActivityModelHeadquarters
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(CustomerHeadquarterUpdated $event): void
    {
        $customer = $event->customer;
        $newHeadquarterId = $event->newHeadquarterId;

        $customerMorphClass = $customer->getMorphClass();

        $campaignEntries = CampaignEntry::query()
            ->where('customer_type', $customerMorphClass)
            ->where('customer_id', $customer->id)
            ->get();

        foreach ($campaignEntries as $entry) {
            // Check if the relationship exists and load it if it does
            if ($entry->relationLoaded('entryable') || $entry->entryable()->exists()) {
                $activity = $entry->entryable;

                // Only proceed if we got a valid activity model
                if ($activity) {
                    try {
                        // Update using save() method instead of update()
                        $activity->headquarter_id = $newHeadquarterId;
                        $updated = $activity->save();
                        Log::info('Customer HQ : ' . ($updated ? 'Success' : 'Failed') . ' for customer ' . $customer->name);
                    } catch (\Exception $e) {
                        Log::error('Failed to update HQ: ' . $e->getMessage());
                    }
                }
            } else {
                Log::warning('Activity not found for campaign entry #' . $entry->id);
            }
        }
    }
}
