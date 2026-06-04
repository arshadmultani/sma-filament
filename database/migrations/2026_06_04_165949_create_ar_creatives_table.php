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
        Schema::create('ar_creatives', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('marker_image_path')->nullable();
            $table->string('video_path')->nullable();
            $table->string('mind_file_path')->nullable();
            // Trackability quality (0-100) computed in the browser at compile time.
            $table->unsignedInteger('tracking_score')->nullable();
            // Marker pixel dimensions captured at compile time (avoids reading the
            // image off S3 just to know its aspect ratio for the video overlay).
            $table->unsignedInteger('marker_width')->nullable();
            $table->unsignedInteger('marker_height')->nullable();
            $table->string('status')->default('draft'); // draft | published
            $table->string('play_mode')->default('loop'); // loop | once
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ar_creatives');
    }
};
