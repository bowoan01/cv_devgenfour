<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProjectFactory extends Factory
{
    public function definition(): array
    {
        $title = fake()->unique()->sentence(4);

        return [
            'title' => $title,
            'slug' => Str::slug($title.' '.fake()->unique()->randomNumber()),
            'client' => fake()->company(),
            'category' => fake()->randomElement(['Web', 'Mobile', 'Enterprise', 'Product']),
            'tech_stack' => [fake()->randomElement(['Laravel', 'Vue', 'React', 'Flutter', 'AWS'])],
            'summary' => fake()->paragraph(3),
            'results' => fake()->paragraphs(4, true),
            'cover_image' => null,
            'is_published' => true,
        ];
    }
}
