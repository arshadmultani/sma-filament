<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

class CampaignVisibilityScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        $user = Auth::user();

        // Only apply if user is authenticated and not super_admin/admin
        if ($user && !$user->hasRole(['super_admin', 'admin'])) {
            $divisionId = $user->division_id;
            $roleIds = $user->roles()->pluck('id')->toArray();

            $builder
                ->whereHas('divisions', function ($q) use ($divisionId) {
                    $q->where('divisions.id', $divisionId);
                })
                ->whereHas('roles', function ($q) use ($roleIds) {
                    $q->whereIn('roles.id', $roleIds);
                });
        }
    }
}
