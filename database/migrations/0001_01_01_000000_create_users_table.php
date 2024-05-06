<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('users', function (Blueprint $table) {
            $table->id()->primary();

            $table->string('username', User::MAX_USERNAME_SIZE);
            $table->string('email', User::MAX_EMAIL_SIZE)->unique();
            $table->string('password', 128);
            $table->string('image', User::MAX_IMAGE_URL_SIZE)->nullable();

            $table->enum('status', ['online', 'offline'])->default('online');
            $table->enum('type', ['admin', 'user'])->default('user');

            $table->decimal('donations', 12, 2)->default(0);
            $table->integer('posts_published')->unsigned()->default(0);
            $table->integer('posts_views_received')->unsigned()->default(0);
            $table->integer('stars_received')->unsigned()->default(0);
            $table->timestamp('email_verified_at')->nullable();
            $table->timestamp('last_activity')->default('now()');
            $table->rememberToken();
            $table->timestamps();

            $table->index('created_at');
            $table->index('email');
            $table->index('posts_published');
            $table->index('posts_views_received');
            $table->index('donations');
            $table->index('last_activity');
            $table->index('status');
            /* $table->index('type'); */
        });

        /* DB::statement('alter table users add column username citext not null'); */
        /* DB::statement("CREATE INDEX user_search_idx ON users (LOWER(username));"); */
        DB::statement("alter table users
                add column search tsvector
                generated always as (to_tsvector('pg_catalog.simple', username)) stored;");
        DB::statement("CREATE INDEX user_search_idx ON users USING GIN (search);");

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
