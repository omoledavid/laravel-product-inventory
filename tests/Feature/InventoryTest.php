<?php

use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;

it('creates a product belonging to a category', function () {
    $category = Category::factory()->withDiscount(5)->create(['slug' => 'electronics']);
    $product = Product::factory()->for($category)->create(['price_cents' => 19999]);

    expect($product->category)->toBeInstanceOf(Category::class)
        ->and($product->category->slug)->toBe('electronics')
        ->and((float) $product->category->discount_percent)->toBe(5.00)
        ->and($product->price_cents)->toBe(19999);
});

it('exposes products via the category relationship', function () {
    $category = Category::factory()->create();
    Product::factory()->count(3)->for($category)->create();

    expect($category->products)->toHaveCount(3);
});

it('nulls the category_id on a product when its category is deleted', function () {
    $category = Category::factory()->create();
    $product = Product::factory()->for($category)->create();

    $category->delete();

    expect($product->fresh()->category_id)->toBeNull();
});

it('creates a special customer with a discount', function () {
    $customer = Customer::factory()->special(10)->create();

    expect((float) $customer->discount_percent)->toBe(10.00);
});

it('creates a regular customer with no discount', function () {
    $customer = Customer::factory()->create();

    expect($customer->discount_percent)->toBeNull();
});

it('enforces unique slugs on categories and products', function () {
    Category::factory()->create(['slug' => 'electronics']);
    Product::factory()->create(['slug' => 'duplicate-slug']);

    expect(fn () => Category::factory()->create(['slug' => 'electronics']))
        ->toThrow(Illuminate\Database\QueryException::class);

    expect(fn () => Product::factory()->create(['slug' => 'duplicate-slug']))
        ->toThrow(Illuminate\Database\QueryException::class);
});

it('resolves a product by slug for route model binding', function () {
    $product = Product::factory()->create(['slug' => 'wireless-headphones']);

    expect((new Product)->getRouteKeyName())->toBe('slug')
        ->and(Product::where('slug', 'wireless-headphones')->first()->is($product))->toBeTrue();
});

it('seeds the expected catalog', function () {
    $this->seed();

    expect(Category::count())->toBe(3)
        ->and(Product::count())->toBe(5)
        ->and(Customer::count())->toBe(2)
        ->and(Category::where('slug', 'electronics')->value('discount_percent'))->toEqual('5.00')
        ->and(Customer::where('email', 'john@example.com')->value('discount_percent'))->toEqual('10.00');
});
