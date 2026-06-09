<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'designation' => fake()->words(3, true),
            'prix_unitaire' => fake()->randomFloat(2, 10, 2000),
            'stock' => fake()->numberBetween(0, 100),
            'description' => fake()->optional()->sentence(),
        ];
    }
}
