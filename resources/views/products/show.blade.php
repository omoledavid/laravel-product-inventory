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
            <div class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <span class="text-slate-500">Original</span>
                    <span @class(['text-slate-400 line-through' => $breakdown->hasDiscount()])>{{ $breakdown->original() }}</span>
                </div>

                @if ($breakdown->hasDiscount())
                    <div class="flex justify-between text-emerald-600">
                        <span>Discount ({{ $breakdown->discountLabel }})</span>
                        <span>&minus;{{ $breakdown->discount() }}</span>
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
