<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    public function definition(): array
    {
        $title = $this->faker->unique()->words(3, true);

        return [
            'category_id' => Category::factory(),
            'title' => ucwords($title),
            'slug' => Str::slug($title).'-'.Str::random(5),
            'description' => $this->faker->paragraph(),
            'price_cents' => $this->faker->numberBetween(999, 99999),
        ];
    }
}
