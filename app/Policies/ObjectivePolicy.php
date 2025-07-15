<?php

namespace App\Policies;

use App\Models\Objective;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ObjectivePolicy
{
    public function before(User $user, string $ability): bool|null
    {
        if ($user->hasPermission('view-all-objectives')) {
            return true;
        }
        return null;
    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('view-objectives');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Objective $objective): bool
    {
        if ($user->hasPermission('view-all-objectives')) {
            return true;
        }
        return $objective->user_id === $user->id || $user->hasPermission('view-objectives');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermission('manage-objectives');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Objective $objective): bool
    {
        if ($user->hasPermission('view-all-objectives')) {
            return true;
        }
        return $objective->user_id === $user->id && $user->hasPermission('manage-objectives');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Objective $objective): bool
    {
        if ($user->hasPermission('view-all-objectives')) {
            return true;
        }
        return $objective->user_id === $user->id && $user->hasPermission('manage-objectives');
    }
}
