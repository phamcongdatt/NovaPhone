<?php

namespace Tests\Feature;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\Route;
use PHPUnit\Framework\TestCase;

class RouteHealthTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $app = require dirname(__DIR__, 2) . '/bootstrap/app.php';
        $app->make(Kernel::class)->bootstrap();
    }

    protected function tearDown(): void
    {
        restore_error_handler();
        restore_exception_handler();
        parent::tearDown();
    }

    public function test_public_and_admin_routes_are_registered(): void
    {
        $this->assertTrue(Route::has('home'));
        $this->assertTrue(Route::has('checkout'));
        $this->assertTrue(Route::has('checkout.success'));
        $this->assertTrue(Route::has('admin.post-categories.index'));
        $this->assertTrue(Route::has('admin.products.index'));
    }
}
