<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KofolEntry extends Model
{
    protected $fillable = [
        'kofol_campaign_id',
        'user_id',
        'invoice_image',
        'products',
        'customer_type',
        'customer_id',
        'invoice_amount',
        'status',
        'coupon_code',
    ];
    protected $casts = [
        'products' => 'array',
    ];
    public function kofolCampaign()
    {
        return $this->belongsTo(KofolCampaign::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function customer()
    {
        return $this->morphTo();
    }
    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
