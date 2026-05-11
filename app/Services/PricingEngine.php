<?php

namespace App\Services;

use App\Models\Product;
use App\Support\PriceBreakdown;

class PricingEngine
{
    public function priceFor(Product $product): PriceBreakdown
    {
        $original = $product->price_cents;

        [$discount, $label] = match ($product->discount_type) {
            Product::DISCOUNT_PERCENT => $this->percentDiscount($original, (float) $product->discount_percent),
            Product::DISCOUNT_FIXED => $this->fixedDiscount($original, (int) $product->discount_amount_cents),
            default => [0, null],
        };

        return new PriceBreakdown(
            originalCents: $original,
            discountCents: $discount,
            finalCents: $original - $discount,
            discountLabel: $discount > 0 ? $label : null,
        );
    }

    private function percentDiscount(int $original, float $percent): array
    {
        if ($percent <= 0) {
            return [0, null];
        }

        $discount = (int) floor($original * $percent / 100);
        $label = rtrim(rtrim(number_format($percent, 2), '0'), '.').'% off';

        return [$discount, $label];
    }

    private function fixedDiscount(int $original, int $amountCents): array
    {
        if ($amountCents <= 0) {
            return [0, null];
        }

        $discount = min($original, $amountCents);
        $label = PriceBreakdown::format($discount).' off';

        return [$discount, $label];
    }
}
