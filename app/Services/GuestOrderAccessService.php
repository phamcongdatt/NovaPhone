<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\URL;

class GuestOrderAccessService
{
    private const ACCESS_DAYS = 30;

    public function showUrl(Order $order): string
    {
        return $this->signedUrl('guest.orders.show', $order);
    }

    public function cancelUrl(Order $order): string
    {
        return $this->signedUrl('guest.orders.cancel', $order);
    }

    public function paymentUrl(Order $order): string
    {
        return $this->signedUrl('guest.orders.pay', $order);
    }

    private function signedUrl(string $route, Order $order): string
    {
        return URL::temporarySignedRoute(
            $route,
            now()->addDays(self::ACCESS_DAYS),
            ['order' => $order]
        );
    }
}
