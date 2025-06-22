<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CampaignStatus extends Model
{
    protected $fillable = ['name', 'is_active'];
    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function campaigns()
    {
        return $this->hasMany(Campaign::class, 'status_id');
    }
    
}
