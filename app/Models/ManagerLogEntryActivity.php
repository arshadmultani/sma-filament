<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ManagerLogEntryActivity extends Model
{
    protected $guarded = [];
    public $timestamps = false;

    public function colleague(){
        return $this->belongsTo(ManagerLogEntryColleague::class);
    }
    public function callInputs()
    {
        return $this->belongsToMany(CallInput::class, 'manager_log_entry_activity_call_input');
    }
    public function customer()
    {
        return $this->morphTo();
    }

    public function products()
    {
        return $this->hasMany(ManagerLogEntryActivityProduct::class);
    }
}
