<?php

namespace App\Support;

final readonly class PriceBreakdown
{
    public function __construct(
        public int $originalCents,
        public int $discountCents,
        public int $finalCents,
        public ?string $discountLabel = null,
    ) {}

    public function hasDiscount(): bool
    {
        return $this->discountCents > 0;
    }

    public function original(): string
    {
        return self::format($this->originalCents);
    }

    public function discount(): string
    {
        return self::format($this->discountCents);
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
