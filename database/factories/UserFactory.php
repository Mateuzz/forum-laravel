<?php

namespace Database\Factories;

use App\Models\UserStatus;
use App\Models\UserType;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory {
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array {
        $imageNumber = rand() % 1000;
        $image = "https://picsum.photos/id/$imageNumber/200/200";

        return [
            'username' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'status' => UserStatus::Online,
            'type' => UserType::User,
            'image' => $image,
            'donations' => fake()->randomNumber(),
            'posts_published' => fake()->randomNumber(),
            'posts_views_received' => fake()->randomNumber(),
            'stars_received' => fake()->randomNumber(),
            'password' => static::$password ??= 'password',
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
