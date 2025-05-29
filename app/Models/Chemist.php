<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Chemist extends Model
{
    protected $fillable = ['name', 'phone', 'email', 'address'];

    public function headquarter(){
        return $this->belongsTo(Headquarter::class);
    }

 public function kofolEntries()
    {
        return $this->morphMany(KofolEntry::class, 'customer');
    }
}
