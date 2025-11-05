<?php

namespace Database\Factories;

use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProjectImageFactory extends Factory
{
    public function definition(): array
    {
        return [
            'project_id' => Project::factory(),
            'path' => 'projects/gallery/'.fake()->uuid().'.jpg',
            'caption' => fake()->sentence(6),
            'display_order' => fake()->numberBetween(1, 5),
        ];
    }
}
