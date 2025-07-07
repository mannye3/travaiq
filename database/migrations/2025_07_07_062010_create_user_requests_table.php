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
        Schema::create('user_requests', function (Blueprint $table) {
            $table->id();
            $table->string('location');
            $table->date('travel_date');
            $table->integer('duration');
            $table->string('budget');
            $table->string('traveler_type');
            $table->json('activities');
            $table->string('user_country')->nullable();
            $table->string('user_city')->nullable();
            $table->string('user_ip')->nullable();
            $table->decimal('user_longitude', 10, 7)->nullable();
            $table->decimal('user_latitude', 10, 7)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_requests');
    }
};
