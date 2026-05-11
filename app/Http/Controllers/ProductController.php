<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Product;
use App\Services\PricingEngine;
use Illuminate\Http\Request;
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

    public function show(Request $request, Product $product): View
    {
        $product->load('category');

        $customers = Customer::orderBy('name')->get();
        $selectedCustomer = $request->filled('customer')
            ? $customers->firstWhere('id', (int) $request->query('customer'))
            : null;

        $breakdown = $this->pricing->priceFor($product, $selectedCustomer);

        return view('products.show', [
            'product' => $product,
            'customers' => $customers,
            'selectedCustomer' => $selectedCustomer,
            'breakdown' => $breakdown,
        ]);
    }
}
