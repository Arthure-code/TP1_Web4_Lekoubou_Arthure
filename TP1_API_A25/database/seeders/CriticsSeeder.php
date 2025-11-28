<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\{Critic, User};

class CriticsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::all()->each(function (User $user) {
            Critic::factory(30)->create([
                'user_id' => $user->id,
            ]);
        });
    }
}
