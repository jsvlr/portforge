<?php

namespace Database\Factories;

use App\Models\ProjectRole;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\User;

/**
 * @extends Factory<ProjectRole>
 */
class ProjectRoleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->word();

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'user_id' => User::factory()->create(),
        ];
    }
}
