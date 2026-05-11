@extends('layouts.admin')

@section('title', 'Products')

@section('content')
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-semibold tracking-tight">Products</h1>
        <a href="{{ route('admin.products.create') }}"
           class="rounded-md bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-700">
            + New product
        </a>
    </div>

    <div class="mt-8 overflow-hidden rounded-lg border border-slate-200 bg-white">
        <table class="min-w-full divide-y divide-slate-200 text-sm">
            <thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-500">
                <tr>
                    <th class="px-4 py-3">Title</th>
                    <th class="px-4 py-3">Category</th>
                    <th class="px-4 py-3 text-right">Price</th>
                    <th class="px-4 py-3">Discount</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($products as $product)
                    <tr>
                        <td class="px-4 py-3">
                            <div class="font-medium">{{ $product->title }}</div>
                            <div class="text-xs text-slate-500">{{ $product->slug }}</div>
                        </td>
                        <td class="px-4 py-3 text-slate-600">{{ $product->category?->name ?? '—' }}</td>
                        <td class="px-4 py-3 text-right">${{ number_format($product->price_cents / 100, 2) }}</td>
                        <td class="px-4 py-3 text-slate-600">
                            @if ($product->discount_type === 'percent')
                                {{ rtrim(rtrim($product->discount_percent, '0'), '.') }}% off
                            @elseif ($product->discount_type === 'fixed')
                                ${{ number_format($product->discount_amount_cents / 100, 2) }} off
                            @else
                                —
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex justify-end gap-3">
                                <a href="{{ route('admin.products.edit', $product) }}" class="text-indigo-600 hover:text-indigo-800">Edit</a>
                                <form method="POST" action="{{ route('admin.products.destroy', $product) }}"
                                      onsubmit="return confirm('Delete this product?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-rose-600 hover:text-rose-800">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-6 text-center text-slate-500">No products yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $products->links() }}
    </div>
@endsection
