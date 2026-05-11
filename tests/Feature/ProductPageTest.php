<?php

use App\Models\Category;
use App\Models\Product;

it('renders the product index listing all products', function () {
    $a = Product::factory()->create(['title' => 'Alpha Gadget']);
    $b = Product::factory()->create(['title' => 'Beta Widget']);

    $this->get(route('products.index'))
        ->assertOk()
        ->assertSee('Alpha Gadget')
        ->assertSee('Beta Widget')
        ->assertSee(route('products.show', $a), false)
        ->assertSee(route('products.show', $b), false);
});

it('shows a product page by slug', function () {
    $product = Product::factory()->create([
        'slug' => 'wireless-headphones',
        'title' => 'Wireless Headphones',
        'description' => 'Noise-cancelling.',
        'price_cents' => 19999,
    ]);

    $this->get(route('products.show', $product))
        ->assertOk()
        ->assertSee('Wireless Headphones')
        ->assertSee('Noise-cancelling.')
        ->assertSee('$199.99')
        ->assertDontSee('Customer');
});

it('returns 404 for an unknown product slug', function () {
    $this->get('/products/does-not-exist')->assertNotFound();
});

it('shows the percentage discount and final price', function () {
    $product = Product::factory()->withPercentDiscount(5)->create([
        'slug' => 'tv',
        'price_cents' => 20000,
    ]);

    $this->get(route('products.show', $product))
        ->assertOk()
        ->assertSee('$200.00')
        ->assertSee('$190.00')
        ->assertSee('5% off');
});

it('shows the fixed-amount discount and final price', function () {
    $product = Product::factory()->withFixedDiscount(2500)->create([
        'slug' => 'phone',
        'price_cents' => 20000,
    ]);

    $this->get(route('products.show', $product))
        ->assertOk()
        ->assertSee('$200.00')
        ->assertSee('$175.00')
        ->assertSee('$25.00 off');
});

it('shows the category badge when present', function () {
    $category = Category::factory()->create(['name' => 'Electronics']);
    $product = Product::factory()->for($category)->create();

    $this->get(route('products.show', $product))
        ->assertOk()
        ->assertSee('Electronics');
});
