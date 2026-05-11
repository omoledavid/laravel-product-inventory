@extends('layouts.admin')

@section('title', 'Edit '.$product->title)

@section('content')
    <h1 class="text-2xl font-semibold tracking-tight">Edit product</h1>

    <form method="POST" action="{{ route('admin.products.update', $product) }}" class="mt-6 rounded-lg border border-slate-200 bg-white p-6">
        @method('PUT')
        @include('admin.products._form', ['submitLabel' => 'Save changes'])
    </form>
@endsection
