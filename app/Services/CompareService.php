<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Session;

class CompareService
{
    /**
     * Khóa session dùng để lưu danh sách sản phẩm đang so sánh.
     */
    protected string $key = 'compare_products';

    /**
     * Số lượng sản phẩm tối đa được phép so sánh cùng lúc.
     */
    protected int $maxItems = 4;

    /**
     * Lấy danh sách ID sản phẩm đang so sánh (giữ nguyên thứ tự).
     */
    public function getProductIds(): array
    {
        return Session::get($this->key, []);
    }

    /**
     * Đếm số sản phẩm đang so sánh.
     */
    public function getCount(): int
    {
        return count($this->getProductIds());
    }

    /**
     * Thêm một sản phẩm vào danh sách so sánh.
     *
     * @return array{success: bool, action?: string, message: string, count?: int, product_ids?: array}
     */
    public function add(int $productId): array
    {
        $ids = $this->getProductIds();

        // Không cho trùng
        if (in_array($productId, $ids, true)) {
            return [
                'success' => false,
                'message' => 'Sản phẩm này đã có trong danh sách so sánh.',
            ];
        }

        // Kiểm tra giới hạn
        if (count($ids) >= $this->maxItems) {
            return [
                'success' => false,
                'message' => 'Bạn chỉ có thể so sánh tối đa '.$this->maxItems.' sản phẩm.',
            ];
        }

        // Chỉ cho phép sản phẩm đang kinh doanh
        $product = Product::where('id', $productId)->where('is_active', true)->first();
        if (! $product) {
            return [
                'success' => false,
                'message' => 'Sản phẩm không tồn tại hoặc đã ngừng kinh doanh.',
            ];
        }

        $ids[] = $productId;
        Session::put($this->key, $ids);

        return [
            'success' => true,
            'action' => 'added',
            'message' => 'Đã thêm sản phẩm vào danh sách so sánh.',
            'count' => count($ids),
            'product_ids' => $ids,
        ];
    }

    /**
     * Xóa một sản phẩm khỏi danh sách so sánh.
     *
     * @return array{success: bool, action?: string, message: string, count?: int, product_ids?: array}
     */
    public function remove(int $productId): array
    {
        $ids = $this->getProductIds();

        if (! in_array($productId, $ids, true)) {
            return [
                'success' => false,
                'message' => 'Sản phẩm không có trong danh sách so sánh.',
            ];
        }

        $ids = array_values(array_filter(
            $ids,
            fn (int $id) => $id !== $productId
        ));

        Session::put($this->key, $ids);

        return [
            'success' => true,
            'action' => 'removed',
            'message' => 'Đã xóa sản phẩm khỏi danh sách so sánh.',
            'count' => count($ids),
            'product_ids' => $ids,
        ];
    }

    /**
     * Xóa toàn bộ danh sách so sánh.
     *
     * @return array{success: bool, action?: string, message: string, count: int, product_ids: array}
     */
    public function clear(): array
    {
        Session::forget($this->key);

        return [
            'success' => true,
            'action' => 'cleared',
            'message' => 'Đã xóa toàn bộ danh sách so sánh.',
            'count' => 0,
            'product_ids' => [],
        ];
    }

    /**
     * Lấy các sản phẩm đang so sánh kèm dữ liệu cần thiết cho bảng so sánh.
     * Loại bỏ ID không hợp lệ / inactive / soft-deleted.
     */
    public function getProducts(): Collection
    {
        $ids = $this->getProductIds();

        if (empty($ids)) {
            return collect();
        }

        $products = Product::with([
            'brand:id,name,slug',
            'category:id,name,slug',
            'images' => fn ($q) => $q->orderByDesc('is_primary')->orderBy('sort_order'),
            'variants.inventory',
            'inventory',
            'reviews' => fn ($q) => $q->where('is_visible', true),
            'performance',
        ])
            ->whereIn('id', $ids)
            ->where('is_active', true)
            ->get()
            ->keyBy('id');

        // Lọc ra những ID còn tồn tại và active, nhưng vẫn giữ thứ tự người dùng đã thêm.
        $validIds = array_values(array_filter(
            $ids,
            fn (int $id) => $products->has($id)
        ));
        if ($validIds !== $ids) {
            Session::put($this->key, $validIds);
        }

        return collect($validIds)
            ->map(fn (int $id) => $products->get($id));
    }

    /**
     * Xây dựng payload so sánh chuẩn cho một sản phẩm.
     * Dùng chung logic thông số với ProductDetailController để đảm bảo nhất quán.
     */
    public function buildPayload(Product $product): array
    {
        $images = $product->images;
        $primaryImage = $images->firstWhere('is_primary');
        $imageUrl = $primaryImage?->image_url ?? $product->thumbnail;

        $reviews = $product->reviews;
        $averageRating = round((float) $reviews->avg('rating'), 1);

        $storageOptions = $product->variants->pluck('storage')->filter()->unique()->values()->toArray();
        $colorOptions = $product->variants->pluck('color')->filter()->unique()->values()->toArray();

        $totalAvailable = $product->inventory?->available_quantity
            ?? $product->variants->sum(fn ($v) => $v->inventory?->available_quantity ?? 0);

        return [
            'id' => $product->id,
            'name' => $product->name,
            'slug' => $product->slug,
            'sku' => $product->sku,
            'brand' => $product->brand?->name ?? 'Đang cập nhật',
            'category' => $product->category?->name ?? 'Đang cập nhật',
            'price' => (float) $product->price,
            'sale_price' => $product->sale_price ? (float) $product->sale_price : null,
            'effective_price' => $product->effective_price,
            'discount_percent' => $this->discountPercent($product),
            'image' => $imageUrl ?: asset('images/placeholder.svg'),
            'rating_average' => $averageRating ?: null,
            'rating_count' => $reviews->count(),
            'storage_options' => $storageOptions,
            'color_options' => $colorOptions,
            'available_quantity' => $totalAvailable,
            'is_active' => (bool) $product->is_active,
            'performance_specs' => $this->performanceSpecifications($product),
        ];
    }

    /**
     * Chuẩn hóa thông số hiệu năng cho bảng so sánh.
     * Giá trị null được giữ nguyên để giao diện hiển thị "Chưa có dữ liệu".
     *
     * @return array<int, array{key: string, label: string, value: mixed, unit?: string, higher_is_better?: bool}>
     */
    private function performanceSpecifications(Product $product): array
    {
        $specifications = [
            ['key' => 'chipset', 'label' => 'Chip / SoC'],
            ['key' => 'cpu_cores', 'label' => 'CPU'],
            ['key' => 'gpu', 'label' => 'GPU'],
            ['key' => 'antutu_score', 'label' => 'Antutu Benchmark', 'higher_is_better' => true],
            ['key' => 'geekbench_single', 'label' => 'Geekbench Single-Core', 'higher_is_better' => true],
            ['key' => 'geekbench_multi', 'label' => 'Geekbench Multi-Core', 'higher_is_better' => true],
            ['key' => 'ram', 'label' => 'RAM'],
            ['key' => 'display_size_inch', 'label' => 'Kích thước màn hình', 'unit' => ' inch'],
            ['key' => 'display_type', 'label' => 'Loại màn hình'],
            ['key' => 'refresh_rate', 'label' => 'Tần số quét'],
            ['key' => 'main_camera_mp', 'label' => 'Camera chính'],
            ['key' => 'ultra_wide_camera_mp', 'label' => 'Camera siêu rộng'],
            ['key' => 'front_camera_mp', 'label' => 'Camera trước'],
            ['key' => 'video_recording', 'label' => 'Quay video'],
            ['key' => 'battery_mah', 'label' => 'Dung lượng pin', 'unit' => ' mAh', 'higher_is_better' => true],
            ['key' => 'charging_speed_w', 'label' => 'Sạc nhanh', 'unit' => 'W', 'higher_is_better' => true],
            ['key' => 'os', 'label' => 'Hệ điều hành'],
            ['key' => 'network_support', 'label' => 'Kết nối'],
        ];

        $performance = $product->performance;

        return array_map(function (array $specification) use ($performance): array {
            $key = $specification['key'];

            return [...$specification, 'value' => $performance?->{$key}];
        }, $specifications);
    }

    /**
     * Tính phần trăm giảm giá của sản phẩm.
     */
    private function discountPercent(Product $product): ?int
    {
        if (! $product->sale_price || $product->sale_price >= $product->price) {
            return null;
        }

        return (int) round((($product->price - $product->sale_price) / $product->price) * 100);
    }
}