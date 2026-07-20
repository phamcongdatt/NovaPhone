<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class ProductRankingService
{
    /**
     * Lấy danh sách sản phẩm bán chạy.
     *
     * Ưu tiên sold_count > 0 (đã đồng bộ từ đơn hàng thật).
     * Fallback: featured + newest để UI không trống khi chưa có doanh số.
     */
    public function bestSellers(int $limit = 4, bool $onlyActive = true): Collection
    {
        $products = $this->baseQuery($onlyActive)
            ->where('sold_count', '>', 0)
            ->orderByDesc('sold_count')
            ->latest()
            ->limit($limit)
            ->get();

        if ($products->isNotEmpty()) {
            return $products;
        }

        return $this->baseQuery($onlyActive)
            ->orderByDesc('is_featured')
            ->latest()
            ->limit($limit)
            ->get();
    }

    private function baseQuery(bool $onlyActive): Builder
    {
        return Product::query()
            ->withAvg(
                ['reviews as rating_average' => fn ($query) => $query->where('is_visible', true)],
                'rating'
            )
            ->when($onlyActive, fn (Builder $query) => $query->where('is_active', true));
    }
}
