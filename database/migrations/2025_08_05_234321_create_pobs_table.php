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
        Schema::create('pobs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->nullOnDelete();
            $table->string('invoice_image');
            $table->integer('invoice_amount');
            $table->morphs('customer');
            $table->foreignId('headquarter_id')->nullable()->constrained('headquarters')->nullOnDelete();
            $table->string('status');
            $table->timestamps();
        });

        Schema::create('pob_product', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pob_id')->constrained('pobs')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products');
            $table->integer('quantity');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pob_product');
        Schema::dropIfExists('pobs');
    }
};
