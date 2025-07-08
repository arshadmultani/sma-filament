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
        if (method_exists($user, 'hasAnyRole') && $user->hasAnyRole(['admin', 'super_admin'])) {
            return;
        }

        // DSA can only see their own records + same headquarter
        if (method_exists($user, 'hasRole') && $user->hasRole('DSA')) {
            $builder->where(function ($query) use ($user) {
                $query->where('user_id', $user->id)
                    ->orWhere('headquarter_id', $user->location_id);
                // LATER ADD DIVISION FILTER TOO

            });

            return;
        }

        // ASM logic
        if (method_exists($user, 'hasRole') && $user->hasRole('ASM')) {
            $headquarterIds = Headquarter::where('area_id', $user->location_id)->pluck('id');
            $builder->whereIn('headquarter_id', $headquarterIds);

            return;
        }

        // RSM logic (adjust as needed)
        if (method_exists($user, 'hasRole') && $user->hasRole('RSM')) {
            $areaIds = \App\Models\Area::where('region_id', $user->location_id)->pluck('id');
            $headquarterIds = Headquarter::whereIn('area_id', $areaIds)->pluck('id');
            $builder->whereIn('headquarter_id', $headquarterIds);

            return;
        }
        // ZSM logic
        if (method_exists($user, 'hasRole') && $user->hasRole('ZSM')) {
            // Get all region IDs under the ZSM's zone
            $regionIds = \App\Models\Region::where('zone_id', $user->location_id)->pluck('id');
            // Get all area IDs under those regions
            $areaIds = \App\Models\Area::whereIn('region_id', $regionIds)->pluck('id');
            // Get all headquarter IDs under those areas
            $headquarterIds = Headquarter::whereIn('area_id', $areaIds)->pluck('id');
            // Filter records by those headquarter IDs
            $builder->whereIn('headquarter_id', $headquarterIds);
            return;
        }
        // PMT: records created by DSA, ASM, RSM under his division
        if (method_exists($user, 'hasRole') && $user->hasRole(['PMT','GM'])) {
            $userIds = \App\Models\User::where('division_id', $user->division_id)
                ->whereHas('roles', function ($query) {
                    $query->whereIn('name', ['DSA', 'ASM', 'RSM']);
                })
                ->pluck('id');
            $builder->whereIn('user_id', $userIds);
            return;
        }
        // Default: user can only see their own records
        $builder->where('user_id', $user->id);
    }
}
