@csrf
@php
    $currency = config('app.currency_symbol', '$');
    $currentDiscountType = old('discount_type', $product->discount_type);
    $currentDiscountAmount = old(
        'discount_amount',
        $product->discount_amount_cents !== null ? number_format($product->discount_amount_cents / 100, 2, '.', '') : ''
    );
@endphp
<div class="grid gap-5">
    <div>
        <label for="title" class="block text-sm font-medium text-slate-700">Title</label>
        <input type="text" id="title" name="title" value="{{ old('title', $product->title) }}"
               class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-sm" required>
        @error('title') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="slug" class="block text-sm font-medium text-slate-700">Slug</label>
        <input type="text" id="slug" name="slug" value="{{ old('slug', $product->slug) }}"
               class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-sm" required>
        @error('slug') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="category_id" class="block text-sm font-medium text-slate-700">Category</label>
        <select id="category_id" name="category_id"
                class="mt-1 w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm">
            <option value="">— None —</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}" @selected(old('category_id', $product->category_id) == $category->id)>
                    {{ $category->name }}
                </option>
            @endforeach
        </select>
        @error('category_id') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="price" class="block text-sm font-medium text-slate-700">Price ({{ $currency }})</label>
        <input type="number" step="0.01" min="0" id="price" name="price"
               value="{{ old('price', $product->exists ? number_format($product->price_cents / 100, 2, '.', '') : '') }}"
               class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-sm" required>
        @error('price') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="description" class="block text-sm font-medium text-slate-700">Description</label>
        <textarea id="description" name="description" rows="4"
                  class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-sm" required>{{ old('description', $product->description) }}</textarea>
        @error('description') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
    </div>

    <fieldset class="rounded-md border border-slate-200 p-4">
        <legend class="px-2 text-sm font-medium text-slate-700">Discount</legend>

        <div class="flex flex-wrap gap-4 text-sm">
            <label class="inline-flex items-center gap-2">
                <input type="radio" name="discount_type" value="" @checked($currentDiscountType === null || $currentDiscountType === '')>
                <span>None</span>
            </label>
            <label class="inline-flex items-center gap-2">
                <input type="radio" name="discount_type" value="percent" @checked($currentDiscountType === 'percent')>
                <span>Percentage</span>
            </label>
            <label class="inline-flex items-center gap-2">
                <input type="radio" name="discount_type" value="fixed" @checked($currentDiscountType === 'fixed')>
                <span>Fixed amount</span>
            </label>
        </div>
        @error('discount_type') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror

        <div class="mt-4 grid gap-4 sm:grid-cols-2">
            <div>
                <label for="discount_percent" class="block text-sm font-medium text-slate-700">Percent off (%)</label>
                <input type="number" step="0.01" min="0" max="100" id="discount_percent" name="discount_percent"
                       value="{{ old('discount_percent', $product->discount_percent) }}"
                       class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-sm">
                @error('discount_percent') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="discount_amount" class="block text-sm font-medium text-slate-700">Fixed amount off ({{ $currency }})</label>
                <input type="number" step="0.01" min="0" id="discount_amount" name="discount_amount"
                       value="{{ $currentDiscountAmount }}"
                       class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-sm">
                @error('discount_amount') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
            </div>
        </div>
    </fieldset>

    <div class="flex items-center justify-end gap-3">
        <a href="{{ route('admin.products.index') }}" class="text-sm text-slate-500 hover:text-slate-800">Cancel</a>
        <button type="submit" class="rounded-md bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-700">
            {{ $submitLabel }}
        </button>
    </div>
</div>
