<?php

namespace App\Policies;

use App\Models\StoryCustomMade;
use App\Models\StoryCustomMadeImage;
use App\Models\User;

class StoryCustomMadeImagePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user, StoryCustomMade $customMade): bool
    {
        return $user->id === $customMade->user_id;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, StoryCustomMadeImage $image): bool
    {
        return $user->id === $image->user_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user, StoryCustomMade $customMade): bool
    {
        return $user->id === $customMade->user_id;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, StoryCustomMadeImage $image): bool
    {
        return $user->id === $image->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, StoryCustomMadeImage $image): bool
    {
        return $user->id === $image->user_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, StoryCustomMadeImage $image): bool
    {
        return $user->id === $image->user_id;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, StoryCustomMadeImage $image): bool
    {
        return $user->id === $image->user_id;
    }
}
