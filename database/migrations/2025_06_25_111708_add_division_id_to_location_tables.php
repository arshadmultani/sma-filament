<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Add nullable division_id columns
        Schema::table('zones', function (Blueprint $table) {
            $table->foreignId('division_id')->nullable()->after('id')->constrained();
        });
        Schema::table('regions', function (Blueprint $table) {
            $table->foreignId('division_id')->nullable()->after('id')->constrained();
        });
        Schema::table('areas', function (Blueprint $table) {
            $table->foreignId('division_id')->nullable()->after('id')->constrained();
        });
        Schema::table('headquarters', function (Blueprint $table) {
            $table->foreignId('division_id')->nullable()->after('id')->constrained();
        });

        // 2. Backfill with default division_id (e.g., 1)
        DB::table('zones')->whereNull('division_id')->update(['division_id' => 2]);
        DB::table('regions')->whereNull('division_id')->update(['division_id' => 2]);
        DB::table('areas')->whereNull('division_id')->update(['division_id' => 2]);
        DB::table('headquarters')->whereNull('division_id')->update(['division_id' => 2]);

        // 3. Make division_id non-nullable and add unique constraints
        Schema::table('zones', function (Blueprint $table) {
            $table->foreignId('division_id')->nullable(false)->change();
            $table->unique(['division_id', 'name']);
        });
        Schema::table('regions', function (Blueprint $table) {
            $table->foreignId('division_id')->nullable(false)->change();
            $table->unique(['division_id', 'name']);
        });
        Schema::table('areas', function (Blueprint $table) {
            $table->foreignId('division_id')->nullable(false)->change();
            $table->unique(['division_id', 'name']);
        });
        Schema::table('headquarters', function (Blueprint $table) {
            $table->foreignId('division_id')->nullable(false)->change();
            $table->unique(['division_id', 'name']);
        });
    }

    public function down(): void
    {
        Schema::table('zones', function (Blueprint $table) {
            $table->dropUnique(['division_id', 'name']);
            $table->dropConstrainedForeignId('division_id');
        });
        Schema::table('regions', function (Blueprint $table) {
            $table->dropUnique(['division_id', 'name']);
            $table->dropConstrainedForeignId('division_id');
        });
        Schema::table('areas', function (Blueprint $table) {
            $table->dropUnique(['division_id', 'name']);
            $table->dropConstrainedForeignId('division_id');
        });
        Schema::table('headquarters', function (Blueprint $table) {
            $table->dropUnique(['division_id', 'name']);
            $table->dropConstrainedForeignId('division_id');
        });
    }
}; 