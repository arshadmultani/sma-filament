<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    /** @use HasFactory<\Database\Factories\ReviewFactory> */
    use HasFactory;
    protected $fillable = ['doctor_id', 'reviewer_name', 'video', 'comment', 'rating'];

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }
}
