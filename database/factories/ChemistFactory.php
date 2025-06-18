<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Headquarter;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Chemist>
 */
class ChemistFactory extends Factory
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
            'email' => $this->faker->unique()->safeEmail,
            'phone' => $this->faker->phoneNumber,
            'address' => $this->faker->address,
            'town' => $this->faker->city,
            'headquarter_id' => Headquarter::factory(),
            'type' => $this->faker->randomElement(['Ayurvedic', 'Allopathic']),
            'user_id' => User::factory(),
        ];
    }
}
