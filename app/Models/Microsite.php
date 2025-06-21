<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Microsite extends Model
{
    protected $fillable = ['doctor_id', 'url', 'is_active', 'status'];

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    public function reviews()
    {
        return $this->doctor()->reviews();
    }
    
}
