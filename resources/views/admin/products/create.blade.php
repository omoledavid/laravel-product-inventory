@extends('layouts.admin')

@section('title', 'New product')

@section('content')
    <h1 class="text-2xl font-semibold tracking-tight">New product</h1>

    <form method="POST" action="{{ route('admin.products.store') }}" class="mt-6 rounded-lg border border-slate-200 bg-white p-6">
        @include('admin.products._form', ['submitLabel' => 'Create product'])
    </form>
@endsection
