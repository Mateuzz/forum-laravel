<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder {
    /**
     * Seed the application's database.
     */
    public function run(): void {
        // User::factory(10)->create();

        Schema::disableForeignKeyConstraints();
        User::truncate();
        Category::truncate();
        Post::truncate();
        Schema::enableForeignKeyConstraints();

        $users = User::factory(20)->create();
        $categories = Category::factory(3)->create();
        $categories2 = [];

        for ($i = 0; $i < 9; ++$i) {
            $categories2[] = Category::factory()->create(['parent_id' => rand() % 3 + 1, 'depth' => 1, 'posts_count' => 20 * 2]);
        }

        foreach ($categories2 as $c) {
            foreach ($users as $user) {
                for ($i = 0; $i < 2; ++$i) {
                    Post::factory()->create([
                            'user_id' => $user->id,
                            'category_id' => $c->id,
                    ]);
                }
            }
        }
    }
}
