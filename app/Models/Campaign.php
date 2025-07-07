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
}
