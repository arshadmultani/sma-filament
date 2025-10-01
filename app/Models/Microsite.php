<?php

namespace App\Models;

use App\Traits\HasActivity;
use App\Contracts\IsCampaignEntry;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Spatie\Activitylog\LogOptions;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use App\Models\Scopes\TeamHierarchyScope;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;

#[ScopedBy(TeamHierarchyScope::class)]

class Microsite extends Model implements IsCampaignEntry
{
    use HasActivity, LogsActivity;
    protected $guarded = [];
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['state_name', 'is_active',]);
    }
    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    public function template()
    {
        return $this->belongsTo(MicrositeTemplate::class);
    }

    public function reviews(): HasManyThrough
    {
        return $this->hasManyThrough(Review::class, Doctor::class, 'id', 'doctor_id', 'doctor_id', 'id');
    }

    public function showcases(): HasManyThrough
    {
        return $this->hasManyThrough(Showcase::class, Doctor::class, 'id', 'doctor_id', 'doctor_id', 'id');
    }
    public function getRouteKeyName()
    {
        return 'url';
    }
    protected static function booted()
    {
        parent::booted();

        static::deleting(function ($microsite) {
            if ($microsite->doctor) {
                $microsite->doctor->showcases->each(function ($showcase) {
                    Log::info('DELETING EVENT: Triggering delete for individual Showcase.', ['showcase_id' => $showcase->id]);
                    $showcase->delete();
                });

                $microsite->doctor->reviews()->delete();
            }
        });
    }
}
