<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MicrositeTemplate extends Model
{
    protected $guarded = [];

    protected $casts = [
        'properties' => 'array',
        'is_default' => 'boolean',
        'is_active' => 'boolean'
    ];

    public function microsites()
    {
        return $this->hasMany(Microsite::class);
    }
}
