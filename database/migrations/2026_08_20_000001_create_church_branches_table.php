<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('church_branches', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('name');
            $table->string('city')->nullable();
            $table->string('role')->nullable();
            $table->string('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->text('services')->nullable();
            $table->text('note')->nullable();
            $table->string('pastor')->nullable();
            $table->string('language')->nullable();
            $table->string('hero_sub')->nullable();
            $table->text('about')->nullable();
            $table->text('expect')->nullable();
            $table->text('ministries')->nullable();
            $table->text('families')->nullable();
            $table->text('visit')->nullable();
            $table->text('getting_here')->nullable();
            $table->unsignedInteger('sort_order')->default(0)->index();
            $table->boolean('is_published')->default(true)->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('church_branches');
    }
};
