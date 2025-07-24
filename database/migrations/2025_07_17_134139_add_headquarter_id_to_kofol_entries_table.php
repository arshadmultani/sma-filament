<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('kofol_entries', function (Blueprint $table) {
            $table->foreignId('headquarter_id')->nullable()->after('customer_id')->constrained('headquarters')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('kofol_entries', function (Blueprint $table) {
            $table->dropColumn(['headquarter_id']);
        });
    }
};