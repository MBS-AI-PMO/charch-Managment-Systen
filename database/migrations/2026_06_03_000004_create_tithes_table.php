<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tithes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('giver_name', 160)->nullable();
            $table->foreignId('fund_id')->constrained('tithe_funds');
            $table->unsignedBigInteger('amount_cents');
            $table->date('received_at');
            $table->enum('method', ['cash', 'bank_transfer', 'cheque', 'other']);
            $table->string('reference', 120)->nullable();
            $table->text('note')->nullable();
            $table->foreignId('recorded_by')->constrained('users');
            $table->timestamps();

            $table->index('received_at');
            $table->index(['fund_id', 'received_at']);
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tithes');
    }
};
