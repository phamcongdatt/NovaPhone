<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Services\CartService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    protected CartService $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    /**
     * Hiển thị giao diện thanh toán.
     */
    public function index()
    {
        $items = $this->cartService->getItems();
        if ($items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Giỏ hàng của bạn đang trống.');
        }

        $total = $this->cartService->getTotal();
        $user = Auth::user();

        // Lấy địa chỉ mặc định của user nếu có
        $defaultAddress = $user->addresses()->where('is_default', true)->first()
            ?? $user->addresses()->first();

        return view('checkout.index', compact('items', 'total', 'defaultAddress'));
    }

    /**
     * Xử lý đặt hàng.
     */
    public function store(Request $request)
    {
        $request->validate([
            'shipping_full_name' => 'required|string|max:255',
            'shipping_phone' => 'required|string|max:15',
            'shipping_province' => 'required|string|max:255',
            'shipping_district' => 'required|string|max:255',
            'shipping_ward' => 'required|string|max:255',
            'shipping_address' => 'required|string|max:255',
            'payment_method' => 'required|in:cod,momo,vnpay',
            'note' => 'nullable|string',
        ]);

        $items = $this->cartService->getItems();
        if ($items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Giỏ hàng đang trống.');
        }

        $total = $this->cartService->getTotal();

        try {
            $order = DB::transaction(function () use ($request, $items, $total) {
                // 1. Kiểm tra tồn kho của tất cả sản phẩm
                foreach ($items as $item) {
                    $available = $this->cartService->getAvailableStock($item->product, $item->variant);
                    if ($available < $item->quantity) {
                        throw new Exception("Sản phẩm {$item->product->name} (" . ($item->variant ? $item->variant->name : 'Mặc định') . ") chỉ còn lại {$available} trong kho.");
                    }
                }

                // 2. Tạo bản ghi đơn hàng
                $order = Order::create([
                    'user_id' => Auth::id(),
                    'status' => 'pending',
                    'payment_method' => $request->payment_method,
                    'payment_status' => 'pending',
                    'subtotal' => $total,
                    'discount_amount' => 0,
                    'shipping_fee' => 0,
                    'total_amount' => $total,
                    'shipping_full_name' => $request->shipping_full_name,
                    'shipping_phone' => $request->shipping_phone,
                    'shipping_address' => $request->shipping_address,
                    'shipping_ward' => $request->shipping_ward,
                    'shipping_district' => $request->shipping_district,
                    'shipping_province' => $request->shipping_province,
                    'note' => $request->note,
                ]);

                // 3. Tạo chi tiết đơn hàng & trừ kho
                foreach ($items as $item) {
                    $product = $item->product;
                    $variant = $item->variant;

                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                        'variant_id' => $variant ? $variant->id : null,
                        'product_name' => $product->name,
                        'variant_name' => $variant ? $variant->name : null,
                        'product_thumbnail' => $product->thumbnail,
                        'price' => $item->price,
                        'quantity' => $item->quantity,
                        'subtotal' => $item->price * $item->quantity,
                    ]);

                    // Trừ tồn kho tương ứng
                    $inventory = $variant ? $variant->inventory : $product->inventory;
                    if ($inventory) {
                        $inventory->decrement('quantity', $item->quantity);
                    }
                }

                // 4. Xóa giỏ hàng
                $this->cartService->clear();

                return $order;
            });

            // 5. Điều hướng theo phương thức thanh toán
            if ($order->payment_method === 'cod') {
                return redirect()->route('checkout.success', $order)
                    ->with('success', 'Đặt hàng thành công!');
            }

            // Đối với Momo/VNPay chuyển sang trang giả lập thanh toán
            return redirect()->route('checkout.payment-gateway', $order);

        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    /**
     * Màn hình giả lập thanh toán Momo/VNPay.
     */
    public function paymentGateway(Order $order)
    {
        // Bảo vệ route: chỉ user sở hữu đơn hàng mới xem được
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        if ($order->payment_status === 'paid') {
            return redirect()->route('checkout.success', $order);
        }

        return view('checkout.payment_gateway', compact('order'));
    }

    /**
     * Xác nhận thanh toán thành công (Simulated Callback).
     */
    public function processPayment(Request $request, Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        // Cập nhật trạng thái đã thanh toán
        $order->update([
            'payment_status' => 'paid',
            'status' => 'confirmed' // Đã xác nhận vì đã trả tiền
        ]);

        return redirect()->route('checkout.success', $order)
            ->with('success', 'Thanh toán trực tuyến thành công!');
    }

    /**
     * Trang hoàn tất đặt hàng thành công.
     */
    public function success(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        return view('checkout.success', compact('order'));
    }
}
