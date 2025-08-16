<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Workflow extends Model
{
    protected $guarded = [];

    public function transitions()
    {
        return $this->hasMany(WorkflowTransition::class);
    }
}
