<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('microsite_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug');
            $table->string('thumbnail')->nullable();
            $table->json('properties')->nullable();
            $table->boolean('is_default');
            $table->boolean('is_active');
            $table->timestamps();
        });

        $defaultTemplate = [
            [
                'name' => 'Default Template',
                'slug' => 'default',
                'thumbnail' => null,
                'properties' => json_encode([]),
                'is_default' => true,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ];
        if (DB::table('microsite_templates')->count() === 0) {
            DB::table('microsite_templates')->insert($defaultTemplate);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('microsite_templates');
    }
};
