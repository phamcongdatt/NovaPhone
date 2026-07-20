<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\CompareService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CompareController extends Controller
{
    public function __construct(protected CompareService $compareService)
    {
    }

    /**
     * Hiển thị trang so sánh sản phẩm.
     */
    public function index(): View
    {
        $products = $this->compareService->getProducts();
        $payload = $products
            ->mapWithKeys(fn (Product $product) => [$product->id => $this->compareService->buildPayload($product)])
            ->all();

        return view('compare.index', compact('products', 'payload'));
    }

    /**
     * Thêm sản phẩm vào danh sách so sánh.
     */
    public function add(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
        ]);

        $result = $this->compareService->add($validated['product_id']);

        return response()->json($result, $result['success'] ? 200 : 422);
    }

    /**
     * Xóa sản phẩm khỏi danh sách so sánh.
     */
    public function remove(Product $product): JsonResponse
    {
        $result = $this->compareService->remove($product->id);

        return response()->json($result, $result['success'] ? 200 : 422);
    }

    /**
     * Xóa toàn bộ sản phẩm đang so sánh.
     */
    public function clear(): JsonResponse
    {
        return response()->json($this->compareService->clear());
    }
}