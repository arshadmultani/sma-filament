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
        // Doctors Table
        Schema::table('doctors', function (Blueprint $table) {
            $table->unsignedTinyInteger('experience')->nullable();
            $table->foreignId('merit_id')->nullable()->constrained('merits')->after('microsite_template_id');
        });
        // Microsites Table
        Schema::table('microsites', function (Blueprint $table) {

            // DROP STATUS COLUMN
            $table->dropColumn('status');

            // ADD NEW COLUMNS
            $table->foreignId('microsite_template_id')->nullable()->constrained('microsite_templates')->after('headquarter_id');
            $table->foreignId('state_id')->nullable()->constrained('states')->after('user_id');
            $table->foreignId('headquarter_id')->nullable()->constrained('headquarters')->nullOnDelete()->after('state_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('doctors', function (Blueprint $table) {
            $table->dropColumn('experience');
            $table->dropColumn('merit_id');
        });
        Schema::table('microsites', function (Blueprint $table) {
            $table->dropForeign(['microsite_template_id']);
            $table->dropColumn('microsite_template_id');
            $table->dropForeign(['state_id']);
            $table->dropColumn('state_id');
            $table->dropForeign(['headquarter_id']);
            $table->dropColumn('headquarter_id');
            $table->string('status')->nullable()->after('headquarter_id');
        });
    }
};
