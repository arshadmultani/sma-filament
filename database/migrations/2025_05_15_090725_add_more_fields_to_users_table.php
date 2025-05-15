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
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone_number');
            $table->foreignId('division_id')->constrained('divisions')->nullable();
            $table->foreignId('headquarter_id')->constrained('headquarters')->nullable();
            $table->foreignId('region_id')->constrained('regions')->nullable();
            $table->foreignId('area_id')->constrained('areas')->nullable();

            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
