<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ManagerLogEntryColleague extends Model
{

    protected $guarded = [];
    public $timestamps = false;

    public function logEntry()
    {
        return $this->belongsTo(ManagerLogEntry::class);
    }

    public function activities()
    {
        return $this->hasMany(ManagerLogEntryActivity::class);
    }
    public function user()
{
    return $this->belongsTo(User::class);
}
}
