<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\{Film, User};

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Critic>
 */
class CriticFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
       return [
            'user_id' => User::inRandomOrder()->value('id') ?? User::factory(),
            'film_id' => Film::inRandomOrder()->value('id') ?? Film::factory(),
            'score'   => $this->faker->randomFloat(1, 0, 10),
            'comment' => $this->faker->paragraph(),
        ];
    }
}
