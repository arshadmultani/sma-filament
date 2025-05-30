<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Doctor extends Model
{
        protected $fillable = ['name', 'email', 'phone', 'qualification_id', 'profile_photo', 'user_id', 'headquarter_id', 'attachment', 'address'];

    protected $casts = [
        'attachment' => 'array',
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }

   public function headquarter(){
    return $this->belongsTo(Headquarter::class);
   }

 public function kofolEntries()
    {
        return $this->morphMany(KofolEntry::class, 'customer');
    }

    public function qualification()
    {
        return $this->belongsTo(Qualification::class);
    }
}
