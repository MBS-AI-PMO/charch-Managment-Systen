<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('media_folders', function (Blueprint $t) {
            $t->id();
            $t->foreignId('parent_id')->nullable()->constrained('media_folders')->nullOnDelete();
            $t->string('name');
            $t->string('slug');
            $t->string('path');
            $t->timestamps();
            $t->unique(['parent_id','slug']);
        });

        Schema::create('media', function (Blueprint $t) {
            $t->id();
            $t->foreignId('folder_id')->nullable()->constrained('media_folders')->nullOnDelete();
            $t->string('disk')->default('public');
            $t->string('path')->unique();
            $t->string('filename');
            $t->string('mime_type');
            $t->unsignedBigInteger('size');
            $t->unsignedInteger('width')->nullable();
            $t->unsignedInteger('height')->nullable();
            $t->string('alt_text')->nullable();
            $t->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('media');
        Schema::dropIfExists('media_folders');
    }
};
