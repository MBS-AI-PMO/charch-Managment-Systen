<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('feed_posts', function (Blueprint $t) {
            $t->id();
            $t->foreignId('author_id')->constrained('users');
            $t->string('title', 200)->nullable();
            $t->longText('body');
            $t->string('image_path')->nullable();
            $t->boolean('pinned')->default(false);
            $t->timestamp('published_at')->useCurrent();
            $t->timestamps();
            $t->index(['pinned', 'published_at']);
        });

        Schema::create('feed_reactions', function (Blueprint $t) {
            $t->id();
            $t->foreignId('feed_post_id')->constrained()->cascadeOnDelete();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->enum('kind', ['heart','pray','amen']);
            $t->timestamp('created_at')->useCurrent();
            $t->unique(['feed_post_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feed_reactions');
        Schema::dropIfExists('feed_posts');
    }
};
