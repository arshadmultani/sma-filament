<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class TeamHierarchyScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        if (!Auth::check()) return;

        $user = Auth::user();
        $builder->where(function ($query) use ($user, $model) {
            // Always see own records
            $query->where('user_id', $user->id);

            // Get subordinates (if any)
            $subordinates = $this->getCachedSubordinates($user);
            if (!empty($subordinates)) {
                $query->orWhereIn('user_id', $subordinates);
            }

            // Role-specific location filtering
            if ($user->hasRole('DSA') && $user->headquarter_id) {
                $this->filterByLocation($query, $model, 'headquarter', $user->headquarter_id);
            } elseif ($user->hasRole('ASM') && $user->area_id) {
                $this->filterByLocation($query, $model, 'area', $user->area_id);
            } elseif ($user->hasRole('RSM') && $user->region_id) {
                $this->filterByLocation($query, $model, 'region', $user->region_id);
            } elseif ($user->hasRole('ZSM') && $user->zone_id) {
                $this->filterByLocation($query, $model, 'zone', $user->zone_id);
            }
        });
    }

    protected function filterByLocation(Builder $query, Model $model, string $type, int $id)
    {
        // Simple switch for model types
        if ($model instanceof \App\Models\KofolEntry) {
            $query->orWhereHasMorph('customer', [\App\Models\Doctor::class, \App\Models\Chemist::class], function ($q) use ($type, $id) {
                if ($type === 'headquarter') $q->where('headquarter_id', $id);
                if ($type === 'area') $q->whereHas('headquarter', fn($hq) => $hq->where('area_id', $id));
                if ($type === 'region') $q->whereHas('headquarter.area', fn($a) => $a->where('region_id', $id));
                if ($type === 'zone') $q->whereHas('headquarter.area.region', fn($r) => $r->where('zone_id', $id));
            });
        } elseif ($model instanceof \App\Models\Microsite) {
            $query->orWhereHas('doctor', function ($q) use ($type, $id) {
                if ($type === 'headquarter') $q->where('headquarter_id', $id);
                if ($type === 'area') $q->whereHas('headquarter', fn($hq) => $hq->where('area_id', $id));
                if ($type === 'region') $q->whereHas('headquarter.area', fn($a) => $a->where('region_id', $id));
                if ($type === 'zone') $q->whereHas('headquarter.area.region', fn($r) => $r->where('zone_id', $id));
            });
        }
        // Add more models here as needed
    }

    protected function getCachedSubordinates($user)
    {
        return Cache::remember("user_{$user->id}_subordinates", 3600, function () use ($user) {
            return $user->getSubordinates()->pluck('id')->toArray();
        });
    }
}
