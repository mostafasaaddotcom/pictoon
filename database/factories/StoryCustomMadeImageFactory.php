<?php

namespace Database\Factories;

use App\Models\Story;
use App\Models\StoryCustomMade;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\StoryCustomMadeImage>
 */
class StoryCustomMadeImageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'story_id' => Story::factory(),
            'story_custom_made_id' => StoryCustomMade::factory(),
            'page_number' => fake()->numberBetween(1, 20),
            'image_type' => fake()->randomElement(['page', 'cover_front', 'cover_back']),
            'reference_number' => null,
            'image_url' => null,
            'status' => 'pending',
        ];
    }

    /**
     * Indicate that the status is pending.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
        ]);
    }

    /**
     * Indicate that the status is processing.
     */
    public function processing(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'processing',
        ]);
    }

    /**
     * Indicate that the status is completed.
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
            'image_url' => fake()->imageUrl(800, 600),
        ]);
    }

    /**
     * Indicate that the status is failed.
     */
    public function failed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'failed',
        ]);
    }

    /**
     * Set as page image.
     */
    public function page(int $pageNumber = 1): static
    {
        return $this->state(fn (array $attributes) => [
            'image_type' => 'page',
            'page_number' => $pageNumber,
        ]);
    }

    /**
     * Set as front cover image.
     */
    public function coverFront(): static
    {
        return $this->state(fn (array $attributes) => [
            'image_type' => 'cover_front',
            'page_number' => 0,
        ]);
    }

    /**
     * Set as back cover image.
     */
    public function coverBack(): static
    {
        return $this->state(fn (array $attributes) => [
            'image_type' => 'cover_back',
            'page_number' => 0,
        ]);
    }
}
