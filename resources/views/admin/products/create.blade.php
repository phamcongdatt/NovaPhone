@extends('admin.layout')

@section('title', 'Thêm sản phẩm')
@section('page-title', 'Thêm sản phẩm mới')

@section('content')

<form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data">
    @csrf
    @include('admin.products._form', ['product' => null, 'categories' => $categories, 'brands' => $brands])
</form>

@endsection