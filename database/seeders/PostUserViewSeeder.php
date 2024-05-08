<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Post;
use App\Models\PostUserView;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;

class PostUserViewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $posts = Post::factory(100)->for(Category::factory())->create();
        $posts = $posts->all();
        PostUserView::factory(5000)
            ->sequence(fn (Sequence $sequence) =>
                ['post_id' => $posts[rand() % 100]]
            )
            ->create();
    }
}
