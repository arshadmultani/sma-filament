<?php

namespace App\Traits;

use App\Models\User;
use App\Models\State;
use App\Models\Headquarter;
use App\Models\CampaignEntry;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait HasActivity
{
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function headquarter(): BelongsTo
    {
        return $this->belongsTo(Headquarter::class);
    }

    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class);
    }

    public function customer(): MorphTo
    {
        return $this->morphTo();
    }

    public function campaignEntry(): MorphOne
    {
        return $this->morphOne(CampaignEntry::class, 'entryable');
    }

    protected static function bootHasActivity(): void
    {
        static::deleting(function ($model) {
            if (method_exists($model, 'campaignEntry')) {
                $model->campaignEntry()->delete();
            }
        });
    }
}
