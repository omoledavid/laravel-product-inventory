<?php

namespace App\Support;

final readonly class PriceBreakdown
{
    public function __construct(
        public int $originalCents,
        public int $categoryDiscountCents,
        public int $customerDiscountCents,
        public int $finalCents,
    ) {}

    public function totalDiscountCents(): int
    {
        return $this->categoryDiscountCents + $this->customerDiscountCents;
    }

    public function hasDiscount(): bool
    {
        return $this->totalDiscountCents() > 0;
    }

    public function original(): string
    {
        return self::format($this->originalCents);
    }

    public function categoryDiscount(): string
    {
        return self::format($this->categoryDiscountCents);
    }

    public function customerDiscount(): string
    {
        return self::format($this->customerDiscountCents);
    }

    public function final(): string
    {
        return self::format($this->finalCents);
    }

    public static function format(int $cents): string
    {
        $symbol = config('app.currency_symbol', '$');

        return $symbol.number_format($cents / 100, 2);
    }
}
