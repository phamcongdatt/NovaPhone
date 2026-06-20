<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $query = Product::with(['brand', 'category', 'images' => fn($q) => $q->where('is_primary', true)])
            ->where('is_active', true);

        // Filter by Search Term
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by Brand
        if ($brandId = $request->input('brand')) {
            $query->where('brand_id', $brandId);
        }

        // Filter by Price Range
        if ($price = $request->input('price')) {
            switch ($price) {
                case 'under-5m':
                    $query->where('price', '<', 5000000);
                    break;
                case '5m-10m':
                    $query->whereBetween('price', [5000000, 10000000]);
                    break;
                case '10m-20m':
                    $query->whereBetween('price', [10000000, 20000000]);
                    break;
                case 'above-20m':
                    $query->where('price', '>', 20000000);
                    break;
            }
        }

        // Sort by
        $sort = $request->input('sort', 'latest');
        match ($sort) {
            'price_asc' => $query->orderBy('price', 'asc'),
            'price_desc' => $query->orderBy('price', 'desc'),
            default => $query->latest(),
        };

        $products = $query->paginate(12)->withQueryString();

        return view('products.index', [
            'products' => $products,
            'brands' => Brand::orderBy('name')->get(),
            'categories' => Category::orderBy('name')->get(),
            'filters' => $request->only(['search', 'brand', 'price', 'sort']),
        ]);
    }

    public function quickSearch(Request $request)
    {
        $term = $request->input('q', '');
        if (empty($term)) {
            return response()->json([]);
        }

        $products = Product::with(['images' => fn($q) => $q->where('is_primary', true)])
            ->where('is_active', true)
            ->where(function ($query) use ($term) {
                $query->where('name', 'LIKE', "%{$term}%")
                      ->orWhere('description', 'LIKE', "%{$term}%");
            })
            ->selectRaw('*, (CASE WHEN name LIKE ? THEN 5 ELSE 0 END + CASE WHEN description LIKE ? THEN 1 ELSE 0 END) as relevance', ["%{$term}%", "%{$term}%"])
            ->orderBy('relevance', 'desc')
            ->limit(5)
            ->get();

        $mapped = $products->map(function ($product) {
            $thumb = $product->images->first()?->image_url ?? $product->thumbnail;
            $thumbUrl = str_starts_with($thumb, 'http') ? $thumb : asset('storage/' . $thumb);
            return [
                'name' => $product->name,
                'url' => route('products.show', $product->slug),
                'price' => number_format((float) $product->effective_price, 0, ',', '.') . 'đ',
                'old_price' => $product->sale_price && $product->sale_price < $product->price ? number_format((float) $product->price, 0, ',', '.') . 'đ' : null,
                'thumbnail' => $thumbUrl,
                'relevance' => $product->relevance
            ];
        });

        return response()->json($mapped);
    }
}
