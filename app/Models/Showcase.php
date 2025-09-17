<?php

namespace App\Models;

use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Showcase extends Model
{

    protected $guarded = [];
    protected $appends = ['media_file_url'];

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    public function getMediaFileUrlAttribute()
    {
        if ($this->media_url && Storage::disk('s3')->exists($this->media_url)) {
            return Storage::temporaryUrl($this->media_url, now()->addMinutes(5));
        }

        return null;
    }

    protected static function booted()
    {
        parent::booted();

        static::deleting(function ($showcase) {
            if ($showcase->media_url) {
                Storage::delete($showcase->media_url);
            }
        });
    }
}
