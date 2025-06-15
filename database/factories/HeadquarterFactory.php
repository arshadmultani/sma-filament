<?php

namespace Database\Factories;

use App\Models\Area;
use App\Models\Region;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Headquarter>
 */
class HeadquarterFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name,
            'area_id' => Area::factory(),
            'region_id' => Region::factory(),
        ];
    }
}
