<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KofolEntryCoupon extends Model
{
    protected $fillable = [
        'kofol_entry_id',
        'coupon_code',
    ];

    public function kofolEntry()
    {
        return $this->belongsTo(KofolEntry::class);
    }
} 