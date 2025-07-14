<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CallInput extends Model
{

    protected $fillable = ['name'];

    public function managerLogEntryActivities()
    {
        return $this->belongsToMany(ManagerLogEntryActivity::class);
    }
}
