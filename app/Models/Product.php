<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['name', 'price', 'division_id'];

    public function division()
    {
        return $this->belongsTo(Division::class);
    }
}
