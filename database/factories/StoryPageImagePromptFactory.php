<?php

namespace Database\Factories;

use App\Models\Story;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\StoryPageImagePrompt>
 */
class StoryPageImagePromptFactory extends Factory
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
            'page_number' => fake()->numberBetween(1, 20),
            'scene_title' => fake()->sentence(4),
            'image_prompt' => fake()->paragraph(3),
            'story_text' => fake()->paragraphs(2, true),
            'emotions' => fake()->sentence(6),
            'art_style' => fake()->randomElement(['watercolor', 'cartoon', 'realistic', 'anime', 'oil painting']),
        ];
    }

    /**
     * Indicate that the page prompt has no emotions.
     */
    public function withoutEmotions(): static
    {
        return $this->state(fn (array $attributes) => [
            'emotions' => null,
        ]);
    }
}
