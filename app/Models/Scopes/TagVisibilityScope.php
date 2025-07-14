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
        $headOfficeRoleIds = User::headOfficeRoleIds();

        if ($user && !$user->roles()->whereIn('id', $headOfficeRoleIds)->exists()) {
            $builder->whereHas('divisions', function ($q) use ($user) {
                $q->where('division_id', $user->division_id);
            });
        }
    }
}
