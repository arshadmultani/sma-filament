<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class POBProduct extends Pivot
{
    public $incrementing = true;

    public $timestamps = false;

    // protected $table = 'pob_product';

    public function pob(): BelongsTo
    {
        return $this->belongsTo(POB::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
