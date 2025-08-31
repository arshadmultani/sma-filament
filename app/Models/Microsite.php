<?php

namespace App\Models;

use App\Contracts\IsCampaignEntry;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use App\Models\Scopes\TeamHierarchyScope;
use App\Traits\HasActivity;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;


#[ScopedBy(TeamHierarchyScope::class)]

class Microsite extends Model implements IsCampaignEntry
{
    use LogsActivity, HasActivity;
    protected $guarded = [];
    protected $casts = [
        'reviews' => 'array',
    ];

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

    // protected static function booted()
    // {
    //     static::deleting(function ($microsite) {
    //         $microsite->campaignEntry()->delete();
    //     });

    //     static::saving(function ($model) {
    //         $model->is_active = $model->status === 'Approved';
    //     });
    // }
}
