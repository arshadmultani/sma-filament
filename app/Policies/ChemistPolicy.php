<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Chemist;
use Illuminate\Auth\Access\HandlesAuthorization;

class ChemistPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_chemist');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Chemist $chemist): bool
    {
        return $user->can('view_chemist');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_chemist');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Chemist $chemist): bool
    {
        return $user->can('update_chemist');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Chemist $chemist): bool
    {
        return $user->can('delete_chemist');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_chemist');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, Chemist $chemist): bool
    {
        return $user->can('force_delete_chemist');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_chemist');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, Chemist $chemist): bool
    {
        return $user->can('restore_chemist');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_chemist');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, Chemist $chemist): bool
    {
        return $user->can('replicate_chemist');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_chemist');
    }

    //Determine whether the user can update the status of the chemist to be viewable in campaigns
    //This is a special permission that is not related to the other permissions
    public function updateStatus(User $user, Chemist $chemist): bool
    {
        return $user->can('update_status_chemist');
    }

}
