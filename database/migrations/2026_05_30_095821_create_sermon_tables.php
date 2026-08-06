<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sermon_series', function (Blueprint $t) {
            $t->id();
            $t->string('slug')->unique();
            $t->string('name');
            $t->text('description')->nullable();
            $t->string('cover_image_path')->nullable();
            $t->timestamps();
        });

        Schema::create('sermon_speakers', function (Blueprint $t) {
            $t->id();
            $t->string('slug')->unique();
            $t->string('name');
            $t->string('role')->nullable();
            $t->text('bio')->nullable();
            $t->string('photo_path')->nullable();
            $t->timestamps();
        });

        Schema::create('sermons', function (Blueprint $t) {
            $t->id();
            $t->string('slug')->unique();
            $t->string('title');
            $t->text('summary')->nullable();
            $t->longText('body')->nullable();
            $t->foreignId('series_id')->nullable()->constrained('sermon_series')->nullOnDelete();
            $t->foreignId('speaker_id')->nullable()->constrained('sermon_speakers')->nullOnDelete();
            $t->string('scripture_reference')->nullable();
            $t->date('preached_on')->nullable();
            $t->string('audio_url')->nullable();
            $t->string('video_url')->nullable();
            $t->string('thumbnail_path')->nullable();
            $t->boolean('downloads_enabled')->default(true);
            $t->boolean('is_published')->default(false);
            $t->timestamps();
            $t->index(['is_published','preached_on']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sermons');
        Schema::dropIfExists('sermon_speakers');
        Schema::dropIfExists('sermon_series');
    }
};
