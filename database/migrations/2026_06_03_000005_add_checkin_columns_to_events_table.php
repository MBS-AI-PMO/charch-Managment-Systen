<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->char('checkin_code', 4)->nullable()->after('location');
            $table->boolean('attendance_open')->default(false)->after('checkin_code');
            $table->timestamp('reminded_at')->nullable()->after('attendance_open');
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn(['checkin_code', 'attendance_open', 'reminded_at']);
        });
    }
};
