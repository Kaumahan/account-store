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
        $name = $this->faker->words(3, true);
        return [
            'name' => ucwords($name),
            'slug' => str()->slug($name),
            'description' => $this->faker->sentence(15),
            'price' => $this->faker->randomFloat(2, 10, 500),
            'stock' => $this->faker->numberBetween(0, 50),
            'image_url' => 'https://picsum.photos/seed/' . rand(1, 1000) . '/600/600',
            'is_active' => true,
        ];
    }
}
