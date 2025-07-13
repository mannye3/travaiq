<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('visitor_logs', function (Blueprint $table) {
            $table->string('client_id')->after('user_id')->index(); // GA4-style client ID
            $table->uuid('session_id')->after('client_id')->index(); // Unique session ID
            $table->boolean('is_new_user')->after('session_id')->default(false); // New vs returning user
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('visitor_logs', function (Blueprint $table) {
            $table->dropColumn(['client_id', 'session_id', 'is_new_user']);
        });
    }
};
