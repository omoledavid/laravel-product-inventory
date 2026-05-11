<?php

use App\Models\Product;
use App\Services\PricingEngine;
use App\Support\PriceBreakdown;

beforeEach(function () {
    $this->engine = app(PricingEngine::class);
});

it('returns the original price when there is no discount', function () {
    $product = Product::factory()->create(['price_cents' => 10000]);

    $breakdown = $this->engine->priceFor($product);

    expect($breakdown->originalCents)->toBe(10000)
        ->and($breakdown->discountCents)->toBe(0)
        ->and($breakdown->finalCents)->toBe(10000)
        ->and($breakdown->hasDiscount())->toBeFalse()
        ->and($breakdown->discountLabel)->toBeNull();
});

it('applies a percentage discount', function () {
    $product = Product::factory()->withPercentDiscount(5)->create(['price_cents' => 19999]);

    $breakdown = $this->engine->priceFor($product);

    expect($breakdown->discountCents)->toBe(999)
        ->and($breakdown->finalCents)->toBe(19000)
        ->and($breakdown->discountLabel)->toBe('5% off');
});

it('applies a fixed-amount discount', function () {
    $product = Product::factory()->withFixedDiscount(2000)->create(['price_cents' => 10000]);

    $breakdown = $this->engine->priceFor($product);

    expect($breakdown->discountCents)->toBe(2000)
        ->and($breakdown->finalCents)->toBe(8000)
        ->and($breakdown->discountLabel)->toBe('$20.00 off');
});

it('caps a fixed discount at the original price', function () {
    $product = Product::factory()->withFixedDiscount(50000)->create(['price_cents' => 10000]);

    $breakdown = $this->engine->priceFor($product);

    expect($breakdown->discountCents)->toBe(10000)
        ->and($breakdown->finalCents)->toBe(0);
});

it('floors a fractional percentage discount', function () {
    $product = Product::factory()->withPercentDiscount(7.5)->create(['price_cents' => 333]);

    $breakdown = $this->engine->priceFor($product);

    expect($breakdown->discountCents)->toBe(24)
        ->and($breakdown->finalCents)->toBe(309);
});

it('formats currency values using the configured symbol', function () {
    config()->set('app.currency_symbol', '$');
    $breakdown = new PriceBreakdown(20000, 1000, 19000, '5% off');

    expect($breakdown->original())->toBe('$200.00')
        ->and($breakdown->discount())->toBe('$10.00')
        ->and($breakdown->final())->toBe('$190.00');
});

it('is bound as a singleton in the container', function () {
    expect(app(PricingEngine::class))->toBe(app(PricingEngine::class));
});
