<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('certificates', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('created_by')->constrained('users')->nullOnDelete();
            $table->timestamp('sent_at')->nullable()->after('user_id');
        });
    }

    public function down(): void
    {
        Schema::table('certificates', function (Blueprint $table) {
            $table->dropConstrainedForeignId('user_id');
            $table->dropColumn('sent_at');
        });
    }
};
