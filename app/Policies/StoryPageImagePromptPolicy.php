<?php

namespace App\Policies;

use App\Models\Story;
use App\Models\StoryPageImagePrompt;
use App\Models\User;

class StoryPageImagePromptPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user, Story $story): bool
    {
        return $user->id === $story->user_id;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, StoryPageImagePrompt $storyPageImagePrompt): bool
    {
        return $user->id === $storyPageImagePrompt->user_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user, Story $story): bool
    {
        return $user->id === $story->user_id;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, StoryPageImagePrompt $storyPageImagePrompt): bool
    {
        return $user->id === $storyPageImagePrompt->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, StoryPageImagePrompt $storyPageImagePrompt): bool
    {
        return $user->id === $storyPageImagePrompt->user_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, StoryPageImagePrompt $storyPageImagePrompt): bool
    {
        return $user->id === $storyPageImagePrompt->user_id;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, StoryPageImagePrompt $storyPageImagePrompt): bool
    {
        return $user->id === $storyPageImagePrompt->user_id;
    }
}
