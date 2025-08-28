<?php

namespace App\Models;

use App\Contracts\IsCampaignEntry;
use App\Models\Scopes\TeamHierarchyScope;
use App\Traits\HasActivity;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Builder;
use App\Enums\StateCategory;


#[ScopedBy(TeamHierarchyScope::class)]
class POB extends Model implements IsCampaignEntry
{
    use HasActivity, LogsActivity;
    protected $guarded = [];

    // currently not used as issue in image entry infolist
    // protected $casts = [
    //     'invoice_image' => 'array'
    // ];

    public function pobProducts(): HasMany
    {
        return $this->hasMany(POBProduct::class);
    }
    public function getStateNameAttribute(): ?string
    {
        return $this->state?->name;
    }
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['state_name']);
    }

    public function isApproved():bool{
        return $this->state?->isFinalized() ?? false;
    }

    /**
     * Scope a query to only approved POBs.
     */
    public function scopeApproved(Builder $query): Builder
    {
        return $query->whereHas('state', fn($q) => $q->where('category', StateCategory::FINALIZED));
    }

    /**
     * Return count of approved POBs.
     */
    public static function approvedCount(): int
    {
        return static::approved()->count();
    }


}
