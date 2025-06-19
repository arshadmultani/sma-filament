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
        Schema::create('kofol_entry_coupons', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('coupon_code')->unique(); 
            $table->foreignId('kofol_entry_id')->constrained('kofol_entries')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kofol_entry_coupons');
    }
};
