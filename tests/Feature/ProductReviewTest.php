<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductReviewTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_review_product(): void
    {
        $product = $this->createProduct();

        $this->postJson(route('products.review.store', $product), [
            'rating' => 5,
        ])->assertUnauthorized();
    }

    public function test_rating_must_be_between_one_and_five(): void
    {
        $user = User::factory()->create();
        $product = $this->createProduct();

        $this->actingAs($user)
            ->postJson(route('products.review.store', $product), [
                'rating' => 6,
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('rating');
    }

    public function test_user_cannot_review_product_they_have_not_received(): void
    {
        $user = User::factory()->create();
        $product = $this->createProduct();

        $this->actingAs($user)
            ->postJson(route('products.review.store', $product), [
                'rating' => 5,
                'comment' => 'Sản phẩm tốt.',
            ])
            ->assertForbidden();
    }

    public function test_user_cannot_review_delivered_product_that_is_not_paid(): void
    {
        $user = User::factory()->create();
        $product = $this->createProduct();
        $this->createDeliveredOrderWithProduct($user, $product, [
            'payment_status' => 'pending',
        ]);

        $this->actingAs($user)
            ->postJson(route('products.review.store', $product), [
                'rating' => 5,
                'comment' => 'Sản phẩm tốt.',
            ])
            ->assertForbidden();
    }

    public function test_user_can_review_delivered_product_once(): void
    {
        $user = User::factory()->create();
        $product = $this->createProduct();
        $order = $this->createDeliveredOrderWithProduct($user, $product);

        $response = $this->actingAs($user)
            ->postJson(route('products.review.store', $product), [
                'rating' => 5,
                'comment' => 'Máy đẹp, dùng mượt.',
            ]);

        $response
            ->assertCreated()
            ->assertJsonPath('data.rating', 5)
            ->assertJsonPath('data.product_id', $product->id)
            ->assertJsonPath('data.order_id', $order->id);

        $this->assertDatabaseHas('reviews', [
            'user_id' => $user->id,
            'product_id' => $product->id,
            'order_id' => $order->id,
            'rating' => 5,
            'comment' => 'Máy đẹp, dùng mượt.',
        ]);
    }

    public function test_user_cannot_review_same_product_twice(): void
    {
        $user = User::factory()->create();
        $product = $this->createProduct();
        $this->createDeliveredOrderWithProduct($user, $product);

        Review::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'rating' => 4,
        ]);

        $this->actingAs($user)
            ->postJson(route('products.review.store', $product), [
                'rating' => 5,
            ])
            ->assertConflict();
    }

    private function createProduct(array $overrides = []): Product
    {
        $category = Category::create([
            'name' => 'Điện thoại',
            'slug' => 'dien-thoai-' . uniqid(),
            'is_active' => true,
        ]);

        $brand = Brand::create([
            'name' => 'Nova',
            'slug' => 'nova-' . uniqid(),
            'is_active' => true,
        ]);

        return Product::create(array_merge([
            'name' => 'NovaPhone Test',
            'slug' => 'novaphone-test-' . uniqid(),
            'category_id' => $category->id,
            'brand_id' => $brand->id,
            'price' => 10000000,
            'sku' => 'NVP-' . uniqid(),
            'is_active' => true,
        ], $overrides));
    }

    private function createDeliveredOrderWithProduct(User $user, Product $product, array $overrides = []): Order
    {
        $order = Order::create(array_merge([
            'user_id' => $user->id,
            'status' => 'delivered',
            'payment_method' => 'cod',
            'payment_status' => 'paid',
            'subtotal' => 10000000,
            'discount_amount' => 0,
            'shipping_fee' => 0,
            'total_amount' => 10000000,
            'shipping_full_name' => $user->name,
            'shipping_phone' => '0900000000',
            'shipping_address' => '123 Test',
            'shipping_ward' => 'Ward',
            'shipping_district' => 'District',
            'shipping_province' => 'Province',
        ], $overrides));

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'price' => 10000000,
            'quantity' => 1,
            'subtotal' => 10000000,
        ]);

        return $order;
    }
}
