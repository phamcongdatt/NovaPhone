<?php

use App\Http\Controllers\ProductDetailController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
})->name('home');

Route::get('/products/{product:slug}', [ProductDetailController::class, 'show'])
    ->name('products.show');
