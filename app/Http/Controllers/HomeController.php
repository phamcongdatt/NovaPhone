<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(Request $request): View
    {
        $priceRanges = [
            'duoi-10-trieu' => 'Duoi 10 trieu',
            '10-20-trieu' => '10 - 20 trieu',
            '20-30-trieu' => '20 - 30 trieu',
            'tren-30-trieu' => 'Tren 30 trieu',
        ];
        $featureFilters = [
            'sale' => 'Dang giam gia',
            'featured' => 'Noi bat',
            'in-stock' => 'Con hang',
            'storage-128' => 'Bo nho 128GB',
            'storage-256' => 'Bo nho 256GB',
            'storage-512' => 'Bo nho 512GB',
        ];
        $sortOptions = [
            'featured' => 'Noi bat',
            'newest' => 'Moi nhat',
            'price-asc' => 'Gia thap den cao',
            'price-desc' => 'Gia cao den thap',
            'best-selling' => 'Ban chay',
        ];

        $selectedPriceRange = $request->string('price')->toString();
        if (! array_key_exists($selectedPriceRange, $priceRanges)) {
            $selectedPriceRange = null;
        }

        $selectedFeature = $request->string('feature')->toString();
        if (! array_key_exists($selectedFeature, $featureFilters)) {
            $selectedFeature = null;
        }

        $selectedSort = $request->string('sort')->toString();
        if (! array_key_exists($selectedSort, $sortOptions)) {
            $selectedSort = 'featured';
        }

        $filterBrands = Brand::query()
            ->withCount(['products' => fn ($query) => $query->where('is_active', true)])
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $selectedBrandSlug = $request->string('brand')->toString();
        if (! $filterBrands->contains('slug', $selectedBrandSlug)) {
            $selectedBrandSlug = null;
        }

        $catalogProducts = Product::query()
            ->with('brand')
            ->withAvg(['reviews as rating_average' => fn ($query) => $query->where('is_visible', true)], 'rating')
            ->where('is_active', true)
            ->when($selectedPriceRange, fn ($query) => $this->applyPriceRange($query, $selectedPriceRange))
            ->when($selectedFeature, fn ($query) => $this->applyFeatureFilter($query, $selectedFeature))
            ->when($selectedBrandSlug, fn ($query) => $query->whereHas(
                'brand',
                fn ($brandQuery) => $brandQuery->where('slug', $selectedBrandSlug)
            ))
            ->tap(fn ($query) => $this->applySort($query, $selectedSort))
            ->take(12)
            ->get();

        return view('home', [
            'catalogProducts' => $catalogProducts,
            'featureFilters' => $featureFilters,
            'filterBrands' => $filterBrands,
            'priceRanges' => $priceRanges,
            'selectedBrandSlug' => $selectedBrandSlug,
            'selectedFeature' => $selectedFeature,
            'selectedPriceRange' => $selectedPriceRange,
            'selectedSort' => $selectedSort,
            'sortOptions' => $sortOptions,
        ]);
    }

    private function applyPriceRange($query, string $priceRange)
    {
        $priceColumn = 'COALESCE(sale_price, price)';

        return match ($priceRange) {
            'duoi-10-trieu' => $query->whereRaw("$priceColumn < ?", [10000000]),
            '10-20-trieu' => $query->whereRaw("$priceColumn BETWEEN ? AND ?", [10000000, 20000000]),
            '20-30-trieu' => $query->whereRaw("$priceColumn BETWEEN ? AND ?", [20000000, 30000000]),
            'tren-30-trieu' => $query->whereRaw("$priceColumn > ?", [30000000]),
            default => $query,
        };
    }

    private function applyFeatureFilter($query, string $feature)
    {
        return match ($feature) {
            'sale' => $query->whereNotNull('sale_price')->whereColumn('sale_price', '<', 'price'),
            'featured' => $query->where('is_featured', true),
            'in-stock' => $query->where(function ($stockQuery) {
                $stockQuery
                    ->whereHas('inventory', fn ($inventoryQuery) => $inventoryQuery->whereColumn('quantity', '>', 'reserved_quantity'))
                    ->orWhereHas('variants.inventory', fn ($inventoryQuery) => $inventoryQuery->whereColumn('quantity', '>', 'reserved_quantity'));
            }),
            'storage-128' => $query->whereHas('variants', fn ($variantQuery) => $variantQuery->where('is_active', true)->where('storage', '128GB')),
            'storage-256' => $query->whereHas('variants', fn ($variantQuery) => $variantQuery->where('is_active', true)->where('storage', '256GB')),
            'storage-512' => $query->whereHas('variants', fn ($variantQuery) => $variantQuery->where('is_active', true)->where('storage', '512GB')),
            default => $query,
        };
    }

    private function applySort($query, string $sort)
    {
        $priceColumn = 'COALESCE(sale_price, price)';

        return match ($sort) {
            'newest' => $query->latest(),
            'price-asc' => $query->orderByRaw("$priceColumn asc")->latest(),
            'price-desc' => $query->orderByRaw("$priceColumn desc")->latest(),
            'best-selling' => $query->orderByDesc('sold_count')->latest(),
            default => $query->orderByDesc('is_featured')->latest(),
        };
    }
}
