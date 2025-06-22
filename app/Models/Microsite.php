<?php

namespace App\Models;

use App\Contracts\IsCampaignEntry;
use Illuminate\Database\Eloquent\Model;
use App\Models\Scopes\TeamHierarchyScope;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;


#[ScopedBy(TeamHierarchyScope::class)]

class Microsite extends Model implements IsCampaignEntry
{
    protected $fillable = ['doctor_id', 'url', 'is_active', 'status'];

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    // public function reviews()
    // {
    //     return $this->doctor()->reviews();
    // }

    public function campaignEntry()
    {
        return $this->morphOne(CampaignEntry::class, 'entryable');
    }

    protected static function booted()
    {
        static::deleting(function ($microsite) {
            $microsite->campaignEntry()->delete();
        });
    }
}
