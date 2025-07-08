<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class TeamHierarchyScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        /** @var \App\Models\User|null $user */
        $user = \Illuminate\Support\Facades\Auth::user();

        if (! $user) {
            return;
        }

        // Admin and Super-Admin can see all records
        if (method_exists($user, 'hasAnyRole') && $user->hasAnyRole(['admin', 'super_admin'])) {
            return;
        }

        // DSA: only their own records
        if (method_exists($user, 'hasRole') && $user->hasRole('DSA')) {
            $builder->where('user_id', $user->id);
            return;
        }

        // ASM: records of users under his area and same division
        if (method_exists($user, 'hasRole') && $user->hasRole('ASM')) {
            $headquarterIds = \App\Models\Headquarter::where('area_id', $user->location_id)->pluck('id');
            $userIds = \App\Models\User::where('location_type', \App\Models\Headquarter::class)
                ->whereIn('location_id', $headquarterIds)
                ->where('division_id', $user->division_id)
                ->pluck('id');
            // Add own user_id
            $userIds->push($user->id);
            $builder->whereIn('user_id', $userIds);
            return;
        }

        // RSM: records of users under all areas in his region and same division
        if (method_exists($user, 'hasRole') && $user->hasRole('RSM')) {
            $areaIds = \App\Models\Area::where('region_id', $user->location_id)->pluck('id');
            $headquarterIds = \App\Models\Headquarter::whereIn('area_id', $areaIds)->pluck('id');
            $userIds = \App\Models\User::where('location_type', \App\Models\Headquarter::class)
                ->whereIn('location_id', $headquarterIds)
                ->where('division_id', $user->division_id)
                ->pluck('id');
            // Add own user_id
            $userIds->push($user->id);
            $builder->whereIn('user_id', $userIds);
            return;
        }

        // ZSM: records of users under all regions in his zone and same division
        if (method_exists($user, 'hasRole') && $user->hasRole('ZSM')) {
            $regionIds = \App\Models\Region::where('zone_id', $user->location_id)->pluck('id');
            $areaIds = \App\Models\Area::whereIn('region_id', $regionIds)->pluck('id');
            $headquarterIds = \App\Models\Headquarter::whereIn('area_id', $areaIds)->pluck('id');
            $userIds = \App\Models\User::where('location_type', \App\Models\Headquarter::class)
                ->whereIn('location_id', $headquarterIds)
                ->where('division_id', $user->division_id)
                ->pluck('id');
            // Add own user_id
            $userIds->push($user->id);
            $builder->whereIn('user_id', $userIds);
            return;
        }

        // PMT: records created by DSA, ASM, RSM under his division
        if (method_exists($user, 'hasRole') && $user->hasRole(['PMT','GM'])) {
            $userIds = \App\Models\User::where('division_id', $user->division_id)
                ->whereHas('roles', function ($query) {
                    $query->whereIn('name', ['DSA', 'ASM', 'RSM', 'ZSM']);
                })
                ->pluck('id');
            $builder->whereIn('user_id', $userIds);
            return;
        }

        // Default: only their own records
        $builder->where('user_id', $user->id);
    }
}
