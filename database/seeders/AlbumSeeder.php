<?php

namespace Database\Seeders;

use App\Models\Album;
use App\Models\User;
use Illuminate\Database\Seeder;

class AlbumSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create 50 albums
        Album::factory(50)->create()->each(function ($album) {
            // Randomly assign votes to albums
            $users = User::inRandomOrder()->take(rand(0, 8))->get();
            
            foreach ($users as $user) {
                $album->voters()->attach($user->id, [
                    'vote' => rand(0, 1) ? 1 : -1, // Randomly assign upvote or downvote
                ]);
            }
        });
    }
}

