<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Product;
use App\Support\PriceBreakdown;

class PricingEngine
{
    public function priceFor(Product $product, ?Customer $customer = null): PriceBreakdown
    {
        $original = $product->price_cents;

        $categoryPercent = (float) ($product->category?->discount_percent ?? 0);
        $categoryDiscount = $this->discountCents($original, $categoryPercent);

        $afterCategory = $original - $categoryDiscount;

        $customerPercent = (float) ($customer?->discount_percent ?? 0);
        $customerDiscount = $this->discountCents($afterCategory, $customerPercent);

        $final = $afterCategory - $customerDiscount;

        return new PriceBreakdown(
            originalCents: $original,
            categoryDiscountCents: $categoryDiscount,
            customerDiscountCents: $customerDiscount,
            finalCents: $final,
        );
    }

    private function discountCents(int $subtotal, float $percent): int
    {
        if ($percent <= 0) {
            return 0;
        }

        return (int) floor($subtotal * $percent / 100);
    }
}
