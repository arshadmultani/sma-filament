<?php

namespace App\Traits;

use App\Models\Status;
use App\Models\Workflow;
use App\Models\WorkflowTransition;
use Illuminate\Support\Facades\Auth;

trait HasWorkflow
{
    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    public function getWorkflow(): ?Workflow
    {
        $morphAlias = $this->getMorphClass();

        return cache()->rememberForever('workflow for.'.$morphAlias, function () use ($morphAlias) {
            return Workflow::where('model_type', $morphAlias)->where('is_active', true)->first();
        });
    }

    public function getAvailableTransitions()
    {
        $user = Auth::user();
        if (! $user) {
            return collect();
        }

        $workflow = $this->getWorkflow();
        if (! $workflow) {
            return collect();
        }

        return $workflow->transitions()->with('roles')->where('from_status_id', $this->status_id)
            ->get()
            // The filtering logic now needs to be changed
            ->filter(function (WorkflowTransition $transition) use ($user) {
                // Get the names of all roles allowed for this transition
                $allowedRoles = $transition->roles->pluck('name');

                // If no roles are set for some reason, deny access.
                if ($allowedRoles->isEmpty()) {
                    return false;
                }

                // Check if the user has ANY of the required roles
                return $user->hasAnyRole($allowedRoles);
            });
    }

    public function transitionTo(Status $newStatus)
    {
        $this->status_id = $newStatus->id;
        $this->save();
    }
}
