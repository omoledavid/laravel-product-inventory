<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $electronics = Category::factory()->withDiscount(5)->create([
            'name' => 'Electronics',
            'slug' => 'electronics',
        ]);

        $books = Category::factory()->create([
            'name' => 'Books',
            'slug' => 'books',
        ]);

        $clothing = Category::factory()->withDiscount(15)->create([
            'name' => 'Clothing',
            'slug' => 'clothing',
        ]);

        Product::factory()->for($electronics)->create([
            'title' => 'Wireless Headphones',
            'slug' => 'wireless-headphones',
            'description' => 'Noise-cancelling over-ear headphones with 30-hour battery life.',
            'price_cents' => 19999,
        ]);

        Product::factory()->for($electronics)->create([
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

        Product::factory()->for($clothing)->create([
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

        Customer::factory()->create([
            'name' => 'Regular Jane',
            'email' => 'jane@example.com',
        ]);

        Customer::factory()->special(10)->create([
            'name' => 'VIP John',
            'email' => 'john@example.com',
        ]);
    }
}
