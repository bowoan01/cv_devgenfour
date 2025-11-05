<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ServiceFactory extends Factory
{
    public function definition(): array
    {
        $title = fake()->unique()->sentence(3);

        return [
            'title' => $title,
            'slug' => Str::slug($title),
            'short_description' => fake()->sentence(10),
            'description' => fake()->paragraphs(3, true),
            'icon_path' => null,
            'display_order' => fake()->numberBetween(1, 20),
            'is_published' => true,
        ];
    }
}
