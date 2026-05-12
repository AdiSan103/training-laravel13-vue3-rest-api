<?php

namespace Database\Factories;

use App\Models\Post;
use App\Models\User;
use App\Models\Category;
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
        $title = $this->faker->sentence(5);
        return [
            'title'=> $title,
            'slug' => Str::random(8),
            'user_id'=> User::inRandomOrder()->first()->id ?? User::factory(),
            'category_id'=> Category::inRandomOrder()->first()->id ?? Category::factory(),
            'content'=> fake()->sentence(30),
        ];
    }
}
