@csrf
<div class="grid gap-5">
    <div>
        <label for="name" class="block text-sm font-medium text-slate-700">Name</label>
        <input type="text" id="name" name="name" value="{{ old('name', $category->name) }}"
               class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-sm" required>
        @error('name') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="slug" class="block text-sm font-medium text-slate-700">Slug</label>
        <input type="text" id="slug" name="slug" value="{{ old('slug', $category->slug) }}"
               class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-sm" required>
        @error('slug') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
    </div>

    <div class="flex items-center justify-end gap-3">
        <a href="{{ route('admin.categories.index') }}" class="text-sm text-slate-500 hover:text-slate-800">Cancel</a>
        <button type="submit" class="rounded-md bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-700">
            {{ $submitLabel }}
        </button>
    </div>
</div>
