<?php

namespace Database\Factories;

use App\Enums\ProjectStatusEnum;
use App\Models\Project;
use App\Models\ProjectRole;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Project>
 */
class ProjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $user = User::factory()->createOne();
        $title = fake()->sentence();
        $content = fake()->sentence(5);
        return [
            'title' => $title,
            'slug' => Str::slug($title),
            'user_id' => $user,
            'content' => "<p>$content</p>",
            'project_role_id' => ProjectRole::factory()->for($user)->createOne(),
            'status' => ProjectStatusEnum::Published,
            'started_at' => now()->toDateString(),
            'gallery' => [],
            'meta_title' => $title,
            'meta_description' => $content,
            'completed_at' => now()->subDays(fake()->randomDigitNotNull()),
            'views' => fake()->randomDigit()
        ];
    }
}
