<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Doctor extends Model
{
    protected $fillable = ['name', 'email', 'phone', 'degree', 'profile_photo', 'user_id'];

    public function user(){
        return $this->belongsTo(User::class);
    }
}
