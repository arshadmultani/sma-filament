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
        Schema::table('kofol_entries', function (Blueprint $table) {
            $table->dropForeign(['kofol_campaign_id']);
            $table->dropColumn('kofol_campaign_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kofol_entries', function (Blueprint $table) {
            $table->foreignId('kofol_campaign_id')->constrained('kofol_campaigns');
            
        });
    }
};
