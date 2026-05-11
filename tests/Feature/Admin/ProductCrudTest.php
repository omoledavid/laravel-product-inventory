<?php

use App\Models\Category;
use App\Models\Product;

it('lists products on the admin index', function () {
    Product::factory()->create(['title' => 'Alpha Gadget']);
    Product::factory()->create(['title' => 'Beta Widget']);

    $this->get(route('admin.products.index'))
        ->assertOk()
        ->assertSee('Alpha Gadget')
        ->assertSee('Beta Widget')
        ->assertSee('New product');
});

it('shows discount info on the admin index', function () {
    Product::factory()->withPercentDiscount(10)->create(['title' => 'Pct Item']);
    Product::factory()->withFixedDiscount(500)->create(['title' => 'Fixed Item']);

    $this->get(route('admin.products.index'))
        ->assertOk()
        ->assertSee('10% off')
        ->assertSee('$5.00 off');
});

it('renders the create form with categories', function () {
    Category::factory()->create(['name' => 'Electronics']);

    $this->get(route('admin.products.create'))
        ->assertOk()
        ->assertSee('New product')
        ->assertSee('Electronics')
        ->assertSee('Discount');
});

it('creates a product with no discount', function () {
    $category = Category::factory()->create();

    $payload = [
        'title' => 'Smart Watch',
        'slug' => 'smart-watch',
        'description' => 'Fitness tracking watch.',
        'price' => '249.99',
        'category_id' => $category->id,
        'discount_type' => '',
    ];

    $this->post(route('admin.products.store'), $payload)
        ->assertRedirect(route('admin.products.index'))
        ->assertSessionHas('status');

    $this->assertDatabaseHas('products', [
        'slug' => 'smart-watch',
        'price_cents' => 24999,
        'discount_type' => null,
    ]);
});

it('creates a product with a percentage discount', function () {
    $this->post(route('admin.products.store'), [
        'title' => 'TV',
        'slug' => 'tv',
        'description' => 'A TV.',
        'price' => '500.00',
        'discount_type' => 'percent',
        'discount_percent' => '5',
    ])->assertRedirect(route('admin.products.index'));

    $this->assertDatabaseHas('products', [
        'slug' => 'tv',
        'discount_type' => 'percent',
        'discount_percent' => '5.00',
        'discount_amount_cents' => null,
    ]);
});

it('creates a product with a fixed-amount discount', function () {
    $this->post(route('admin.products.store'), [
        'title' => 'Phone',
        'slug' => 'phone',
        'description' => 'A phone.',
        'price' => '600.00',
        'discount_type' => 'fixed',
        'discount_amount' => '50.00',
    ])->assertRedirect(route('admin.products.index'));

    $this->assertDatabaseHas('products', [
        'slug' => 'phone',
        'discount_type' => 'fixed',
        'discount_amount_cents' => 5000,
        'discount_percent' => null,
    ]);
});

it('requires a value when a discount type is chosen', function () {
    $this->post(route('admin.products.store'), [
        'title' => 'X',
        'slug' => 'x',
        'description' => 'd',
        'price' => '1.00',
        'discount_type' => 'percent',
    ])->assertSessionHasErrors('discount_percent');

    $this->post(route('admin.products.store'), [
        'title' => 'Y',
        'slug' => 'y',
        'description' => 'd',
        'price' => '1.00',
        'discount_type' => 'fixed',
    ])->assertSessionHasErrors('discount_amount');
});

it('rejects an invalid discount type', function () {
    $this->post(route('admin.products.store'), [
        'title' => 'X',
        'slug' => 'x',
        'description' => 'd',
        'price' => '1.00',
        'discount_type' => 'bogus',
    ])->assertSessionHasErrors('discount_type');
});

it('rejects a percentage above 100', function () {
    $this->post(route('admin.products.store'), [
        'title' => 'X',
        'slug' => 'x',
        'description' => 'd',
        'price' => '1.00',
        'discount_type' => 'percent',
        'discount_percent' => '150',
    ])->assertSessionHasErrors('discount_percent');
});

it('rejects invalid input on store', function () {
    $this->post(route('admin.products.store'), [])
        ->assertSessionHasErrors(['title', 'slug', 'description', 'price']);

    expect(Product::count())->toBe(0);
});

it('rejects a duplicate slug on store', function () {
    Product::factory()->create(['slug' => 'taken']);

    $this->post(route('admin.products.store'), [
        'title' => 'X',
        'slug' => 'taken',
        'description' => 'd',
        'price' => '1.00',
    ])->assertSessionHasErrors('slug');
});

it('renders the edit form pre-filled with discount values', function () {
    $product = Product::factory()->withPercentDiscount(12.5)->create([
        'title' => 'Existing',
        'slug' => 'existing',
        'price_cents' => 12345,
    ]);

    $this->get(route('admin.products.edit', $product))
        ->assertOk()
        ->assertSee('Existing')
        ->assertSee('123.45')
        ->assertSee('value="12.50"', false);
});

it('updates a product including switching discount type', function () {
    $product = Product::factory()->withPercentDiscount(5)->create(['slug' => 'old-slug', 'price_cents' => 1000]);

    $this->put(route('admin.products.update', $product), [
        'title' => 'Updated',
        'slug' => 'old-slug',
        'description' => 'New description.',
        'price' => '55.50',
        'discount_type' => 'fixed',
        'discount_amount' => '5.00',
    ])->assertRedirect(route('admin.products.index'));

    expect($product->fresh())
        ->title->toBe('Updated')
        ->price_cents->toBe(5550)
        ->discount_type->toBe('fixed')
        ->discount_amount_cents->toBe(500)
        ->discount_percent->toBeNull();
});

it('clears the discount when type is set to none', function () {
    $product = Product::factory()->withFixedDiscount(500)->create();

    $this->put(route('admin.products.update', $product), [
        'title' => $product->title,
        'slug' => $product->slug,
        'description' => $product->description,
        'price' => '1.00',
        'discount_type' => '',
    ])->assertSessionHasNoErrors();

    expect($product->fresh())
        ->discount_type->toBeNull()
        ->discount_amount_cents->toBeNull()
        ->discount_percent->toBeNull();
});

it('deletes a product', function () {
    $product = Product::factory()->create();

    $this->delete(route('admin.products.destroy', $product))
        ->assertRedirect(route('admin.products.index'));

    expect(Product::find($product->id))->toBeNull();
});
