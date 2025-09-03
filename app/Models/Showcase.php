<?php

namespace App\Models;

use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Showcase extends Model
{

    protected $guarded = [];
    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
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
