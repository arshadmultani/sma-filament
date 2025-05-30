<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Qualification extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'category'];

    public function doctors()
    {
        return $this->belongsToMany(Doctor::class);
    }
}
