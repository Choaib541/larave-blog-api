<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {

        $tags = [];

        for ($i = 0; $i < $this->faker->numberBetween(8, 12); $i++) {
            $tags[] = $this->faker->name();
        }

        $tags = implode("|", $tags);

        return [
            "title" => $this->faker->name(),
            "cover" => "site_images/post_placeholder.jpg",
            "description" => $this->faker->paragraph(),
            "content" => $this->faker->paragraph(),
            "user_id" => $this->faker->numberBetween(1, 10),
            "tags" => $tags,
        ];
    }
}
