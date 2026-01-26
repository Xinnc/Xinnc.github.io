<?php

namespace App\Domains\Task\Policies;

use App\Domains\Project\Model\Project;
use App\Domains\Shared\Exceptions\ForbiddenForYouException;
use App\Domains\Task\Model\Task;
use App\Domains\User\Models\User;

class TaskPolicy
{
    /**
     * Determine whether the user can view any models.
     */

    public function before(User $user, $ability){
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
    public function view(User $user, Task $task): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        if($user->role_name === 'manager'){
            return true;
        }
        throw new ForbiddenForYouException();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Project $project): bool
    {
        if($user->role_name === 'manager') {
            return $project->manager_id === $user->id;
        }
        throw new ForbiddenForYouException();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Project $project): bool
    {
        if($user->role_name === 'manager') {
            return $project->manager_id === $user->id;
        }
        throw new ForbiddenForYouException();

    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Task $task): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Task $task): bool
    {
        return false;
    }

    public function updateStatus(User $user, Task $task): bool
    {
        //в идеали проеверять, кому выдано задание
        return true;
    }
}
