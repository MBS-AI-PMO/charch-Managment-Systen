<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('hero_slides', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('position')->default(0)->index();
            $table->string('image_path')->nullable();
            $table->string('eyebrow')->nullable();
            $table->string('heading');
            $table->text('sub')->nullable();
            $table->string('primary_cta_url')->nullable();
            $table->string('primary_cta_label')->nullable();
            $table->string('secondary_cta_url')->nullable();
            $table->string('secondary_cta_label')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hero_slides');
    }
};
