@extends('layouts.admin')

@section('title', 'Edit '.$category->name)

@section('content')
    <h1 class="text-2xl font-semibold tracking-tight">Edit category</h1>

    <form method="POST" action="{{ route('admin.categories.update', $category) }}" class="mt-6 max-w-xl rounded-lg border border-slate-200 bg-white p-6">
        @method('PUT')
        @include('admin.categories._form', ['submitLabel' => 'Save changes'])
    </form>
@endsection
