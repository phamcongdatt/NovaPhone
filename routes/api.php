<?php

use App\Http\Controllers\ProductDetailController;
use Illuminate\Support\Facades\Route;

Route::get('/products/{product:slug}', [ProductDetailController::class, 'apiShow'])
    ->name('api.products.show');
