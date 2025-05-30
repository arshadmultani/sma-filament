<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Chemist extends Model
{
    protected $fillable = ['name', 'phone', 'email', 'address', 'headquarter_id'];

    public function headquarter(){
        return $this->belongsTo(Headquarter::class);
    }

 public function kofolEntries()
    {
        return $this->morphMany(KofolEntry::class, 'customer');
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
}
