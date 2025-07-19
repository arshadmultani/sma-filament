<?php
namespace App\Services;

use Illuminate\Database\Eloquent\Builder;
use App\Models\User;
use App\Contracts\HeadquarterFilterable;

class VisibilityService
{
    public function apply(Builder $query, User $user): Builder
    {
        // DSA: See own + entries for their HQ
        if ($user->hasRole('DSA') && $query->getModel() instanceof HeadquarterFilterable) {
            $hqId = $user->headquarter_id;
            $query->where(function ($q) use ($user, $hqId) {
                $q->where('user_id', $user->id);
                if ($hqId) {
                    $q->orWhere(function ($q2) use ($hqId) {
                        $q2->getModel()->scopeWhereHeadquarterIn($q2, [$hqId]);
                    });
                }
            });
            return $query;
        }

        // ASM: See own + subordinates + entries for their area
        if ($user->hasRole('ASM') && $query->getModel() instanceof HeadquarterFilterable) {
            $subordinateIds = $user->getSubordinates();
            $areaId = $user->area_id; // Use the existing accessor
            
            $query->where(function ($q) use ($user, $subordinateIds, $areaId) {
                $q->where('user_id', $user->id);
                
                if ($subordinateIds && $subordinateIds->isNotEmpty()) {
                    $q->orWhereIn('user_id', $subordinateIds);
                }
                
                if ($areaId) {
                    $q->orWhere(function ($q2) use ($areaId) {
                        $q2->whereLocationIn('area', [$areaId]);
                    });
                }
            });
            return $query;
        }

        // RSM: See own + subordinates + entries for their region
        if ($user->hasRole('RSM') && $query->getModel() instanceof HeadquarterFilterable) {
            $subordinateIds = $user->getSubordinates();
            $regionId = $user->region_id; // Use the existing accessor
            
            $query->where(function ($q) use ($user, $subordinateIds, $regionId) {
                $q->where('user_id', $user->id);
                
                if ($subordinateIds && $subordinateIds->isNotEmpty()) {
                    $q->orWhereIn('user_id', $subordinateIds);
                }
                
                if ($regionId) {
                    $q->orWhere(function ($q2) use ($regionId) {
                        $q2->whereLocationIn('region', [$regionId]);
                    });
                }
            });
            return $query;
        }

        // ZSM: See own + subordinates + entries for their zone
        if ($user->hasRole('ZSM') && $query->getModel() instanceof HeadquarterFilterable) {
            $subordinateIds = $user->getSubordinates();
            $zoneId = $user->zone_id; // Use the existing accessor
            
            $query->where(function ($q) use ($user, $subordinateIds, $zoneId) {
                $q->where('user_id', $user->id);
                
                if ($subordinateIds && $subordinateIds->isNotEmpty()) {
                    $q->orWhereIn('user_id', $subordinateIds);
                }
                
                if ($zoneId) {
                    $q->orWhere(function ($q2) use ($zoneId) {
                        $q2->whereLocationIn('zone', [$zoneId]);
                    });
                }
            });
            return $query;
        }

        // Other roles: use getSubordinates()
        $subordinateIds = $user->getSubordinates();
        if ($subordinateIds && $subordinateIds->isNotEmpty()) {
            $query->whereIn('user_id', $subordinateIds);
        } else {
            $query->where('user_id', $user->id);
        }

        return $query;
    }

    // Optionally, add helper methods for subordinate logic, caching, etc.
}