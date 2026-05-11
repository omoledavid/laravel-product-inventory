@extends('layouts.app')

@section('title', 'Products')

@section('content')
    <h1 class="text-2xl font-semibold tracking-tight">Products</h1>
    <p class="mt-2 text-sm text-slate-500">Browse the catalog. Discounts apply on the product page.</p>

    <div class="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @forelse ($products as $product)
            @php($breakdown = $engine->priceFor($product))
            <a href="{{ route('products.show', $product) }}"
               class="group block rounded-lg border border-slate-200 bg-white p-5 transition hover:border-slate-400 hover:shadow-sm">
                <div class="flex items-center justify-between">
                    <h2 class="font-medium group-hover:text-indigo-600">{{ $product->title }}</h2>
                    @if ($product->category)
                        <span class="rounded-full bg-slate-100 px-2 py-0.5 text-xs text-slate-600">
                            {{ $product->category->name }}
                        </span>
                    @endif
                </div>
                <p class="mt-2 line-clamp-2 text-sm text-slate-500">{{ $product->description }}</p>
                <div class="mt-4 flex items-baseline gap-2">
                    @if ($breakdown->hasDiscount())
                        <span class="text-sm text-slate-400 line-through">{{ $breakdown->original() }}</span>
                    @endif
                    <span class="text-lg font-semibold">{{ $breakdown->final() }}</span>
                </div>
            </a>
        @empty
            <p class="col-span-full text-sm text-slate-500">No products yet.</p>
        @endforelse
    </div>
@endsection
