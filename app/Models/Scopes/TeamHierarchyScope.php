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

        // Use the new getSubordinates() method for all role logic
        $userIds = $user->getSubordinates();

        // If the user can see all (admins), don't apply any restriction
        if ($userIds->count() === \App\Models\User::count()) {
            return;
        }

        $builder->whereIn('user_id', $userIds);
    }
}
