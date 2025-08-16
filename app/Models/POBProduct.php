<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class POBProduct extends Pivot
{
    public $incrementing = true;

    // protected $table = 'pob_product';

    public function pob()
    {
        return $this->belongsTo(POB::class, 'pob_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
