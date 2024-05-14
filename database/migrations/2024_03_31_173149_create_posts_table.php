<?php

use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('title', Post::MAX_TITLE_SIZE);
            $table->string('slug', Post::MAX_SLUG_SIZE);
            $table->text('body');
            $table->string('excerpt', Post::MAX_EXCERPT_SIZE);
            $table->integer('views_count')->default(0);
            $table->integer('unique_views_count')->default(0);
            $table->integer('comments_count')->default(0);
            $table->integer('stars')->default(0);
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->index(['created_at']);
            $table->index(['category_id']);
            $table->index(['user_id']);
        });

        DB::statement("alter table posts
            add column search tsvector
                generated always as (
                    setweight( to_tsvector('simple'::regconfig, title ), 'A') ||
                    setweight( to_tsvector('simple'::regconfig, body ), 'B')
                ) stored;
        ");

        DB::statement("CREATE INDEX post_search_idx ON posts USING GIN (search);");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('posts');
    }
};
