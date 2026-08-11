<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('certificate_sends', function (Blueprint $table) {
            $table->id();
            $table->foreignId('certificate_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('sent_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('sent_at');
            $table->timestamps();

            $table->index(['certificate_id', 'sent_at']);
        });

        // Backfill current assignments so History isn’t empty for older certificates.
        $now = now();
        $rows = DB::table('certificates')
            ->whereNotNull('user_id')
            ->whereNotNull('sent_at')
            ->get(['id', 'user_id', 'created_by', 'sent_at', 'created_at', 'updated_at']);

        foreach ($rows as $row) {
            DB::table('certificate_sends')->insert([
                'certificate_id' => $row->id,
                'user_id' => $row->user_id,
                'sent_by' => $row->created_by,
                'sent_at' => $row->sent_at,
                'created_at' => $row->created_at ?? $now,
                'updated_at' => $row->updated_at ?? $now,
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('certificate_sends');
    }
};
