<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $electronics = Category::factory()->create(['name' => 'Electronics', 'slug' => 'electronics']);
        $books = Category::factory()->create(['name' => 'Books', 'slug' => 'books']);
        $clothing = Category::factory()->create(['name' => 'Clothing', 'slug' => 'clothing']);
        $home = Category::factory()->create(['name' => 'Home & Kitchen', 'slug' => 'home-kitchen']);

        Product::factory()->for($electronics)->withPercentDiscount(5)->create([
            'title' => 'Wireless Headphones',
            'slug' => 'wireless-headphones',
            'description' => 'Noise-cancelling over-ear headphones with 30-hour battery life.',
            'price_cents' => 19999,
        ]);

        Product::factory()->for($electronics)->withFixedDiscount(2000)->create([
            'title' => 'Smart Watch',
            'slug' => 'smart-watch',
            'description' => 'Fitness tracking smartwatch with heart-rate monitor and GPS.',
            'price_cents' => 24999,
        ]);

        Product::factory()->for($books)->create([
            'title' => 'The Pragmatic Programmer',
            'slug' => 'the-pragmatic-programmer',
            'description' => 'Classic guide to software craftsmanship.',
            'price_cents' => 3499,
        ]);

        Product::factory()->for($clothing)->withPercentDiscount(15)->create([
            'title' => 'Cotton T-Shirt',
            'slug' => 'cotton-t-shirt',
            'description' => 'Comfortable 100% cotton t-shirt available in multiple colors.',
            'price_cents' => 1999,
        ]);

        Product::factory()->for($clothing)->create([
            'title' => 'Denim Jacket',
            'slug' => 'denim-jacket',
            'description' => 'Classic denim jacket with a modern fit.',
            'price_cents' => 7999,
        ]);

        Product::factory()->count(10)->for($electronics)->create();
        Product::factory()->count(8)->for($books)->create();
        Product::factory()->count(8)->for($clothing)->create();
        Product::factory()->count(10)->for($home)->create();

        Product::factory()->count(5)->for($home)->withPercentDiscount(10)->create();
        Product::factory()->count(3)->for($electronics)->withFixedDiscount(1000)->create();
    }
}
