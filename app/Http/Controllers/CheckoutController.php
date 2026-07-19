<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PaymentTransaction;
use App\Services\CartService;
use App\Services\VnpayService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    protected CartService $cartService;
    protected VnpayService $vnpayService;

    public function __construct(CartService $cartService, VnpayService $vnpayService)
    {
        $this->cartService = $cartService;
        $this->vnpayService = $vnpayService;
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

        $disableCod = $total > 20000000;
        $defaultPaymentMethod = $disableCod ? 'vnpay' : 'cod';

        return view('checkout.index', compact('items', 'total', 'defaultAddress', 'disableCod', 'defaultPaymentMethod'));
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
            'payment_method' => 'required|in:cod,vnpay',
            'note' => 'nullable|string',
        ]);

        $items = $this->cartService->getItems();
        if ($items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Giỏ hàng đang trống.');
        }

        $total = $this->cartService->getTotal();

        // Kiểm tra giới hạn COD ở backend
        if ($total > 20000000 && $request->payment_method === 'cod') {
            return redirect()->back()->with('error', 'Đơn hàng có tổng giá trị vượt quá 20.000.000đ không hỗ trợ phương thức thanh toán COD. Vui lòng chọn phương thức thanh toán trực tuyến.')->withInput();
        }

        try {
            $order = DB::transaction(function () use ($request, $items, $total) {
                // 1. Kiểm tra tồn kho và trạng thái của tất cả sản phẩm
                foreach ($items as $item) {
                    // Kiểm tra sản phẩm còn tồn tại
                    if (!$item->product) {
                        throw new Exception("Sản phẩm không còn tồn tại hoặc đã bị xóa khỏi hệ thống.");
                    }

                    // Kiểm tra sản phẩm chưa bị ẩn
                    if (!$item->product->is_active) {
                        throw new Exception("Sản phẩm {$item->product->name} hiện không khả dụng (đã bị ẩn hoặc ngừng bán).");
                    }

                    // Nếu có biến thể thì kiểm tra biến thể
                    if ($item->variant_id) {
                        if (!$item->variant) {
                            throw new Exception("Phiên bản của sản phẩm {$item->product->name} không còn tồn tại hoặc đã bị xóa.");
                        }
                        if (!$item->variant->is_active) {
                            throw new Exception("Phiên bản {$item->variant->name} của sản phẩm {$item->product->name} hiện không khả dụng.");
                        }
                    }

                    // Kiểm tra tồn kho khả dụng
                    $available = $this->cartService->getAvailableStock($item->product, $item->variant);
                    if ($available <= 0) {
                        $name = $item->product->name . ($item->variant ? " (" . $item->variant->name . ")" : "");
                        throw new Exception("Sản phẩm {$name} đã hết hàng.");
                    }

                    if ($available < $item->quantity) {
                        $name = $item->product->name . ($item->variant ? " (" . $item->variant->name . ")" : "");
                        throw new Exception("Sản phẩm {$name} chỉ còn {$available} sản phẩm trong kho.");
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

                        // Ghi nhận lịch sử xuất kho
                        \App\Models\InventoryHistory::create([
                            'product_id' => $product->id,
                            'variant_id' => $variant ? $variant->id : null,
                            'type'       => 'export',
                            'quantity'   => $item->quantity,
                            'note'       => 'Xuất kho tự động cho đơn hàng #' . $order->order_code,
                            'user_id'    => Auth::id(),
                        ]);
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

            // VNPay: chuyển thẳng sang cổng thanh toán thật
            return redirect()->route('checkout.vnpay.create', $order);

        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
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

    /**
     * Tạo URL và chuyển hướng người dùng sang cổng VNPay.
     */
    public function vnpayCreate(Request $request, Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        if ($order->payment_status === 'paid') {
            return redirect()->route('checkout.success', $order);
        }

        // Ghi nhận một giao dịch chờ xử lý để đối soát
        PaymentTransaction::create([
            'order_id' => $order->id,
            'gateway'  => 'vnpay',
            'amount'   => $order->total_amount,
            'status'   => 'pending',
        ]);

        $paymentUrl = $this->vnpayService->createPaymentUrl($order, $request->ip());

        return redirect()->away($paymentUrl);
    }

    /**
     * Xử lý dữ liệu VNPay trả về (Return URL).
     */
    public function vnpayReturn(Request $request)
    {
        $order = Order::where('order_code', $request->query('vnp_TxnRef'))->first();

        if (! $order) {
            return redirect()->route('home')->with('error', 'Không tìm thấy đơn hàng tương ứng.');
        }

        // 1. Xác thực chữ ký
        if (! $this->vnpayService->validateReturn($request)) {
            return redirect()->route('checkout.success', $order)
                ->with('error', 'Chữ ký thanh toán không hợp lệ. Vui lòng liên hệ hỗ trợ.');
        }

        $isSuccess     = $this->vnpayService->isSuccessful($request);
        $responseCode  = $request->query('vnp_ResponseCode');
        $amountMatched = (int) round($order->total_amount * 100) === (int) $request->query('vnp_Amount');

        // 2. Cập nhật nhật ký giao dịch
        $transaction = $order->payments()->where('gateway', 'vnpay')->latest()->first();
        if ($transaction) {
            $transaction->update([
                'transaction_code' => $request->query('vnp_TransactionNo'),
                'status'           => ($isSuccess && $amountMatched) ? 'success' : 'failed',
                'response_code'    => $responseCode,
                'response_message' => $isSuccess ? 'Giao dịch thành công' : 'Giao dịch thất bại',
                'payload'          => $request->query(),
                'paid_at'          => ($isSuccess && $amountMatched) ? now() : null,
            ]);
        }

        // 3. Cập nhật đơn hàng (chỉ khi chưa thanh toán để tránh xử lý lặp)
        if ($isSuccess && $amountMatched && $order->payment_status !== 'paid') {
            $order->update([
                'payment_status' => 'paid',
                'status'         => 'confirmed',
            ]);

            return redirect()->route('checkout.success', $order)
                ->with('success', 'Thanh toán VNPay thành công!');
        }

        return redirect()->route('checkout.success', $order)
            ->with('error', 'Thanh toán không thành công (mã lỗi: ' . $responseCode . ').');
    }
}
