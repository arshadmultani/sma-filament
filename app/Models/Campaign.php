<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Models\Scopes\CampaignVisibilityScope;

class Campaign extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];
    protected static function booted()
    {
        static::addGlobalScope(new CampaignVisibilityScope);
    }
    protected function status(): Attribute
    {
        return Attribute::make(
            get: function () {
                $today = now()->startOfDay();

                if ($today->lt($this->start_date)) {
                    return 'Upcoming';
                }

                if ($today->gt($this->end_date)) {
                    return 'Completed';
                }

                if ($today->between($this->start_date, $this->end_date)) {
                    return 'Active';
                }

                return 'Unknown';
            },
        );
    }

    public function entries()
    {
        return $this->hasMany(CampaignEntry::class);
    }

    public function divisions()
    {
        return $this->belongsToMany(\App\Models\Division::class, 'campaign_division');
    }

    public function roles()
    {
        return $this->belongsToMany(\Spatie\Permission\Models\Role::class, 'campaign_role');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'campaign_tag')->withTimestamps();
    }

    public function getRelationsToCheckForDelete(): array
    {
        // Only check for campaign entries, not divisions or roles
        return ['entries'];
    }
    public function getParticipantsAttribute()
{
    $headOfficeRoleNames = \Spatie\Permission\Models\Role::whereIn('id', \App\Models\User::headOfficeRoleIds())->pluck('name')->toArray();
    return $this->roles
        ->pluck('name')
        ->reject(fn($role) => in_array($role, $headOfficeRoleNames))
        ->values()
        ->all();
}

    /**
     * Scope to get campaigns by entry type and active status
     */
    public function scopeForEntryType($query, string $entryType)
    {
        return $query->where('allowed_entry_type', $entryType)
                    ->where('is_active', true);
    }

    /**
     * Get campaigns for entry type with caching
     */
    public static function getForEntryType(string $entryType, int $cacheMinutes = 30)
    {
        $cacheKey = "campaigns_for_entry_type_{$entryType}";
        
        return \Illuminate\Support\Facades\Cache::remember($cacheKey, $cacheMinutes * 60, function () use ($entryType) {
            return static::forEntryType($entryType)->pluck('name', 'id');
        });
    }
}
