<?php

namespace Database\Factories;

use App\Models\Story;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\StoryCoverImagePrompt>
 */
class StoryCoverImagePromptFactory extends Factory
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
            'type' => fake()->randomElement(['front', 'back']),
            'image_prompt' => fake()->paragraph(),
            'meta_data' => null,
        ];
    }

    /**
     * Indicate that this is a front cover.
     */
    public function front(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'front',
        ]);
    }

    /**
     * Indicate that this is a back cover.
     */
    public function back(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'back',
        ]);
    }

    /**
     * Add meta data to the cover.
     */
    public function withMetaData(array $data): static
    {
        return $this->state(fn (array $attributes) => [
            'meta_data' => $data,
        ]);
    }
}
