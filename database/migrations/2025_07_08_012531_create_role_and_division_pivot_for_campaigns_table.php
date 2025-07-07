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
       // Pivot for campaigns and divisions
       Schema::create('campaign_division', function (Blueprint $table) {
        $table->id();
        $table->foreignId('campaign_id')->nullable()->constrained('campaigns')->onDelete('cascade');
        $table->foreignId('division_id')->nullable()->constrained('divisions')->onDelete('cascade');
        $table->unique(['campaign_id', 'division_id']);
    });
    // Pivot for campaigns and roles
    Schema::create('campaign_role', function (Blueprint $table) {
        $table->id();
        $table->foreignId('campaign_id')->nullable()->constrained('campaigns')->onDelete('cascade');
        $table->foreignId('role_id')->nullable()->constrained('roles')->onDelete('cascade');
        $table->unique(['campaign_id', 'role_id']);
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campaign_division');
        Schema::dropIfExists('campaign_role');
    }
};
