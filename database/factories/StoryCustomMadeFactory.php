<?php

namespace Database\Factories;

use App\Models\Story;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\StoryCustomMade>
 */
class StoryCustomMadeFactory extends Factory
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
            'child_name' => fake()->firstName(),
            'child_gender' => fake()->randomElement(['male', 'female', 'other']),
            'child_age' => fake()->numberBetween(1, 12),
            'child_image_url' => null,
            'pdf_final_url' => null,
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
            'pdf_final_url' => fake()->url(),
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
     * Add a child image URL.
     */
    public function withChildImage(): static
    {
        return $this->state(fn (array $attributes) => [
            'child_image_url' => fake()->imageUrl(200, 200, 'people'),
        ]);
    }
}
