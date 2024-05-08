<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder {
    /**
     * Seed the application's database.
     */
    public function run(): void {
        Schema::disableForeignKeyConstraints();
        User::truncate();
        Category::truncate();
        Post::truncate();
        Schema::enableForeignKeyConstraints();

        $users = User::factory(20)->create()->all();
        $categories = Category::factory(3)->create();

        $subCategories = Category::factory(9)
            ->sequence(fn(Sequence $sequence) => ['parent_id' => rand() % 3])
            ->create(['depth' => 1, 'posts_count' => 10])
            ->all();

        Post::factory(400)
            ->sequence(fn(Sequence $sequence) => [
                'user_id' => $users[array_rand($users)],
                'category_id' => $subCategories[array_rand($subCategories)],
            ])
            ->create();
    }
}
