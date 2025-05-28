<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['name', 'description', 'price', 'division_id'];

    public function division()
    {
        return $this->belongsTo(Division::class);
    }
    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }
    public function kofolEntries()
    {
        return $this->hasMany(KofolEntry::class);
    }
}
