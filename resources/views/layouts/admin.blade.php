<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin &middot; @yield('title', 'Dashboard')</title>
    @vite(['resources/css/app.css'])
</head>
<body class="min-h-screen bg-slate-100 text-slate-900 antialiased">
    <header class="border-b border-slate-200 bg-white">
        <div class="mx-auto flex max-w-6xl items-center justify-between px-6 py-4">
            <a href="{{ route('admin.dashboard') }}" class="text-lg font-semibold tracking-tight">Admin</a>
            <nav class="flex items-center gap-5 text-sm text-slate-500">
                <a href="{{ route('admin.products.index') }}" class="hover:text-slate-900">Products</a>
                <a href="{{ route('products.index') }}" class="text-slate-400 hover:text-slate-700">View storefront &rarr;</a>
            </nav>
        </div>
    </header>

    <main class="mx-auto max-w-6xl px-6 py-10">
        @if (session('status'))
            <div class="mb-6 rounded-md border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                {{ session('status') }}
            </div>
        @endif

        @yield('content')
    </main>
</body>
</html>
