<?php
namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class TagVisibilityScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        $user = Auth::user();
        
        // Early return for guests
        if (!$user) {
            return $builder->whereRaw('0=1');
        }
        
        // Cache user data to avoid repeated queries
        $headOfficeRoleIds = User::headOfficeRoleIds();
        $userRoleIds = $user->roles->pluck('id');
        
        // Early return for head office users (no restrictions)
        if ($userRoleIds->intersect($headOfficeRoleIds)->isNotEmpty()) {
            return;
        }
        
        // Apply restrictions for non-head office users
        if (!$user->division_id || $userRoleIds->isEmpty()) {
            return $builder->whereRaw('0=1');
        }
        
        // Use whereHas for decoupling - Laravel optimizes this internally
        $builder->whereHas('divisions', function ($query) use ($user) {
            $query->where('divisions.id', $user->division_id);
        })->whereHas('roles', function ($query) use ($userRoleIds) {
            $query->whereIn('roles.id', $userRoleIds->toArray());
        });
    }
}