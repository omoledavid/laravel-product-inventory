<?php

use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use App\Services\PricingEngine;
use App\Support\PriceBreakdown;

beforeEach(function () {
    $this->engine = app(PricingEngine::class);
});

it('returns the original price when there are no discounts', function () {
    $category = Category::factory()->create(['discount_percent' => null]);
    $product = Product::factory()->for($category)->create(['price_cents' => 10000]);
    $customer = Customer::factory()->create(['discount_percent' => null]);

    $breakdown = $this->engine->priceFor($product, $customer);

    expect($breakdown->originalCents)->toBe(10000)
        ->and($breakdown->categoryDiscountCents)->toBe(0)
        ->and($breakdown->customerDiscountCents)->toBe(0)
        ->and($breakdown->finalCents)->toBe(10000)
        ->and($breakdown->hasDiscount())->toBeFalse();
});

it('applies only the category discount when no customer is provided', function () {
    $category = Category::factory()->withDiscount(5)->create();
    $product = Product::factory()->for($category)->create(['price_cents' => 19999]);

    $breakdown = $this->engine->priceFor($product);

    expect($breakdown->categoryDiscountCents)->toBe(999)
        ->and($breakdown->customerDiscountCents)->toBe(0)
        ->and($breakdown->finalCents)->toBe(19000);
});

it('applies only the customer discount when category has none', function () {
    $category = Category::factory()->create(['discount_percent' => null]);
    $product = Product::factory()->for($category)->create(['price_cents' => 10000]);
    $customer = Customer::factory()->special(10)->create();

    $breakdown = $this->engine->priceFor($product, $customer);

    expect($breakdown->categoryDiscountCents)->toBe(0)
        ->and($breakdown->customerDiscountCents)->toBe(1000)
        ->and($breakdown->finalCents)->toBe(9000);
});

it('stacks category then customer discounts sequentially', function () {
    $category = Category::factory()->withDiscount(5)->create();
    $product = Product::factory()->for($category)->create(['price_cents' => 20000]);
    $customer = Customer::factory()->special(10)->create();

    $breakdown = $this->engine->priceFor($product, $customer);

    expect($breakdown->originalCents)->toBe(20000)
        ->and($breakdown->categoryDiscountCents)->toBe(1000)
        ->and($breakdown->customerDiscountCents)->toBe(1900)
        ->and($breakdown->finalCents)->toBe(17100)
        ->and($breakdown->totalDiscountCents())->toBe(2900);
});

it('handles a product with no category', function () {
    $product = Product::factory()->create(['category_id' => null, 'price_cents' => 5000]);
    $customer = Customer::factory()->special(10)->create();

    $breakdown = $this->engine->priceFor($product, $customer);

    expect($breakdown->categoryDiscountCents)->toBe(0)
        ->and($breakdown->customerDiscountCents)->toBe(500)
        ->and($breakdown->finalCents)->toBe(4500);
});

it('floors discount cents to avoid fractional currency', function () {
    $category = Category::factory()->withDiscount(7.5)->create();
    $product = Product::factory()->for($category)->create(['price_cents' => 333]);

    $breakdown = $this->engine->priceFor($product);

    expect($breakdown->categoryDiscountCents)->toBe(24)
        ->and($breakdown->finalCents)->toBe(309);
});

it('formats currency values using the configured symbol', function () {
    config()->set('app.currency_symbol', '$');
    $breakdown = new PriceBreakdown(20000, 1000, 1900, 17100);

    expect($breakdown->original())->toBe('$200.00')
        ->and($breakdown->categoryDiscount())->toBe('$10.00')
        ->and($breakdown->customerDiscount())->toBe('$19.00')
        ->and($breakdown->final())->toBe('$171.00');
});

it('is bound as a singleton in the container', function () {
    expect(app(PricingEngine::class))->toBe(app(PricingEngine::class));
});
