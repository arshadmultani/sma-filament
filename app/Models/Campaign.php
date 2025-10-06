<?php

namespace App\Models;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Division;
use Illuminate\Support\Collection;
use Spatie\Permission\Models\Role;
use App\Observers\CamapaignObserver;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;
use App\Models\Scopes\CampaignVisibilityScope;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;

#[ObservedBy([CamapaignObserver::class])]
class Campaign extends Model
{
    use HasFactory;
    // TODO:Drop is_active column, it is redundant with start_date and end_date
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
        return $this->belongsToMany(Division::class, 'campaign_division');
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'campaign_role');
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
        $headOfficeRoleNames = Role::whereIn('id', User::headOfficeRoleIds())->pluck('name')->toArray();
        return $this->roles
            ->pluck('name')
            ->reject(fn($role) => in_array($role, $headOfficeRoleNames))
            ->values()
            ->all();
    }

    public function scopeActive($query)
    {
        return $query->where('start_date', '<=', now())
            ->where('end_date', '>=', now());
    }

    /**
     * Scope to get campaigns by entry type and active status
     */
    public function scopeForEntryType($query, string $entryType)
    {
        return $query->where('allowed_entry_type', $entryType)
            ->where('is_active', true);
    }

    public static function getActiveCampaigns(): Collection
    {
        return self::where('is_active', true)->get(['id', 'name', 'allowed_entry_type']);
    }

    public static function getCacheKeyForEntryType(string $entryType): string
    {
        return "campaigns_for_entry_type_{$entryType}" . now()->format('Y-m-d-H-i-s');
    }
    public static function getForEntryType(string $entryType)
    {
        $cacheKey = self::getCacheKeyForEntryType($entryType);

        return Cache::remember($cacheKey, now()->addDays(1), function () use ($entryType) {
            return static::forEntryType($entryType)->pluck('name', 'id');
        });
    }
}
