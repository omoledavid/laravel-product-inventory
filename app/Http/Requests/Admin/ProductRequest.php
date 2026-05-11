<?php

namespace App\Http\Requests\Admin;

use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $productId = $this->route('product')?->id;

        return [
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'alpha_dash', Rule::unique('products', 'slug')->ignore($productId)],
            'description' => ['required', 'string'],
            'price' => ['required', 'numeric', 'min:0', 'max:999999.99'],
            'category_id' => ['nullable', Rule::exists('categories', 'id')],
            'discount_type' => ['nullable', Rule::in([Product::DISCOUNT_PERCENT, Product::DISCOUNT_FIXED])],
            'discount_percent' => [
                Rule::requiredIf(fn () => $this->input('discount_type') === Product::DISCOUNT_PERCENT),
                'nullable', 'numeric', 'min:0.01', 'max:100',
            ],
            'discount_amount' => [
                Rule::requiredIf(fn () => $this->input('discount_type') === Product::DISCOUNT_FIXED),
                'nullable', 'numeric', 'min:0.01', 'max:999999.99',
            ],
        ];
    }

    public function validatedAttributes(): array
    {
        $data = $this->validated();

        $type = $data['discount_type'] ?? null;

        return [
            'title' => $data['title'],
            'slug' => $data['slug'],
            'description' => $data['description'],
            'price_cents' => (int) round($data['price'] * 100),
            'category_id' => $data['category_id'] ?? null,
            'discount_type' => $type,
            'discount_percent' => $type === Product::DISCOUNT_PERCENT ? $data['discount_percent'] : null,
            'discount_amount_cents' => $type === Product::DISCOUNT_FIXED ? (int) round($data['discount_amount'] * 100) : null,
        ];
    }
}
