<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Products>
 */
class ProductsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'barcode' => $this->faker->unique()->numerify('#####'),
            'sku' => $this->faker->unique()->word,
            'name' => $this->faker->word,
            'category_id' => \App\Models\ProductCategory::inRandomOrder()->first()->id, // Asumsi ada data di ProductCategory
            'price' => $this->faker->randomFloat(2, 1000, 100000),
            'unit' => $this->faker->word,
            'min_stock' => $this->faker->numberBetween(1, 100),
            'is_active' => $this->faker->boolean(80),
        ];
    }
}
