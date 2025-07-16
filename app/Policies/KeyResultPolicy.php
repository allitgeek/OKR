<?php

namespace App\Policies;

use App\Models\KeyResult;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class KeyResultPolicy
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
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, KeyResult $keyResult): bool
    {
        return $user->company_id === $keyResult->objective->company_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // This is typically handled by checking if the user can update the parent objective
        return $user->hasPermission('manage-objectives');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, KeyResult $keyResult): bool
    {
        // Only the owner of the parent objective can edit the KR's definition
        return $user->id === $keyResult->objective->user_id &&
               $user->hasPermission('manage-key-results');
    }

    /**
     * Determine whether the user can update the progress of the model.
     */
    public function updateProgress(User $user, KeyResult $keyResult): bool
    {
        // The objective owner or the KR assignee can update its progress
        return ($user->id === $keyResult->objective->user_id || $user->id === $keyResult->assignee_id) &&
               $user->hasPermission('manage-key-results');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, KeyResult $keyResult): bool
    {
        return $user->id === $keyResult->objective->user_id &&
               $user->hasPermission('manage-key-results');
    }
} 