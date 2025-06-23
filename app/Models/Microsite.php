<?php

namespace App\Models;

use App\Contracts\IsCampaignEntry;
use Illuminate\Database\Eloquent\Model;
use App\Models\Scopes\TeamHierarchyScope;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;


#[ScopedBy(TeamHierarchyScope::class)]

class Microsite extends Model implements IsCampaignEntry
{
    protected $guarded = [];
    protected $casts = [
        'reviews' => 'array',
    ];

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
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

        static::saving(function ($model) {
            $model->is_active = $model->status === 'Approved';
        });
    }
}
