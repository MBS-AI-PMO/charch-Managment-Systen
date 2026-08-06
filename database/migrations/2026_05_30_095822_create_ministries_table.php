<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ministries', function (Blueprint $t) {
            $t->id();
            $t->string('slug')->unique();
            $t->string('name');
            $t->string('summary', 500)->nullable();
            $t->longText('body')->nullable();
            $t->string('leader_name')->nullable();
            $t->string('contact_email')->nullable();
            $t->string('cover_image_path')->nullable();
            $t->unsignedInteger('sort_order')->default(0);
            $t->boolean('is_published')->default(false);
            $t->timestamps();
            $t->index(['is_published','sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ministries');
    }
};
