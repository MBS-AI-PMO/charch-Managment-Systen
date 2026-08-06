<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prayer_requests', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $t->string('name', 120);
            $t->string('title', 200);
            $t->longText('body');
            $t->boolean('is_public')->default(false);
            $t->boolean('is_anonymous')->default(false);
            $t->enum('status', ['pending','praying','answered','closed'])->default('pending');
            $t->unsignedInteger('pray_count')->default(0);
            $t->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $t->longText('admin_notes')->nullable();
            $t->timestamps();
            $t->softDeletes();
            $t->index(['status', 'created_at']);
            $t->index('user_id');
        });

        Schema::create('prayer_request_prays', function (Blueprint $t) {
            $t->id();
            $t->foreignId('prayer_request_id')->constrained()->cascadeOnDelete();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->timestamp('created_at')->useCurrent();
            $t->unique(['prayer_request_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prayer_request_prays');
        Schema::dropIfExists('prayer_requests');
    }
};
