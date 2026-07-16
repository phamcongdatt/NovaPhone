<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\FlashSale;
use App\Models\Product;
use App\Services\CartService;
use App\Services\ProductRankingService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends Controller
{
    protected CartService $cartService;
    protected ProductRankingService $productRankingService;

    public function __construct(CartService $cartService, ProductRankingService $productRankingService)
    {
        $this->cartService = $cartService;
        $this->productRankingService = $productRankingService;
    }

    public function index(Request $request): View
    {
        $priceRanges = [
            'duoi-10-trieu' => 'Dưới 10 triệu',
            '10-20-trieu' => '10 - 20 triệu',
            '20-30-trieu' => '20 - 30 triệu',
            'tren-30-trieu' => 'Trên 30 triệu',
        ];
        $featureFilters = [
            'sale' => 'Đang giảm giá',
            'featured' => 'Nổi bật',
            'in-stock' => 'Còn hàng',
            'storage-128' => 'Bộ nhớ 128GB',
            'storage-256' => 'Bộ nhớ 256GB',
            'storage-512' => 'Bộ nhớ 512GB',
        ];
        $sortOptions = [
            'featured' => 'Nổi bật',
            'newest' => 'Mới nhất',
            'price-asc' => 'Giá thấp đến cao',
            'price-desc' => 'Giá cao đến thấp',
            'best-selling' => 'Bán chạy',
        ];

        $selectedPriceRange = $request->string('price')->toString();
        if (! array_key_exists($selectedPriceRange, $priceRanges)) {
            $selectedPriceRange = null;
        }

        $selectedFeatures = $this->selectedFeatures($request, $featureFilters);

        $selectedSort = $request->string('sort')->toString();
        if (! array_key_exists($selectedSort, $sortOptions)) {
            $selectedSort = 'featured';
        }

        $selectedSearchQuery = trim($request->string('q')->toString());
        if ($selectedSearchQuery === '') {
            $selectedSearchQuery = null;
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

        $filterCategories = \App\Models\Category::query()
            ->withCount(['products' => fn ($query) => $query->where('is_active', true)])
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $selectedCategorySlug = $request->string('category')->toString();
        if (! $filterCategories->contains('slug', $selectedCategorySlug)) {
            $selectedCategorySlug = null;
        }

        $catalogProducts = Product::query()
            ->with('brand')
            ->withAvg(['reviews as rating_average' => fn ($query) => $query->where('is_visible', true)], 'rating')
            ->where('is_active', true)
            ->when($selectedSearchQuery, fn ($query) => $this->applySearchQuery($query, $selectedSearchQuery))
            ->when($selectedPriceRange, fn ($query) => $this->applyPriceRange($query, $selectedPriceRange))
            ->when($selectedFeatures, fn ($query) => $this->applyFeatureFilters($query, $selectedFeatures))
            ->when($selectedBrandSlug, fn ($query) => $query->whereHas(
                'brand',
                fn ($brandQuery) => $brandQuery->where('slug', $selectedBrandSlug)
            ))
            ->when($selectedCategorySlug, fn ($query) => $query->whereHas(
                'category',
                fn ($catQuery) => $catQuery->where('slug', $selectedCategorySlug)
            ))
            ->tap(fn ($query) => $this->applySort($query, $selectedSort))
            ->paginate(12)
            ->withQueryString()
            ->fragment('san-pham');

        $activeFlashSale = FlashSale::with(['items.product' => function ($q) {
            $q->where('is_active', true);
        }])
        ->where('is_active', true)
        ->where('start_time', '<=', now())
        ->where('end_time', '>=', now())
        ->latest()
        ->first();

        $bestSellerProducts = $this->productRankingService->bestSellers(4);

        $latestPosts = \App\Models\Post::where('is_published', true)
            ->latest('published_at')
            ->take(3)
            ->get();

        $banners = \App\Models\Banner::where('is_active', true)
            ->orderBy('sort_order')
            ->latest()
            ->get();

        return view('home', [
            'activeFlashSale' => $activeFlashSale,
            'banners' => $banners,
            'bestSellerProducts' => $bestSellerProducts,
            'catalogProducts' => $catalogProducts,
            'latestPosts' => $latestPosts,
            'cartCount' => $this->cartService->getCount(),
            'featureFilters' => $featureFilters,
            'filterBrands' => $filterBrands,
            'filterCategories' => $filterCategories,
            'priceRanges' => $priceRanges,
            'selectedBrandSlug' => $selectedBrandSlug,
            'selectedCategorySlug' => $selectedCategorySlug,
            'selectedFeatures' => $selectedFeatures,
            'selectedPriceRange' => $selectedPriceRange,
            'selectedSearchQuery' => $selectedSearchQuery,
            'selectedSort' => $selectedSort,
            'sortOptions' => $sortOptions,
        ]);
    }

    private function applySearchQuery($query, string $searchQuery)
    {
        $keyword = "%{$searchQuery}%";

        return $query->where(function ($searchBuilder) use ($keyword) {
            $searchBuilder
                ->where('name', 'like', $keyword)
                ->orWhere('sku', 'like', $keyword)
                ->orWhere('description', 'like', $keyword);
        });
    }

    private function selectedFeatures(Request $request, array $featureFilters): array
    {
        $selectedFeatures = $request->input('features', []);

        if (! is_array($selectedFeatures)) {
            $selectedFeatures = [$selectedFeatures];
        }

        $legacyFeature = $request->string('feature')->toString();
        if ($legacyFeature !== '') {
            $selectedFeatures[] = $legacyFeature;
        }

        return collect($selectedFeatures)
            ->filter(fn ($feature) => is_string($feature) && array_key_exists($feature, $featureFilters))
            ->unique()
            ->values()
            ->all();
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

    private function applyFeatureFilters($query, array $features)
    {
        foreach ($features as $feature) {
            $this->applyFeatureFilter($query, $feature);
        }

        return $query;
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
