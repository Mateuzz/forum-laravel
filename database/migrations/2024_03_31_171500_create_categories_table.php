<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('title', 50);
            $table->string('slug', 80);
            $table->integer('posts_count')->default(0);
            $table->integer('comments_count')->default(0);
            $table->bigInteger('parent_id')->nullable();
            $table->integer('depth');

            $table->index('slug');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('categories');
    }
};
