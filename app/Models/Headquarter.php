<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Headquarter extends Model
{
    protected $fillable = ['name', 'area_id'];

    public function area()
    {
        return $this->belongsTo(Area::class);
    }

    public function users()
    {
        return $this->morphMany(User::class, 'location');
    }

    public function chemists()
    {
        return $this->hasMany(Chemist::class);
    }
    public function doctors()
    {
        return $this->hasMany(Doctor::class);
    }
}
