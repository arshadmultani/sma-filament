<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            // Essential Fields

            $table->id();
            $table->foreignId('doctor_id')->nullable()->constrained();
            $table->string('reviewer_name')->nullable();
            $table->string('submitted_by_name')->nullable();
            $table->string('submitted_by_email')->nullable();
            $table->unsignedTinyInteger('rating')->nullable();

            // Content Fields
            $table->text('review_text')->nullable();
            $table->string('media_url')->nullable();
            $table->string('media_type')->nullable()->comment("e.g., 'video', 'image'");

            // Moderation
            $table->foreignId('state_id')->nullable()->constrained('states');
            $table->timestamp('verified_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
