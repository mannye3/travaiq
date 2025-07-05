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
        Schema::create('agoda_hotel_recommendations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('location_overview_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('address')->nullable();
            $table->decimal('rating', 3, 1)->default(0);
            $table->decimal('price', 10, 2)->default(0);
            $table->string('currency', 3)->default('USD');
            $table->string('image_url')->nullable();
            $table->json('amenities')->nullable();
            $table->json('location')->nullable();
            $table->decimal('review_score', 3, 1)->default(0);
            $table->integer('review_count')->default(0);
            $table->string('booking_url')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agoda_hotel_recommendations');
    }
};
