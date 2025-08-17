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
        Schema::create('p_o_b_s', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->nullOnDelete();
            $table->foreignId('headquarter_id')->nullable()->constrained('headquarters')->nullOnDelete();
            $table->foreignId('state_id')->nullable()->constrained('states');

            $table->morphs('customer');

            $table->unsignedInteger('invoice_amount');
            $table->string('invoice_image');
            $table->timestamps();
        });

        Schema::create('p_o_b_product', function (Blueprint $table) {
            $table->id();
            $table->foreignId('p_o_b_id')->constrained('p_o_b_s')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->nullOnDelete();
            $table->unsignedMediumInteger('quantity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('p_o_b_product');
        Schema::dropIfExists('p_o_b_s');
    }
};
