<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Services\PricingEngine;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function __construct(private readonly PricingEngine $pricing) {}

    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));
        $categorySlug = (string) $request->query('category', '');

        $products = Product::query()
            ->with('category')
            ->when($search !== '', fn ($q) => $q->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            }))
            ->when($categorySlug !== '', fn ($q) => $q->whereHas(
                'category',
                fn ($q) => $q->where('slug', $categorySlug)
            ))
            ->orderBy('title')
            ->paginate(12)
            ->withQueryString();

        return view('products.index', [
            'products' => $products,
            'engine' => $this->pricing,
            'categories' => Category::orderBy('name')->get(),
            'search' => $search,
            'categorySlug' => $categorySlug,
        ]);
    }

    public function show(Product $product): View
    {
        $product->load('category');

        return view('products.show', [
            'product' => $product,
            'breakdown' => $this->pricing->priceFor($product),
        ]);
    }
}
