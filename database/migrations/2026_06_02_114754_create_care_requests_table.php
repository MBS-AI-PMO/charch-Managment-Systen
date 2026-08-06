<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('care_requests', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $t->enum('category', ['illness','grief','financial','food','other']);
            $t->text('message');
            $t->boolean('share_with_team')->default(false);
            $t->enum('status', ['open','responding','closed'])->default('open');
            $t->foreignId('responder_id')->nullable()->constrained('users')->nullOnDelete();
            $t->longText('response_notes')->nullable();
            $t->timestamp('closed_at')->nullable();
            $t->timestamps();
            $t->index(['status', 'created_at']);
            $t->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('care_requests');
    }
};
