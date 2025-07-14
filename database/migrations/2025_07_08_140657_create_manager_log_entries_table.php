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
        Schema::create('manager_log_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete(); //manager ID
            $table->date('date');
            $table->boolean('worked_with_team')->nullable();
            $table->unsignedTinyInteger('doctors_met')->nullable();
            $table->boolean('activity_doctor_met')->nullable();
            $table->string('remark')->nullable();
            $table->timestamps();
            $table->unique(['user_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('manager_log_entries');
    }
};
