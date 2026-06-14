<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreProductRequest;
use App\Http\Requests\Admin\UpdateProductRequest;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    // ─── Danh sách sản phẩm ────────────────────────────────────

    public function index(Request $request)
    {
        $query = Product::with(['category', 'brand'])
            ->withCount('variants')
            ->withSum('inventories', 'quantity');

        // Tìm kiếm theo tên hoặc SKU
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        // Lọc theo danh mục
        if ($categoryId = $request->input('category_id')) {
            $query->where('category_id', $categoryId);
        }

        // Lọc theo thương hiệu
        if ($brandId = $request->input('brand_id')) {
            $query->where('brand_id', $brandId);
        }

        // Lọc theo trạng thái
        if ($request->filled('status')) {
            $query->where('is_active', $request->input('status') === 'active');
        }

        $products = $query->latest()->paginate(15)->withQueryString();

        return view('admin.products.index', [
            'products'   => $products,
            'categories' => Category::orderBy('name')->get(),
            'brands'     => Brand::orderBy('name')->get(),
            'filters'    => $request->only(['search', 'category_id', 'brand_id', 'status']),
        ]);
    }

    // ─── Form thêm sản phẩm ─────────────────────────────────────

    public function create()
    {
        return view('admin.products.create', [
            'categories' => Category::orderBy('name')->get(),
            'brands'     => Brand::orderBy('name')->get(),
        ]);
    }

    // ─── Lưu sản phẩm mới ──────────────────────────────────────

    public function store(StoreProductRequest $request)
    {
        $data = $request->validated();

        DB::beginTransaction();

        try {
            // Tạo slug nếu chưa có
            $data['slug'] = $this->generateUniqueSlug($data['slug'] ?? $data['name']);

            // Upload ảnh đại diện
            if ($request->hasFile('thumbnail')) {
                $data['thumbnail'] = $request->file('thumbnail')->store('products/thumbnails', 'public');
            }

            $data['is_active']   = $request->boolean('is_active');
            $data['is_featured'] = $request->boolean('is_featured');

            $product = Product::create($data);

            // Tạo biến thể + tồn kho
            $this->syncVariants($product, $request->input('variants', []));

            // Nếu không có biến thể nào, tạo 1 dòng tồn kho gốc cho sản phẩm
            if (empty($request->input('variants'))) {
                Inventory::create([
                    'product_id'          => $product->id,
                    'variant_id'          => null,
                    'quantity'            => $request->input('base_quantity', 0),
                    'low_stock_threshold' => $request->input('low_stock_threshold', 5),
                ]);
            }

            // Upload ảnh thư viện
            $this->storeGalleryImages($product, $request->file('images', []));

            DB::commit();

            return redirect()
                ->route('admin.products.index')
                ->with('success', 'Thêm sản phẩm thành công!');
        } catch (\Throwable $e) {
            DB::rollBack();

            return back()
                ->withInput()
                ->withErrors(['error' => 'Có lỗi xảy ra: ' . $e->getMessage()]);
        }
    }

    // ─── Form chỉnh sửa ─────────────────────────────────────────

    public function edit(Product $product)
    {
        $product->load(['variants.inventory', 'images', 'inventory', 'inventories']);

        return view('admin.products.edit', [
            'product'    => $product,
            'categories' => Category::orderBy('name')->get(),
            'brands'     => Brand::orderBy('name')->get(),
        ]);
    }

    // ─── Cập nhật sản phẩm ──────────────────────────────────────

    public function update(UpdateProductRequest $request, Product $product)
    {
        $data = $request->validated();

        DB::beginTransaction();

        try {
            // Cập nhật slug nếu đổi tên/slug
            if (($data['slug'] ?? null) !== $product->slug) {
                $data['slug'] = $this->generateUniqueSlug($data['slug'] ?? $data['name'], $product->id);
            }

            // Thay ảnh đại diện mới
            if ($request->hasFile('thumbnail')) {
                if ($product->thumbnail) {
                    Storage::disk('public')->delete($product->thumbnail);
                }
                $data['thumbnail'] = $request->file('thumbnail')->store('products/thumbnails', 'public');
            }

            $data['is_active']   = $request->boolean('is_active');
            $data['is_featured'] = $request->boolean('is_featured');

            $product->update($data);

            // Đồng bộ biến thể (thêm/sửa/xoá)
            $this->syncVariants($product, $request->input('variants', []));

            // Xoá biến thể đã đánh dấu xoá
            if ($deleted = $request->input('deleted_variants')) {
                ProductVariant::whereIn('id', $deleted)->where('product_id', $product->id)->delete();
                Inventory::whereIn('variant_id', $deleted)->delete();
            }

            // Cập nhật tồn kho gốc (sản phẩm không biến thể)
            if (empty($request->input('variants')) && $product->variants()->doesntExist()) {
                Inventory::updateOrCreate(
                    ['product_id' => $product->id, 'variant_id' => null],
                    [
                        'quantity'            => $request->input('base_quantity', 0),
                        'low_stock_threshold' => $request->input('low_stock_threshold', 5),
                    ]
                );
            }

            // Xoá ảnh đã đánh dấu xoá
            if ($deletedImages = $request->input('deleted_images')) {
                $images = ProductImage::whereIn('id', $deletedImages)->where('product_id', $product->id)->get();
                foreach ($images as $img) {
                    Storage::disk('public')->delete($img->image_url);
                    $img->delete();
                }
            }

            // Thêm ảnh mới
            $this->storeGalleryImages($product, $request->file('images', []));

            DB::commit();

            return redirect()
                ->route('admin.products.index')
                ->with('success', 'Cập nhật sản phẩm thành công!');
        } catch (\Throwable $e) {
            DB::rollBack();

            return back()
                ->withInput()
                ->withErrors(['error' => 'Có lỗi xảy ra: ' . $e->getMessage()]);
        }
    }

    // ─── Xoá sản phẩm (soft delete) ─────────────────────────────

    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Đã chuyển sản phẩm vào thùng rác.');
    }

    // ─── Bật/tắt trạng thái nhanh ───────────────────────────────

    public function toggleStatus(Product $product)
    {
        $product->update(['is_active' => ! $product->is_active]);

        return back()->with('success', 'Đã cập nhật trạng thái sản phẩm.');
    }

    // ════════════════════════════════════════════════════════════
    //  HELPERS
    // ════════════════════════════════════════════════════════════

    /**
     * Tạo slug duy nhất, tự thêm số nếu trùng.
     */
    private function generateUniqueSlug(string $value, ?int $ignoreId = null): string
    {
        $slug     = Str::slug($value);
        $original = $slug;
        $i        = 1;

        while (
            Product::where('slug', $slug)
            ->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))
            ->exists()
        ) {
            $slug = "{$original}-{$i}";
            $i++;
        }

        return $slug;
    }

    /**
     * Tạo hoặc cập nhật biến thể + tồn kho tương ứng.
     */
    private function syncVariants(Product $product, array $variants): void
    {
        foreach ($variants as $variantData) {
            $variantId = $variantData['id'] ?? null;
            $quantity  = (int) ($variantData['quantity'] ?? 0);

            $payload = [
                'product_id'       => $product->id,
                'name'             => $variantData['name'],
                'storage'          => $variantData['storage'] ?? null,
                'color'            => $variantData['color'] ?? null,
                'color_code'       => $variantData['color_code'] ?? null,
                'additional_price' => $variantData['additional_price'] ?? 0,
                'sku'              => $variantData['sku'] ?? null,
                'is_active'        => true,
            ];

            if ($variantId) {
                $variant = ProductVariant::where('id', $variantId)
                    ->where('product_id', $product->id)
                    ->first();

                if ($variant) {
                    $variant->update($payload);
                } else {
                    $variant = ProductVariant::create($payload);
                }
            } else {
                $variant = ProductVariant::create($payload);
            }

            // Đồng bộ tồn kho cho biến thể
            Inventory::updateOrCreate(
                ['product_id' => $product->id, 'variant_id' => $variant->id],
                [
                    'quantity'            => $quantity,
                    'low_stock_threshold' => $variantData['low_stock_threshold'] ?? 5,
                ]
            );
        }
    }

    /**
     * Lưu ảnh thư viện sản phẩm.
     */
    private function storeGalleryImages(Product $product, array $images): void
    {
        if (empty($images)) {
            return;
        }

        $hasPrimary = $product->images()->where('is_primary', true)->exists();
        $maxOrder   = (int) $product->images()->max('sort_order');

        foreach ($images as $i => $image) {
            $path = $image->store('products/gallery', 'public');

            ProductImage::create([
                'product_id' => $product->id,
                'image_url'  => $path,
                'is_primary' => ! $hasPrimary && $i === 0,
                'sort_order' => $maxOrder + $i + 1,
            ]);
        }
    }
}
