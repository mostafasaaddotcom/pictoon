<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Story>
 */
class StoryFactory extends Factory
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
            'idea' => fake()->sentence(),
            'description' => fake()->paragraphs(3, true),
            'moral_lesson' => fake()->sentence(),
            'language' => fake()->randomElement(['en', 'ar', 'fr', 'es', 'de']),
            'pages_count' => fake()->numberBetween(5, 30),
            'images' => [
                fake()->imageUrl(640, 480, 'story'),
                fake()->imageUrl(640, 480, 'story'),
            ],
            'is_active' => true,
        ];
    }

    /**
     * Indicate that the story is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * Indicate that the story has no images.
     */
    public function withoutImages(): static
    {
        return $this->state(fn (array $attributes) => [
            'images' => [],
        ]);
    }
}
