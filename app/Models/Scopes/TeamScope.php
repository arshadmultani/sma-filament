<?php

namespace App\Models\Scopes;

use App\Models\Headquarter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

class TeamScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        $user = Auth::user();

        if (! $user) {
            return;
        }

        // Admin and Super-Admin can see all records
        /* */
        if ($user->hasAnyRole(['admin', 'super_admin'])) {
            return;
        }

        // DSA can only see their own records + same headquarter
        if ($user->hasRole('DSA')) {
            $builder->where(function ($query) use ($user) {
                $query->where('user_id', $user->id)
                    ->orWhere('headquarter_id', $user->location_id);
                // LATER ADD DIVISION FILTER TOO

            });

            return;
        }

        // ASM logic
        if ($user->hasRole('ASM')) {
            $headquarterIds = Headquarter::where('area_id', $user->location_id)->pluck('id');
            $builder->whereIn('headquarter_id', $headquarterIds);

            return;
        }

        // RSM logic (adjust as needed)
        if ($user->hasRole('RSM')) {
            $areaIds = \App\Models\Area::where('region_id', $user->location_id)->pluck('id');
            $headquarterIds = Headquarter::whereIn('area_id', $areaIds)->pluck('id');
            $builder->whereIn('headquarter_id', $headquarterIds);

            return;
        }

        // Default: user can only see their own records
        $builder->where('user_id', $user->id);
    }
}
