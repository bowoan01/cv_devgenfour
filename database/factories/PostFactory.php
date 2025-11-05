<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PostFactory extends Factory
{
    public function definition(): array
    {
        $title = fake()->unique()->sentence(6);

        return [
            'title' => $title,
            'slug' => Str::slug($title.' '.fake()->unique()->randomNumber()),
            'excerpt' => fake()->paragraph(),
            'body' => fake()->paragraphs(6, true),
            'cover_image' => null,
            'status' => fake()->randomElement(['draft', 'published']),
            'published_at' => now(),
            'author_id' => User::factory(),
        ];
    }
}
