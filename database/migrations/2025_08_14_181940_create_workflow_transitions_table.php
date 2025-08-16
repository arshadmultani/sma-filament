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
        Schema::create('workflow_transitions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('workflow_id')->constrained('workflows');

            $table->foreignId('from_status_id')->constrained('statuses');
            $table->foreignId('to_status_id')->constrained('statuses');

            $table->string('action');

            $table->timestamps();
        });

        Schema::create('role_workflow_transition', function (Blueprint $table) {
            $table->foreignId('workflow_transition_id')->constrained('workflow_transitions')->cascadeOnDelete();
            $table->foreignId('role_id')->constrained('roles')->nullOnDelete();
            $table->primary(['workflow_transition_id', 'role_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('role_workflow_transition');
        Schema::dropIfExists('workflow_transitions');
    }
};
