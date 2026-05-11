<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\PricingEngine;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function __construct(private readonly PricingEngine $pricing) {}

    public function index(): View
    {
        $products = Product::with('category')->orderBy('title')->get();

        return view('products.index', [
            'products' => $products,
            'engine' => $this->pricing,
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
