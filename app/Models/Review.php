<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Review extends Model
{
    protected $guarded = [];

    protected $appends = ['media_file_url'];

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

    public function state()
    {
        return $this->belongsTo(State::class);
    }

    public function getMediaFileUrlAttribute()
    {
        if ($this->media_url && Storage::disk('s3')->exists($this->media_url)) {
            return Storage::temporaryUrl($this->media_url, now()->addMinutes(5));
        }

        return null;
    }
}
