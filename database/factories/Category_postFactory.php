<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category_post>
 */
class Category_postFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            "category_id" => $this->faker->numberBetween(1, 10),
            "post_id" => $this->faker->numberBetween(1, 10),
        ];
    }
}
