<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pages', function (Blueprint $t) {
            $t->id();
            $t->string('slug')->unique();
            $t->string('title');
            $t->string('hero_heading')->nullable();
            $t->string('hero_subheading')->nullable();
            $t->string('hero_image_path')->nullable();
            $t->longText('body')->nullable();
            $t->string('meta_title')->nullable();
            $t->string('meta_description', 500)->nullable();
            $t->string('meta_og_image_path')->nullable();
            $t->boolean('is_published')->default(false);
            $t->timestamp('published_at')->nullable();
            $t->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $t->timestamps();
            $t->index(['is_published','published_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pages');
    }
};
