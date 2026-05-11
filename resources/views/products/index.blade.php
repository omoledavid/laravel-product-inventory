@extends('layouts.app')

@section('title', 'Products')

@section('content')
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="text-2xl font-semibold tracking-tight">Products</h1>
            <p class="mt-2 text-sm text-slate-500">Browse the catalog. Discounts apply on the product page.</p>
        </div>
    </div>

    <form method="GET" action="{{ route('products.index') }}"
          class="mt-6 flex flex-wrap items-center gap-3 rounded-lg border border-slate-200 bg-white p-4">
        <div class="flex-1 min-w-[14rem]">
            <label for="search" class="sr-only">Search</label>
            <input type="search" id="search" name="search" value="{{ $search }}"
                   placeholder="Search by title or description"
                   class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm">
        </div>
        <div>
            <label for="category" class="sr-only">Category</label>
            <select id="category" name="category"
                    class="rounded-md border border-slate-300 bg-white px-3 py-2 text-sm">
                <option value="">All categories</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->slug }}" @selected($categorySlug === $category->slug)>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <button type="submit"
                class="rounded-md bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-700">
            Apply
        </button>
        @if ($search !== '' || $categorySlug !== '')
            <a href="{{ route('products.index') }}" class="text-sm text-slate-500 hover:text-slate-900">Clear</a>
        @endif
    </form>

    <p class="mt-4 text-xs text-slate-500">
        Showing {{ $products->firstItem() ?? 0 }}–{{ $products->lastItem() ?? 0 }} of {{ $products->total() }}
    </p>

    <div class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
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
            <p class="col-span-full text-sm text-slate-500">No products match your filters.</p>
        @endforelse
    </div>

    <div class="mt-8">
        {{ $products->links() }}
    </div>
@endsection
