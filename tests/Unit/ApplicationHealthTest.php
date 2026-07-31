<?php

namespace Tests\Unit;

use App\Http\Controllers\Admin\PostCategoryController;
use App\Models\OrderItem;
use PHPUnit\Framework\TestCase;

class ApplicationHealthTest extends TestCase
{
    public function test_core_application_classes_are_loadable(): void
    {
        $this->assertTrue(class_exists(PostCategoryController::class));
        $this->assertTrue(class_exists(OrderItem::class));
    }
}
