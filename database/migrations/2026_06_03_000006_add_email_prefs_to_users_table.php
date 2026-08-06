<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('email_reminder_event_24h')->default(true);
            $table->boolean('email_weekly_digest')->default(true);
            $table->boolean('email_admin_daily_digest')->default(false);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'email_reminder_event_24h',
                'email_weekly_digest',
                'email_admin_daily_digest',
            ]);
        });
    }
};
