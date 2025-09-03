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
            $table->date('practice_since')->nullable();
        });
        // Microsites Table
        Schema::table('microsites', function (Blueprint $table) {

            // DROP COLUMNS
            if (Schema::hasColumn('microsites', 'status')) {
                $table->dropColumn('status');
            }
            if (Schema::hasColumn('microsites', 'message')) {
                $table->dropColumn('message');
            }
            if (Schema::hasColumn('microsites', 'reviews')) {
                $table->dropColumn('reviews');
            }
            if (Schema::hasColumn('microsites', 'doctor_id')) {
                $table->dropColumn('doctor_id');
            }
            if (Schema::hasColumn('microsites', 'customer_type')) {
                $table->dropColumn('customer_type');
            }
            if (Schema::hasColumn('microsites', 'customer_id')) {
                $table->dropColumn('customer_id');
            }




            // ADD NEW COLUMNS
            $table->foreignId('doctor_id')->nullable()->constrained('doctors');
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
            $table->dropColumn('practice_since');
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
