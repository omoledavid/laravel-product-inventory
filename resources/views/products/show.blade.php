@extends('layouts.app')

@section('title', $product->title)

@section('content')
    <a href="{{ route('products.index') }}" class="text-sm text-slate-500 hover:text-slate-900">&larr; Back to products</a>

    <article class="mt-6 grid gap-10 lg:grid-cols-[2fr_1fr]">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-3xl font-semibold tracking-tight">{{ $product->title }}</h1>
                @if ($product->category)
                    <span class="rounded-full bg-slate-100 px-2.5 py-0.5 text-xs text-slate-600">
                        {{ $product->category->name }}
                    </span>
                @endif
            </div>

            <p class="mt-4 text-slate-600">{{ $product->description }}</p>
        </div>

        <aside class="rounded-lg border border-slate-200 bg-white p-6">
            <form method="GET" action="{{ route('products.show', $product) }}" class="space-y-2">
                <label for="customer" class="block text-xs font-medium uppercase tracking-wide text-slate-500">
                    Customer
                </label>
                <select id="customer" name="customer" onchange="this.form.submit()"
                        class="w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm">
                    <option value="">Guest (no customer discount)</option>
                    @foreach ($customers as $customer)
                        <option value="{{ $customer->id }}" @selected($selectedCustomer?->id === $customer->id)>
                            {{ $customer->name }}@if ($customer->discount_percent) ({{ rtrim(rtrim($customer->discount_percent, '0'), '.') }}% off)@endif
                        </option>
                    @endforeach
                </select>
            </form>

            <div class="mt-6 space-y-2 text-sm">
                <div class="flex justify-between">
                    <span class="text-slate-500">Original</span>
                    <span @class(['text-slate-400 line-through' => $breakdown->hasDiscount()])>{{ $breakdown->original() }}</span>
                </div>

                @if ($breakdown->categoryDiscountCents > 0)
                    <div class="flex justify-between text-emerald-600">
                        <span>Category discount ({{ rtrim(rtrim($product->category->discount_percent, '0'), '.') }}%)</span>
                        <span>&minus;{{ $breakdown->categoryDiscount() }}</span>
                    </div>
                @endif

                @if ($breakdown->customerDiscountCents > 0)
                    <div class="flex justify-between text-emerald-600">
                        <span>Customer discount ({{ rtrim(rtrim($selectedCustomer->discount_percent, '0'), '.') }}%)</span>
                        <span>&minus;{{ $breakdown->customerDiscount() }}</span>
                    </div>
                @endif

                <div class="mt-3 flex items-baseline justify-between border-t border-slate-200 pt-3">
                    <span class="text-slate-500">Final price</span>
                    <span class="text-2xl font-semibold">{{ $breakdown->final() }}</span>
                </div>
            </div>
        </aside>
    </article>
@endsection
