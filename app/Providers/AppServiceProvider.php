<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \Illuminate\Support\Facades\View::composer('*', function ($view) {
            if (! request()->is('api/*') && request()->hasSession()) {
                $cartService = app(\App\Services\CartService::class);
                $view->with([
                    'cartCount' => $cartService->getCount(),
                    'cartTotal' => $cartService->getTotal(),
                    'cartItems' => $cartService->getItems(),
                ]);
            } else {
                $view->with([
                    'cartCount' => 0,
                    'cartTotal' => 0,
                    'cartItems' => collect(),
                ]);
            }
        });
    }
}
