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

        $selectedPriceRange = $request->string('price')->toString();
        if (! array_key_exists($selectedPriceRange, $priceRanges)) {
            $selectedPriceRange = null;
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
            ->when($selectedBrandSlug, fn ($query) => $query->whereHas(
                'brand',
                fn ($brandQuery) => $brandQuery->where('slug', $selectedBrandSlug)
            ))
            ->orderByDesc('is_featured')
            ->latest()
            ->take(12)
            ->get();

        return view('home', [
            'catalogProducts' => $catalogProducts,
            'filterBrands' => $filterBrands,
            'priceRanges' => $priceRanges,
            'selectedBrandSlug' => $selectedBrandSlug,
            'selectedPriceRange' => $selectedPriceRange,
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
}
