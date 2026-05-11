@extends('layouts.admin')

@section('title', 'New category')

@section('content')
    <h1 class="text-2xl font-semibold tracking-tight">New category</h1>

    <form method="POST" action="{{ route('admin.categories.store') }}" class="mt-6 max-w-xl rounded-lg border border-slate-200 bg-white p-6">
        @include('admin.categories._form', ['submitLabel' => 'Create category'])
    </form>
@endsection
