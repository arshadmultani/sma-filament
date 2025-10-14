<?php

namespace App\Models;

use App\Enums\MeritType;
use Illuminate\Database\Eloquent\Model;

class Merit extends Model
{
    protected $guarded = [];

    protected $casts = [
        'type' => MeritType::class,
        'display_order' => 'integer',
        'year' => 'integer',
    ];

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }
}
