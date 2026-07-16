<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductPerformance;
use App\Services\CompareService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class PerformanceComparisonTest extends TestCase
{
    use RefreshDatabase;

    private function createProduct(array $overrides = []): Product
    {
        $category = Category::create([
            'name' => 'Test Category '.uniqid(),
            'slug' => 'test-category-'.uniqid(),
            'is_active' => true,
        ]);

        $brand = Brand::create([
            'name' => 'Test Brand '.uniqid(),
            'slug' => 'test-brand-'.uniqid(),
            'is_active' => true,
        ]);

        $product = Product::create(array_merge([
            'name' => 'Test Phone '.uniqid(),
            'slug' => 'test-phone-'.uniqid(),
            'category_id' => $category->id,
            'brand_id' => $brand->id,
            'price' => 10000000,
            'sku' => 'TP-'.uniqid(),
            'is_active' => true,
        ], $overrides));

        return $product;
    }

    public function test_migration_creates_separate_product_performances_table(): void
    {
        $this->assertTrue(Schema::hasTable('product_performances'));

        foreach ([
            'product_id',
            'chipset',
            'antutu_score',
            'geekbench_single',
            'battery_mah',
            'charging_speed_w',
            'ram',
            'network_support',
        ] as $column) {
            $this->assertTrue(Schema::hasColumn('product_performances', $column));
        }

        $this->assertFalse(Schema::hasColumn('products', 'chipset'));
        $this->assertFalse(Schema::hasColumn('products', 'antutu_score'));
    }

    public function test_product_has_one_performance_record(): void
    {
        $product = $this->createProduct();
        $performance = ProductPerformance::create([
            'product_id' => $product->id,
            'chipset' => 'Snapdragon 8 Gen 3',
            'antutu_score' => 1500000,
            'battery_mah' => 5000,
        ]);

        $this->assertTrue($product->performance()->is($performance));
        $this->assertSame('Snapdragon 8 Gen 3', $product->fresh()->performance->chipset);
        $this->assertSame(1500000.0, (float) $product->fresh()->performance->antutu_score);
    }

    public function test_performance_record_is_deleted_with_hard_deleted_product(): void
    {
        $product = $this->createProduct();
        $performance = ProductPerformance::create([
            'product_id' => $product->id,
            'chipset' => 'Apple A17 Pro',
        ]);

        $product->forceDelete();

        $this->assertDatabaseMissing('product_performances', ['id' => $performance->id]);
    }

    public function test_performance_accessor_returns_available_relation_values_only(): void
    {
        $product = $this->createProduct();
        $product->performance()->create([
            'chipset' => 'Apple A17 Pro',
            'battery_mah' => 4441,
        ]);

        $keys = collect($product->fresh()->performance_specifications)->pluck('key')->all();

        $this->assertContains('chipset', $keys);
        $this->assertContains('battery_mah', $keys);
        $this->assertNotContains('antutu_score', $keys);
    }

    public function test_compare_payload_reads_data_from_performance_relation(): void
    {
        $product = $this->createProduct();
        $product->performance()->create([
            'chipset' => 'Dimensity 9200+',
            'antutu_score' => 2000000,
            'geekbench_single' => 2500,
            'battery_mah' => 5100,
        ]);

        $payload = app(CompareService::class)->buildPayload($product->load([
            'images',
            'variants.inventory',
            'inventory',
            'reviews',
            'performance',
        ]));
        $specs = collect($payload['performance_specs']);

        $this->assertSame('Dimensity 9200+', $specs->firstWhere('key', 'chipset')['value']);
        $this->assertSame(2000000.0, (float) $specs->firstWhere('key', 'antutu_score')['value']);
        $this->assertNull($specs->firstWhere('key', 'os')['value']);
    }

    public function test_compare_page_renders_performance_data_and_missing_value_state(): void
    {
        $withSpecs = $this->createProduct(['slug' => 'with-performance']);
        $withSpecs->performance()->create([
            'chipset' => 'Snapdragon 8 Elite',
            'antutu_score' => 2800000,
        ]);
        $withoutSpecs = $this->createProduct(['slug' => 'without-performance']);

        $this->withSession(['compare_products' => [$withSpecs->id, $withoutSpecs->id]])
            ->get(route('compare.index'))
            ->assertOk()
            ->assertSee('Chip / SoC')
            ->assertSee('Snapdragon 8 Elite')
            ->assertSee('Antutu Benchmark')
            ->assertSee('Chưa có dữ liệu');
    }

    public function test_product_requests_keep_performance_validation_rules(): void
    {
        $storeRules = (new \App\Http\Requests\Admin\StoreProductRequest())->rules();
        $updateRequest = new \App\Http\Requests\Admin\UpdateProductRequest();
        $updateRequest->setRouteResolver(fn () => new class {
            public function parameter(string $key): Product
            {
                return new Product(['id' => 1]);
            }
        });
        $updateRules = $updateRequest->rules();

        foreach (['antutu_score', 'geekbench_single', 'geekbench_multi', 'battery_mah', 'charging_speed_w'] as $field) {
            $this->assertArrayHasKey($field, $storeRules);
            $this->assertArrayHasKey($field, $updateRules);
        }
    }
}
