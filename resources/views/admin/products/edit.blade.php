@extends('admin.layout')

@section('title', 'Sửa sản phẩm')
@section('page-title', 'Sửa sản phẩm: ' . $product->name)

@section('content')

<form method="POST" action="{{ route('admin.products.update', $product) }}" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    @include('admin.products._form', ['product' => $product, 'categories' => $categories, 'brands' => $brands])
</form>

@endsection