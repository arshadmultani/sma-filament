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
        Schema::create('kofol_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kofol_campaign_id')->constrained('kofol_campaigns');
            $table->foreignId('user_id')->constrained('users');
            $table->string('invoice_image');
            $table->json('products');
            $table->morphs('customer');
            $table->string('status');
            $table->integer('coupon_code')->unique()->nullable();
            $table->integer('invoice_amount')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kofol_entries');
    }
};
