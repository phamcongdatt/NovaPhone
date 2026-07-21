<?php

namespace App\Providers;

use App\Events\OrderCancelled;
use App\Listeners\SendOrderCancelledNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        OrderCancelled::class => [
            SendOrderCancelledNotification::class,
        ],
    ];

    public function boot(): void
    {
        //
    }
}
