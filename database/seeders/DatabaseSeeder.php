<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Post;
use App\Models\PostUserView;
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
        PostUserView::truncate();
        Schema::enableForeignKeyConstraints();

        $users = User::factory(20)->create()->all();
        $categories = Category::factory(3)->create()->all();

        $postsCount = 400;
        $subCategoriesCount = 10;
        $postsPerCategory = $postsCount / $subCategoriesCount;

        $subCategories = Category::factory($subCategoriesCount)
            ->sequence(fn(Sequence $sequence) => ['parent_id' => $categories[array_rand($categories)]])
            ->create(['depth' => 1, 'posts_count' => $postsPerCategory])
            ->all();

        $posts = Post::factory($postsCount)
            ->sequence(fn(Sequence $sequence) => [
                'user_id' => $users[array_rand($users)],
                'category_id' => $sequence->index % $subCategoriesCount + 1,
            ])
            ->create()
            ->all();

        PostUserView::factory(200)
            ->sequence(fn (Sequence $sequence) =>
                ['post_id' => $posts[rand() % 100]]
            )
            ->create();
    }
}
