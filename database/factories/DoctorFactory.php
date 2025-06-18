<?php

namespace Database\Factories;

use App\Models\Headquarter;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Qualification;
use App\Models\Specialty;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Doctor>
 */
class DoctorFactory extends Factory
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
            'type' => $this->faker->randomElement(['Ayurvedic', 'Allopathic']),
            'qualification_id' => Qualification::inRandomOrder()->first()->id,
            'specialty_id' => Specialty::inRandomOrder()->first()->id,
            'support_type' => $this->faker->randomElement(['Prescribing', 'Dispensing']),
            'email' => $this->faker->unique()->safeEmail,
            'phone' => $this->faker->phoneNumber,
            'address' => $this->faker->address,
            'town' => $this->faker->city,
            'headquarter_id' => Headquarter::factory(),
            'user_id' => User::factory(),
            'qualification_id' => 1,
            'support_type' => $this->faker->randomElement(['Prescribing', 'Dispensing']),
            'attachment' => $this->faker->imageUrl(),
            'profile_photo' => $this->faker->imageUrl(),
        ];
    }
}
