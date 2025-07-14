<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ManagerLogEntryActivityProduct extends Model
{
    protected $guarded = [];
    public $timestamps = false;
    public function activity()
    {
        return $this->belongsTo(ManagerLogEntryActivity::class);
    }
    public function product()
{
    return $this->belongsTo(Product::class);
}
}
