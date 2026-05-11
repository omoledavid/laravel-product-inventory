<?php

use App\Models\Category;
use App\Models\Customer;
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
        ->assertSee('$199.99');
});

it('returns 404 for an unknown product slug', function () {
    $this->get('/products/does-not-exist')->assertNotFound();
});

it('applies the category discount on the show page', function () {
    $category = Category::factory()->withDiscount(5)->create(['name' => 'Electronics']);
    $product = Product::factory()->for($category)->create([
        'slug' => 'tv',
        'price_cents' => 20000,
    ]);

    $this->get(route('products.show', $product))
        ->assertOk()
        ->assertSee('$200.00')
        ->assertSee('$190.00')
        ->assertSee('Category discount');
});

it('stacks category and customer discounts when a customer is selected', function () {
    $category = Category::factory()->withDiscount(5)->create();
    $product = Product::factory()->for($category)->create([
        'slug' => 'phone',
        'price_cents' => 20000,
    ]);
    $customer = Customer::factory()->special(10)->create();

    $this->get(route('products.show', $product).'?customer='.$customer->id)
        ->assertOk()
        ->assertSee('$200.00')
        ->assertSee('Category discount')
        ->assertSee('Customer discount')
        ->assertSee('$171.00');
});

it('ignores an invalid customer id query param', function () {
    $product = Product::factory()->create(['slug' => 'item', 'price_cents' => 10000]);

    $this->get(route('products.show', $product).'?customer=99999')
        ->assertOk()
        ->assertSee('$100.00')
        ->assertDontSee('Customer discount');
});

it('lists every customer in the selector', function () {
    $product = Product::factory()->create();
    $regular = Customer::factory()->create(['name' => 'Regular Jane']);
    $vip = Customer::factory()->special(10)->create(['name' => 'VIP John']);

    $this->get(route('products.show', $product))
        ->assertOk()
        ->assertSee('Regular Jane')
        ->assertSee('VIP John');
});
