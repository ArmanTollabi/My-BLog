<?php

namespace Database\Factories;

use App\Models\Post;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(),
            'category' => fake()->randomElement([
                'Technology',
                'Programming',
                'Laravel',
                'Design',
                'tutorial',
            ]),
            'user_id' => fake()->numberBetween(9, 32),
            'description' => fake()->paragraph(),

        ];
    }
}
