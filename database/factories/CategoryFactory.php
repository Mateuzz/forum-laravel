<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
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

        return [
            'title' => $title,
            'slug' => $slug,
            'parent_id' => null,
            'posts_count' => 0,
            'comments_count' => 0,
            'depth' => 0,
        ];
    }
}
