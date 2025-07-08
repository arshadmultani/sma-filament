<?php

namespace App\Policies;

use App\Models\User;
use App\Models\ConversionActivity;
use Illuminate\Auth\Access\HandlesAuthorization;

class ConversionActivityPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_conversion::activity');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ConversionActivity $conversionActivity): bool
    {
        return $user->can('view_conversion::activity');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_conversion::activity');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ConversionActivity $conversionActivity): bool
    {
        return $user->can('update_conversion::activity');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ConversionActivity $conversionActivity): bool
    {
        return $user->can('delete_conversion::activity');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_conversion::activity');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, ConversionActivity $conversionActivity): bool
    {
        return $user->can('force_delete_conversion::activity');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_conversion::activity');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, ConversionActivity $conversionActivity): bool
    {
        return $user->can('restore_conversion::activity');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_conversion::activity');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, ConversionActivity $conversionActivity): bool
    {
        return $user->can('replicate_conversion::activity');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_conversion::activity');
    }
}
