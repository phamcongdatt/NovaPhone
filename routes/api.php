<?php

use App\Http\Controllers\Api\GeminiChatbotController;
use App\Http\Controllers\ProductDetailController;
use Illuminate\Support\Facades\Route;

Route::get('/products/{product:slug}', [ProductDetailController::class, 'apiShow'])
    ->name('api.products.show');

Route::post('/chatbot', [GeminiChatbotController::class, 'chat'])
    ->middleware('throttle:20,1')
    ->name('api.chatbot');
