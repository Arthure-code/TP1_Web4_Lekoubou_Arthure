<?php

namespace Database\Factories;

use App\Models\Language;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Film>
 */
class FilmFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => strtoupper($this->faker->words(2, true)),
            'description' => $this->faker->sentence(20),
            'release_year' => $this->faker->numberBetween(2000, 2024),
            'language_id' => Language::factory(),
            'length' => $this->faker->numberBetween(45, 180),
            'rating' => $this->faker->randomElement(['G', 'PG', 'PG-13', 'R', 'NC-17']),
            'special_features' => $this->faker->randomElement([
                'Trailers',
                'Deleted Scenes',
                'Behind the Scenes',
                'Commentaries',
                'Trailers,Deleted Scenes',
                'Commentaries,Behind the Scenes',
                'Deleted Scenes,Behind the Scenes',
            ]),
            'image' => '',
            'created_at' => now(),
        ];
    }
}
