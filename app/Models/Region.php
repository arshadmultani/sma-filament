<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
    protected $fillable = ['name'];

    public function areas(){
        return $this->hasMany(Area::class);
    }
    public function users(){
        return $this->morphMany(User::class, 'location');
    }
}
