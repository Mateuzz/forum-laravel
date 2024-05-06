<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
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
        $title = $this->faker->text(50);
        $slug = $this->faker->text(50);
        $body = $this->faker->paragraphs(10, true);
        $excerpt = substr($body, 0, 200);

        return [
            'title' => $title,
            'slug' => $slug,
            'body' => $body,
            'excerpt' => $excerpt,
            'views_count' => 0,
            'unique_views_count' => 0,
            'comments_count' => 0,
            'stars' => 0,
        ];
    }
}
