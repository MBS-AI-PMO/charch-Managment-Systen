<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contact_messages', function (Blueprint $t) {
            $t->foreignId('user_id')->nullable()->after('id')->constrained()->nullOnDelete();
            $t->timestamp('replied_at')->nullable()->after('read_at');
            $t->index('replied_at');
        });

        Schema::create('contact_message_replies', function (Blueprint $t) {
            $t->id();
            $t->foreignId('contact_message_id')->constrained('contact_messages')->cascadeOnDelete();
            $t->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $t->boolean('is_staff')->default(true);
            $t->text('body');
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contact_message_replies');

        Schema::table('contact_messages', function (Blueprint $t) {
            $t->dropConstrainedForeignId('user_id');
            $t->dropIndex(['replied_at']);
            $t->dropColumn('replied_at');
        });
    }
};
