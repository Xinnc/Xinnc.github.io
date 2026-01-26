<?php

namespace App\Domains\TimeEntry\Policies;

use App\Domains\Shared\Exceptions\ForbiddenForYouException;
use App\Domains\TimeEntry\Model\TimeEntry;
use App\Domains\User\Models\User;

class TimeEntryPolicy
{
    /**
     * Determine whether the user can view any models.
     */

    public function before($user, $ability)
    {
        if($user->role_name === 'admin'){
            return true;
        }
        return null;
    }
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, TimeEntry $timeEntry): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, TimeEntry $timeEntry): bool
    {
        if ($user->id === $timeEntry->user_id) {
            return true;
        }

        throw new ForbiddenForYouException();
    }


    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, TimeEntry $timeEntry): bool
    {
        if($user->role_name === 'moderator'){
            return true;
        }
        throw new ForbiddenForYouException();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, TimeEntry $timeEntry): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, TimeEntry $timeEntry): bool
    {
        return false;
    }
}
