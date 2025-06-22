<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
    protected $fillable = ['name', 'description', 'start_date', 'end_date', 'status_id'];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function status()
    {
        return $this->belongsTo(CampaignStatus::class, 'status_id');
    }

    public function entries()
    {
        return $this->hasMany(CampaignEntry::class);
    }
}
