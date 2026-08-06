<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('events', function (Blueprint $t) {
            $t->id();
            $t->string('slug')->unique();
            $t->string('title');
            $t->longText('description')->nullable();
            $t->string('location')->nullable();
            $t->dateTime('starts_at');
            $t->dateTime('ends_at')->nullable();
            $t->string('cover_image_path')->nullable();
            $t->string('registration_url')->nullable();
            $t->boolean('is_published')->default(false);
            $t->boolean('is_featured')->default(false);
            $t->timestamps();
            $t->index(['is_published','starts_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
