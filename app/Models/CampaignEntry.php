<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CampaignEntry extends Model
{
    protected $fillable = ['campaign_id', 'customer_id','customer_type','entry_id','entry_type'];

    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }

    public function customer()
    {
        return $this->morphTo();
    }

    public function entry()
    {
        return $this->morphTo();
    }
}
