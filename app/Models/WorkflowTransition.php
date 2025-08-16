<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Role;

class WorkflowTransition extends Model
{
    protected $guarded = [];

    public function fromStatus()
    {
        return $this->belongsTo(Status::class, 'from_status_id');
    }

    public function toStatus()
    {
        return $this->belongsTo(Status::class, 'to_status_id');
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_workflow_transition');
    }
}
