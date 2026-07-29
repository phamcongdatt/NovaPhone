<?php

namespace Tests\Feature;

use Tests\TestCase;

class GuestCheckoutTest extends TestCase
{
    public function test_guest_can_access_checkout_page_without_logging_in(): void
    {
        $response = $this->withSession([])->get(route('checkout'));

        $response->assertRedirectToRoute('cart.index');
        $response->assertSessionHas('error', 'Vui lòng chọn ít nhất một sản phẩm để thanh toán.');
    }
}
