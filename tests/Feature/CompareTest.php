<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CompareTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_open_an_empty_compare_page(): void
    {
        $this->get(route('compare.index'))
            ->assertOk()
            ->assertSee('Chưa có sản phẩm để so sánh');
    }

    public function test_guest_can_add_an_active_product_to_compare_list(): void
    {
        $product = $this->createProduct();

        $this->postJson(route('compare.add'), ['product_id' => $product->id])
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('action', 'added')
            ->assertJsonPath('count', 1)
            ->assertJsonPath('product_ids', [$product->id]);

        $this->assertEquals([$product->id], session('compare_products'));
    }

    public function test_duplicate_product_is_not_added_twice(): void
    {
        $product = $this->createProduct();

        $this->postJson(route('compare.add'), ['product_id' => $product->id]);

        $this->postJson(route('compare.add'), ['product_id' => $product->id])
            ->assertUnprocessable()
            ->assertJsonPath('success', false);

        $this->assertEquals([$product->id], session('compare_products'));
    }

    public function test_inactive_product_cannot_be_added_to_compare_list(): void
    {
        $product = $this->createProduct(['is_active' => false]);

        $this->postJson(route('compare.add'), ['product_id' => $product->id])
            ->assertUnprocessable()
            ->assertJsonPath('success', false)
            ->assertJsonPath('message', 'Sản phẩm không tồn tại hoặc đã ngừng kinh doanh.');

        $this->assertNull(session('compare_products'));
    }

    public function test_fifth_product_is_rejected_without_changing_existing_list(): void
    {
        $products = collect(range(1, 5))->map(fn () => $this->createProduct());

        $products->take(4)->each(function (Product $product) {
            $this->postJson(route('compare.add'), ['product_id' => $product->id])->assertOk();
        });

        $this->postJson(route('compare.add'), ['product_id' => $products->last()->id])
            ->assertUnprocessable()
            ->assertJsonPath('message', 'Bạn chỉ có thể so sánh tối đa 4 sản phẩm.');

        $this->assertEquals($products->take(4)->pluck('id')->all(), session('compare_products'));
    }

    public function test_guest_can_remove_and_clear_products(): void
    {
        $first = $this->createProduct();
        $second = $this->createProduct();
        $this->withSession(['compare_products' => [$first->id, $second->id]]);

        $this->deleteJson(route('compare.remove', $first))
            ->assertOk()
            ->assertJsonPath('action', 'removed')
            ->assertJsonPath('count', 1)
            ->assertJsonPath('product_ids', [$second->id]);

        $this->deleteJson(route('compare.clear'))
            ->assertOk()
            ->assertJsonPath('action', 'cleared')
            ->assertJsonPath('count', 0)
            ->assertJsonPath('product_ids', []);

        $this->assertNull(session('compare_products'));
    }

    public function test_compare_page_preserves_selection_order_and_excludes_inactive_products(): void
    {
        $first = $this->createProduct(['name' => 'Sản phẩm đầu tiên']);
        $inactive = $this->createProduct(['name' => 'Sản phẩm ngừng bán', 'is_active' => false]);
        $last = $this->createProduct(['name' => 'Sản phẩm cuối cùng']);

        $response = $this->withSession([
            'compare_products' => [$last->id, $inactive->id, $first->id],
        ])->get(route('compare.index'));

        $response->assertOk()
            ->assertSeeInOrder(['Sản phẩm cuối cùng', 'Sản phẩm đầu tiên'])
            ->assertDontSee('Sản phẩm ngừng bán')
            ->assertSee('Chip / SoC');

        $this->assertEquals([$last->id, $first->id], session('compare_products'));
    }

    public function test_authenticated_user_uses_the_same_session_based_compare_list(): void
    {
        $user = User::factory()->create();
        $product = $this->createProduct();

        $this->actingAs($user)
            ->postJson(route('compare.add'), ['product_id' => $product->id])
            ->assertOk()
            ->assertJsonPath('count', 1);

        $this->assertEquals([$product->id], session('compare_products'));
    }

    private function createProduct(array $overrides = []): Product
    {
        $category = Category::create([
            'name' => 'Điện thoại '.uniqid(),
            'slug' => 'dien-thoai-'.uniqid(),
            'is_active' => true,
        ]);

        $brand = Brand::create([
            'name' => 'Nova '.uniqid(),
            'slug' => 'nova-'.uniqid(),
            'is_active' => true,
        ]);

        return Product::create(array_merge([
            'name' => 'NovaPhone Test '.uniqid(),
            'slug' => 'novaphone-test-'.uniqid(),
            'category_id' => $category->id,
            'brand_id' => $brand->id,
            'price' => 10000000,
            'sku' => 'NVP-'.uniqid(),
            'is_active' => true,
        ], $overrides));
    }
}