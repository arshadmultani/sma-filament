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
        Schema::create('manager_log_entry_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('manager_log_entry_id')->nullable()->constrained('manager_log_entries')->cascadeOnDelete();
            $table->foreignId('manager_log_entry_colleague_id')->nullable()->constrained('manager_log_entry_colleagues')->cascadeOnDelete(); //worked with user
            $table->nullableMorphs('customer');
            $table->boolean('doctor_converted')->nullable();
            $table->string('conversion_type')->nullable();
            $table->smallInteger('no_of_prescriptions')->nullable();
            $table->string('prescription_image')->nullable();
            $table->decimal('invoice_amount', 10, 2)->nullable();
            $table->string('invoice_image')->nullable();
        });
        Schema::create('manager_log_entry_activity_call_input', function (Blueprint $table) {
            $table->id();
            $table->foreignId('manager_log_entry_activity_id')->constrained()->onDelete('cascade');
            $table->foreignId('call_input_id')->constrained()->onDelete('cascade');
        });
    }   

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('manager_log_entry_activity_call_input');
        Schema::dropIfExists('manager_log_entry_activities');
        Schema::dropIfExists('manager_log_entry_colleagues');

    }
}; 