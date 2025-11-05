<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ContactMessageFactory extends Factory
{
    public function definition(): array
    {
        $status = fake()->randomElement(['new', 'handled']);

        return [
            'name' => fake()->name(),
            'email' => fake()->safeEmail(),
            'company' => fake()->company(),
            'phone' => fake()->phoneNumber(),
            'message' => fake()->paragraph(4),
            'status' => $status,
            'handled_by' => $status === 'handled' ? User::factory() : null,
            'handled_at' => $status === 'handled' ? now() : null,
        ];
    }
}
