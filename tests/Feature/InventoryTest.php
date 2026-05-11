<?php

use App\Models\Category;
use App\Models\Product;

it('creates a product belonging to a category', function () {
    $category = Category::factory()->create(['slug' => 'electronics']);
    $product = Product::factory()->for($category)->create(['price_cents' => 19999]);

    expect($product->category)->toBeInstanceOf(Category::class)
        ->and($product->category->slug)->toBe('electronics')
        ->and($product->price_cents)->toBe(19999);
});

it('exposes products via the category relationship', function () {
    $category = Category::factory()->create();
    Product::factory()->count(3)->for($category)->create();

    expect($category->products)->toHaveCount(3);
});

it('nulls the category_id when its category is deleted', function () {
    $category = Category::factory()->create();
    $product = Product::factory()->for($category)->create();

    $category->delete();

    expect($product->fresh()->category_id)->toBeNull();
});

it('persists percentage discount fields', function () {
    $product = Product::factory()->withPercentDiscount(10)->create();

    expect($product->discount_type)->toBe('percent')
        ->and((float) $product->discount_percent)->toBe(10.00)
        ->and($product->discount_amount_cents)->toBeNull();
});

it('persists fixed discount fields', function () {
    $product = Product::factory()->withFixedDiscount(500)->create();

    expect($product->discount_type)->toBe('fixed')
        ->and($product->discount_amount_cents)->toBe(500)
        ->and($product->discount_percent)->toBeNull();
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
        ->and(Product::where('slug', 'wireless-headphones')->value('discount_type'))->toBe('percent')
        ->and(Product::where('slug', 'smart-watch')->value('discount_type'))->toBe('fixed');
});
