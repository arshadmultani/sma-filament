<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $guarded = [];

    protected $casts = [
        'rating' => 'integer',
        'is_verified' => 'boolean',
        'review_date' => 'datetime',
        'verification_expires_at' => 'datetime',
        'verified_at' => 'datetime',
    ];

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }
}
