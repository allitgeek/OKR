<?php

namespace App\Policies;

use App\Models\Objective;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ObjectivePolicy
{
    public function before(User $user, string $ability): bool|null
    {
        if ($user->hasRole('super-admin')) {
            return true;
        }
        return null;
    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Any authenticated user can view the list of objectives within their company
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Objective $objective): bool
    {
        // Users can view any objective within their own company.
        return $user->company_id === $objective->company_id;
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
        return $user->id === $objective->user_id && $user->hasPermission('manage-objectives');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Objective $objective): bool
    {
        return $user->id === $objective->user_id && $user->hasPermission('manage-objectives');
    }
}
