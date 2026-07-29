<?php

namespace Database\Factories;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->sentence();

        return [
            'user_id' => User::factory(),
            'title' => $title,
            'slug' => Str::slug($title),
            'excerpt' => fake()->paragraph(),
            'content' => fake()->paragraphs(3, true),
            'cover_image' => null,
            'status' => \App\Enums\PostStatusEnum::Draft,
            'published_at' => now(),
            'post_category_id' => \App\Models\PostCategory::factory(),
            'views' => 0,
            'tags' => [fake()->word()],
            'meta_title' => $title,
            'meta_description' => fake()->paragraph(),
        ];
    }
}
