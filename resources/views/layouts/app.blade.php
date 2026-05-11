<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', config('app.name'))</title>
    @vite(['resources/css/app.css'])
</head>
<body class="min-h-screen bg-slate-50 text-slate-900 antialiased">
    <header class="border-b border-slate-200 bg-white">
        <div class="mx-auto flex max-w-5xl items-center justify-between px-6 py-4">
            <a href="{{ route('products.index') }}" class="text-lg font-semibold tracking-tight">
                {{ config('app.name') }}
            </a>
            <nav class="flex items-center gap-5 text-sm text-slate-500">
                <a href="{{ route('products.index') }}" class="hover:text-slate-900">Products</a>
                <a href="{{ route('admin.dashboard') }}" class="hover:text-slate-900">Admin</a>
            </nav>
        </div>
    </header>

    <main class="mx-auto max-w-5xl px-6 py-10">
        @yield('content')
    </main>
</body>
</html>
