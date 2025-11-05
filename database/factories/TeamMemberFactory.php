<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class TeamMemberFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'role_title' => fake()->jobTitle(),
            'bio' => fake()->paragraph(3),
            'photo_path' => null,
            'social_links' => [
                ['label' => 'LinkedIn', 'url' => fake()->url()],
            ],
            'order_index' => fake()->numberBetween(1, 10),
            'is_visible' => true,
        ];
    }
}
