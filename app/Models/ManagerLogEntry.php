<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Scopes\TeamHierarchyScope;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use App\Contracts\IsCampaignEntry;

#[ScopedBy(TeamHierarchyScope::class)]

class ManagerLogEntry extends Model implements IsCampaignEntry
{
    protected $guarded = [];

    protected $casts = [
        'date' => 'date',
        'activity_doctor_met' => 'boolean',
        'worked_with_team' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function colleagues()
    {
        return $this->hasMany(ManagerLogEntryColleague::class);
    }
    public function customer()
    {
        return $this->morphTo();
    }

    public function activities()
    {
        return $this->hasMany(ManagerLogEntryActivity::class);
    }

    public function callInput()
    {
        return $this->belongsTo(CallInput::class);
    }
    public function campaignEntry()
    {
        return $this->morphOne(CampaignEntry::class, 'entryable');
    }
    protected static function booted()
    {
        static::deleting(function ($managerLogEntry) {
            $managerLogEntry->campaignEntry()->delete();
        });
    }
}
