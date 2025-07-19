<?php

namespace App\Models;

use App\Contracts\IsCampaignEntry;
use App\Contracts\HeadquarterFilterable;
use Illuminate\Database\Eloquent\Model;
use App\Models\Scopes\TeamHierarchyScope;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Builder;


#[ScopedBy(TeamHierarchyScope::class)]

class Microsite extends Model implements IsCampaignEntry, HeadquarterFilterable
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

    public function scopeWhereHeadquarterIn(Builder $query, array $headquarterIds): Builder
    {
        return $query->whereHas('doctor.headquarter', function ($q) use ($headquarterIds) {
            $q->whereIn('id', $headquarterIds);
        });
    }

    public function scopeWhereLocationIn(Builder $query, string $locationType, array $locationIds): Builder
    {
        return $query->whereHas('doctor.headquarter', function ($q) use ($locationType, $locationIds) {
            switch ($locationType) {
                case 'headquarter':
                    $q->whereIn('id', $locationIds);
                    break;
                case 'area':
                    $q->whereIn('area_id', $locationIds);
                    break;
                case 'region':
                    $q->whereHas('area', function ($q2) use ($locationIds) {
                        $q2->whereIn('region_id', $locationIds);
                    });
                    break;
                case 'zone':
                    $q->whereHas('area.region', function ($q2) use ($locationIds) {
                        $q2->whereIn('zone_id', $locationIds);
                    });
                    break;
            }
        });
    }
}
