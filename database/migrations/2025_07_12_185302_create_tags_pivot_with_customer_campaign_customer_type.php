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
        Schema::create('campaign_tag', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->nullable()->constrained('campaigns');
            $table->foreignId('tag_id')->nullable()->constrained('tags');
            $table->unique(['campaign_id', 'tag_id']);
            $table->timestamps();
        });
        
        Schema::create('doctor_tag', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doctor_id')->nullable()->constrained('doctors');
            $table->foreignId('tag_id')->nullable()->constrained('tags');
            $table->unique(['doctor_id', 'tag_id']);
            $table->index(['doctor_id', 'tag_id']);
            $table->foreignId('user_id')->nullable()->constrained('users');
            $table->timestamps();
        });

        Schema::create('chemist_tag', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chemist_id')->nullable()->constrained('chemists');
            $table->foreignId('tag_id')->nullable()->constrained('tags');
            $table->unique(['chemist_id', 'tag_id']);
            $table->index(['chemist_id', 'tag_id']);
            $table->foreignId('user_id')->nullable()->constrained('users');
            $table->timestamps();
        });
        Schema::create('division_tag', function (Blueprint $table) {
            $table->id();
            $table->foreignId('division_id')->nullable()->constrained('divisions');
            $table->foreignId('tag_id')->nullable()->constrained('tags');
            $table->unique(['division_id', 'tag_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('division_tag');
        Schema::dropIfExists('chemist_tag');
        Schema::dropIfExists('doctor_tag');
        Schema::dropIfExists('campaign_tag');
    }
};
