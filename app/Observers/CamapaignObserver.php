<?php

namespace App\Observers;

use App\Models\Campaign;
use Illuminate\Support\Facades\Cache;

class CamapaignObserver
{
    /**
     * Handle the Campaign "created" event.
     */
    public function created(Campaign $campaign): void
    {
        Cache::forget(Campaign::getCacheKeyForEntryType($campaign->allowed_entry_type));
    }

    /**
     * Handle the Campaign "updated" event.
     */
    public function updated(Campaign $campaign): void
    {
        Cache::forget(Campaign::getCacheKeyForEntryType($campaign->allowed_entry_type));

        if ($campaign->wasChanged('allowed_entry_type')) {
            Cache::forget(Campaign::getCacheKeyForEntryType($campaign->getOriginal('allowed_entry_type')));
        }
    }

    /**
     * Handle the Campaign "deleted" event.
     */
    public function deleted(Campaign $campaign): void
    {
        Cache::forget(Campaign::getCacheKeyForEntryType($campaign->allowed_entry_type));

    }

    /**
     * Handle the Campaign "restored" event.
     */
    public function restored(Campaign $campaign): void
    {
        //
    }

    /**
     * Handle the Campaign "force deleted" event.
     */
    public function forceDeleted(Campaign $campaign): void
    {
        //
    }
}
