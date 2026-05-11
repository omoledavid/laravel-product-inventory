<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    /** @use HasFactory<\Database\Factories\ProductFactory> */
    use HasFactory;

    public const DISCOUNT_PERCENT = 'percent';

    public const DISCOUNT_FIXED = 'fixed';

    protected $fillable = [
        'category_id',
        'title',
        'slug',
        'description',
        'price_cents',
        'discount_type',
        'discount_percent',
        'discount_amount_cents',
    ];

    protected function casts(): array
    {
        return [
            'price_cents' => 'integer',
            'discount_percent' => 'decimal:2',
            'discount_amount_cents' => 'integer',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
