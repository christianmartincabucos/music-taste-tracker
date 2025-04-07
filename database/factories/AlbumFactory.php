<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Album>
 */
class AlbumFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'song_name' => $this->faker->sentence(3),
            'artist_name' => $this->faker->name(),
            'cover_image' => $this->faker->boolean(70) 
                ? 'https://picsum.photos/id/' . $this->faker->numberBetween(1, 1000) . '/300/300' 
                : null,
        ];
    }
}

