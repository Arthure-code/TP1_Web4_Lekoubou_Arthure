<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\{Critic, User};

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            LanguagesSeeder::class,
            FilmsSeeder::class,
            ActorsSeeder::class,
            ActorFilmSeeder::class,
        ]);

        User::factory(10)->create()->each(function ($user) {
            Critic::factory(30)->create(['user_id' => $user->id]);
        });
    }
}
