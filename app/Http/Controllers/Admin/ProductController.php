<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProductRequest;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(): View
    {
        $products = Product::with('category')->orderBy('title')->paginate(15);

        return view('admin.products.index', compact('products'));
    }

    public function create(): View
    {
        return view('admin.products.create', [
            'product' => new Product,
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function store(ProductRequest $request): RedirectResponse
    {
        $product = Product::create($request->validatedAttributes());

        return redirect()
            ->route('admin.products.index')
            ->with('status', "Product \"{$product->title}\" created.");
    }

    public function edit(Product $product): View
    {
        return view('admin.products.edit', [
            'product' => $product,
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function update(ProductRequest $request, Product $product): RedirectResponse
    {
        $product->update($request->validatedAttributes());

        return redirect()
            ->route('admin.products.index')
            ->with('status', "Product \"{$product->title}\" updated.");
    }

    public function destroy(Product $product): RedirectResponse
    {
        $title = $product->title;
        $product->delete();

        return redirect()
            ->route('admin.products.index')
            ->with('status', "Product \"{$title}\" deleted.");
    }
}
