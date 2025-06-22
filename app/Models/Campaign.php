<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Campaign extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

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
}
