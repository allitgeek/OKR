<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class TaskPolicy
{
    public function before(User $user, string $ability): bool|null
    {
        if ($user->hasPermission('view-all-tasks')) {
            return true;
        }
        return null;
    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('view-tasks');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Task $task): bool
    {
        if ($user->hasPermission('view-all-tasks')) {
            return true;
        }
        return $task->creator_id === $user->id || 
               $task->assignee_id === $user->id || 
               $user->hasPermission('view-tasks');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermission('manage-tasks');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Task $task): bool
    {
        if ($user->hasPermission('view-all-tasks')) {
            return true;
        }
        return ($task->creator_id === $user->id || $task->assignee_id === $user->id) && 
               $user->hasPermission('manage-tasks');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Task $task): bool
    {
        if ($user->hasPermission('view-all-tasks')) {
            return true;
        }
        return $task->creator_id === $user->id && $user->hasPermission('manage-tasks');
    }

    /**
     * Determine whether the user can assign the task to anyone.
     */
    public function assign(User $user, Task $task): bool
    {
        return $user->hasPermission('assign-tasks');
    }
}
