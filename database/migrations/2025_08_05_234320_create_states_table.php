<?php

use App\Enums\StateCategory;
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
        Schema::create('states', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('color')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_system')->default(false);
            $table->string('category')->nullable();
            $table->string('slug')->nullable();
            $table->timestamps();
        });

        $defaultStates = [
            [
                'name' => 'Approved',
                'color' => 'success',
                'is_system' => true,
                'category' => StateCategory::FINALIZED,
                'slug' => 'approved',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Pending',
                'color' => 'warning',
                'is_system' => true,
                'category' => StateCategory::PENDING,
                'slug' => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Rejected',
                'color' => 'danger',
                'is_system' => true,
                'category' => StateCategory::CANCELLED,
                'slug' => 'rejected',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Draft',
                'color' => 'gray',
                'is_system' => true,
                'category' => StateCategory::DRAFT,
                'slug' => 'draft',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('states')->insert($defaultStates);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('states');
    }
};
