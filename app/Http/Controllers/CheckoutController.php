<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckoutRequest;
use App\Models\Address;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PaymentTransaction;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Services\CartService;
use App\Services\CouponService;
use App\Services\SoldCountService;
use App\Services\TelegramNotificationService;
use App\Services\VnpayService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    protected CartService $cartService;
    protected VnpayService $vnpayService;
    protected TelegramNotificationService $telegramNotificationService;
    protected SoldCountService $soldCountService;
    protected CouponService $couponService;

    public function __construct(
        CartService $cartService,
        VnpayService $vnpayService,
        TelegramNotificationService $telegramNotificationService,
        SoldCountService $soldCountService,
        CouponService $couponService
    ) {
        $this->cartService = $cartService;
        $this->vnpayService = $vnpayService;
        $this->telegramNotificationService = $telegramNotificationService;
        $this->soldCountService = $soldCountService;
        $this->couponService = $couponService;
    }

    /**
     * Xây dựng danh sách item + tổng tiền cho luồng hiện tại.
     * Hỗ trợ hai luồng:
     * 1. Thanh toán từ giỏ hàng (chỉ lấy các item đã được chọn - xem CartService::getSelectedItems)
     * 2. Mua ngay (lấy data từ session 'buy_now_item')
     *
     * @return array{items: \Illuminate\Support\Collection, total: float, isBuyNow: bool}
     */
    private function resolveCheckoutData(): array
    {
        $isBuyNow = session()->has('buy_now_item');

        if ($isBuyNow) {
            $buyNowData = session()->get('buy_now_item');
            $product = Product::findOrFail($buyNowData['product_id']);
            $variant = $buyNowData['variant_id'] ? ProductVariant::findOrFail($buyNowData['variant_id']) : null;

            $items = collect();
            $mockItem = new CartItem([
                'product_id' => $product->id,
                'variant_id' => $variant ? $variant->id : null,
                'quantity' => $buyNowData['quantity'],
                'price' => $buyNowData['price'],
            ]);
            $mockItem->setRelation('product', $product);
            if ($variant) {
                $mockItem->setRelation('variant', $variant);
            }
            $mockItem->setDisplayId('buy_now_0');
            $items->push($mockItem);

            $total = $buyNowData['price'] * $buyNowData['quantity'];

            return compact('items', 'total', 'isBuyNow');
        }

        $items = $this->cartService->getSelectedItems();
        $total = (float) $items->sum(fn ($item) => $item->price * $item->quantity);

        return compact('items', 'total', 'isBuyNow');
    }

    /**
     * Hiển thị giao diện thanh toán.
     */
    public function index()
    {
        $user = Auth::user();

        // Nếu trước đó user đã tạo đơn nhưng rời khỏi cổng thanh toán online
        // (bấm Back/đóng tab) mà chưa hoàn tất, hiển thị lại đơn đó để "Tiếp tục thanh toán"
        // thay vì cho đặt hàng mới đè lên.
        $pendingPaymentOrder = null;
        if ($pendingOrderId = session('pending_payment_order_id')) {
            $pendingPaymentOrder = Order::where('id', $pendingOrderId)
                ->where('user_id', $user->id)
                ->where('status', 'pending')
                ->where('payment_status', 'pending')
                ->where('payment_method', 'vnpay')
                ->first();

            if (! $pendingPaymentOrder) {
                session()->forget('pending_payment_order_id');
            }
        }

        ['items' => $items, 'total' => $total, 'isBuyNow' => $isBuyNow] = $this->resolveCheckoutData();

        if (! $isBuyNow && $items->isEmpty()) {
            if ($pendingPaymentOrder) {
                return redirect()->route('checkout.success', $pendingPaymentOrder);
            }
            return redirect()->route('cart.index')->with('error', 'Vui lòng chọn ít nhất một sản phẩm để thanh toán.');
        }

        // Xử lý mã giảm giá (nếu có)
        $discountAmount = 0;
        $appliedCouponsData = [];
        if (session()->has('applied_coupons')) {
            $codes = session()->get('applied_coupons', []);
            $result = $this->couponService->applyMultiple($codes, $user, $items, $total);
            if ($result['success']) {
                $discountAmount = $result['discount_amount'];
                $appliedCouponsData = $result['coupons'];
            }
        }

        // Lấy địa chỉ mặc định của user nếu có
        $defaultAddress = $user->addresses()->where('is_default', true)->first()
            ?? $user->addresses()->first();

        $codMaxAmount = (float) config('shop.cod_max_amount');
        $disableCod = $total > $codMaxAmount;
        $defaultPaymentMethod = $disableCod ? 'vnpay' : 'cod';

        // Lấy danh sách mã giảm giá hợp lệ cho user hiện tại
        $now = now();
        $availableCoupons = \App\Models\Coupon::with(['eligibleUsers'])
            ->where('is_active', true)
            ->where(function ($query) use ($now) {
                $query->whereNull('starts_at')->orWhere('starts_at', '<=', $now);
            })
            ->where(function ($query) use ($now) {
                $query->whereNull('expires_at')->orWhere('expires_at', '>=', $now);
            })
            ->where(function ($query) {
                $query->whereNull('usage_limit')->orWhereColumn('used_count', '<', 'usage_limit');
            })
            ->get()
            ->filter(function ($coupon) use ($user) {
                if ($coupon->per_user_limit !== null) {
                    $userUsedCount = $user->orders()->where('coupon_id', $coupon->id)->where('status', '!=', 'cancelled')->count();
                    if ($userUsedCount >= $coupon->per_user_limit) return false;
                }
                if ($coupon->eligibleUsers->isNotEmpty()) {
                    if (!$coupon->eligibleUsers->contains('id', $user->id)) return false;
                }
                return true;
            });

        // Bắt buộc trình duyệt phải gọi lại server (không phục hồi từ bfcache) khi user
        // bấm nút Back từ cổng thanh toán VNPay, để banner "Tiếp tục thanh toán" luôn cập nhật.
        return response()
            ->view('checkout.index', compact(
                'items', 'total', 'defaultAddress', 'disableCod', 'defaultPaymentMethod',
                'codMaxAmount', 'discountAmount', 'appliedCouponsData', 'availableCoupons', 'isBuyNow',
                'pendingPaymentOrder'
            ))
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache');
    }

    public function applyCoupon(Request $request)
    {
        $request->validate(['code' => 'required|string']);

        ['items' => $items, 'total' => $total] = $this->resolveCheckoutData();

        $user = Auth::user();
        $codes = session()->get('applied_coupons', []);
        $newCode = strtoupper($request->code);

        if (in_array($newCode, array_map('strtoupper', $codes))) {
            return $this->jsonOrBack($request, false, 'Mã này đã được áp dụng.');
        }

        $testCodes = array_merge($codes, [$newCode]);
        $result = $this->couponService->applyMultiple($testCodes, $user, $items, $total);

        if (!$result['success']) {
            return $this->jsonOrBack($request, false, $result['message']);
        }

        session()->put('applied_coupons', $testCodes);

        return $this->jsonOrBack($request, true, $result['message'], $result);
    }

    public function removeCoupon(Request $request)
    {
        $codeToRemove = strtoupper($request->input('code'));
        $codes = session()->get('applied_coupons', []);

        if ($codeToRemove) {
            $codes = array_filter($codes, function ($c) use ($codeToRemove) {
                return strtoupper($c) !== $codeToRemove;
            });
            session()->put('applied_coupons', array_values($codes));
        } else {
            session()->forget('applied_coupons'); // Xoá tất cả nếu không truyền code
        }

        return $this->jsonOrBack($request, true, 'Đã bỏ mã giảm giá.');
    }

    private function jsonOrBack(Request $request, bool $success, string $message, $data = null)
    {
        if ($request->expectsJson()) {
            $response = ['success' => $success, 'message' => $message];
            if ($data) $response['data'] = $data;
            return response()->json($response);
        }
        return back()->with($success ? 'success' : 'error', $message);
    }

    /**
     * Xử lý đặt hàng.
     * Hỗ trợ hai luồng:
     * 1. Thanh toán từ giỏ hàng (chỉ xóa khỏi giỏ các item đã đặt, giữ lại các item chưa chọn)
     * 2. Mua ngay (không xóa giỏ, chỉ xóa session 'buy_now_item')
     */
    public function store(CheckoutRequest $request)
    {
        ['items' => $items, 'total' => $total, 'isBuyNow' => $isBuyNow] = $this->resolveCheckoutData();

        if (! $isBuyNow && $items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Vui lòng chọn ít nhất một sản phẩm để thanh toán.');
        }

        $codMaxAmount = (float) config('shop.cod_max_amount');

        // Kiểm tra giới hạn COD ở backend
        if ($total > $codMaxAmount && $request->payment_method === 'cod') {
            return redirect()->back()->with('error', 'Đơn hàng có tổng giá trị vượt quá ' . number_format($codMaxAmount, 0, ',', '.') . 'đ không hỗ trợ phương thức thanh toán COD. Vui lòng chọn phương thức thanh toán trực tuyến.')->withInput();
        }

        try {
            $order = DB::transaction(function () use ($request, $items, $total, $isBuyNow) {
                // 1. Kiểm tra tồn kho và Flash Sale của tất cả sản phẩm

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

                    $product = $item->product;
                    $activeSale = $product->activeFlashSaleItem;
                    if ($activeSale) {
                        $flashSalePrice = (float) ($product->price * (1 - $activeSale->discount_percent / 100));
                        $basePrice = $flashSalePrice + ($item->variant ? (float) $item->variant->additional_price : 0);

                        if (abs((float)$item->price - $basePrice) < 0.01) {
                            if ($activeSale->sold + $item->quantity > $activeSale->quantity) {
                                throw new Exception("Sản phẩm {$product->name} đã hết suất bán Flash Sale. Vui lòng cập nhật lại giỏ hàng.");
                            }
                            $remainingQuota = $product->getFlashSaleRemainingQuota();
                            if ($remainingQuota < $item->quantity) {
                                throw new Exception("Bạn chỉ còn {$remainingQuota} lượt mua giá Flash Sale cho {$product->name}. Vui lòng cập nhật lại giỏ hàng.");
                            }
                        }
                    }
                }

                $discountAmount = 0;
                $appliedCouponsList = [];

                // 2. Tính lại mã giảm giá nếu có
                if (session()->has('applied_coupons')) {
                    $codes = session()->get('applied_coupons');
                    $result = $this->couponService->applyMultiple($codes, Auth::user(), $items, $total);
                    if ($result['success']) {
                        $discountAmount = $result['discount_amount'];
                        $appliedCouponsList = $result['coupons'];
                    }
                }

                $finalTotal = max(0, $total - $discountAmount);

                // 3. Tạo bản ghi đơn hàng
                $order = Order::create([
                    'user_id' => Auth::id(),
                    'status' => 'pending',
                    'payment_method' => $request->payment_method,
                    'payment_status' => 'pending',
                    'subtotal' => $total,
                    'discount_amount' => $discountAmount,
                    'coupon_id' => !empty($appliedCouponsList) ? $appliedCouponsList[0]['coupon']->id : null,
                    'coupon_code' => !empty($appliedCouponsList) ? $appliedCouponsList[0]['coupon']->code : null,
                    'shipping_fee' => 0,
                    'total_amount' => $finalTotal,
                    'shipping_full_name' => $request->shipping_full_name,
                    'shipping_phone' => $request->shipping_phone,
                    'shipping_address' => $request->shipping_address,
                    'shipping_ward' => $request->shipping_ward,
                    'shipping_district' => $request->shipping_district,
                    'shipping_province' => $request->shipping_province,
                    'note' => $request->note,
                ]);

                // Lưu danh sách mã giảm giá vào order_coupons
                foreach ($appliedCouponsList as $ac) {
                    \App\Models\OrderCoupon::create([
                        'order_id' => $order->id,
                        'coupon_id' => $ac['coupon']->id,
                        'coupon_code' => $ac['coupon']->code,
                        'discount_amount' => $ac['discount_amount'],
                    ]);
                    $ac['coupon']->increment('used_count');
                }

                // 4. Tạo chi tiết đơn hàng & trừ kho
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

                    // Tăng số lượng đã bán của Flash Sale (nếu mua với giá Flash Sale)
                    $activeSale = $product->activeFlashSaleItem;
                    if ($activeSale) {
                        $flashSalePrice = (float) ($product->price * (1 - $activeSale->discount_percent / 100));
                        $basePrice = $flashSalePrice + ($variant ? (float) $variant->additional_price : 0);
                        if (abs((float)$item->price - $basePrice) < 0.01) {
                            $activeSale->increment('sold', $item->quantity);
                        }
                    }
                }

                // 5. Lưu địa chỉ vào database (nếu chưa tồn tại)
                $this->saveShippingAddress($request);

                // 6. Xóa dữ liệu tạm thời
                if ($isBuyNow) {
                    // Luồng "Mua ngay": Chỉ xóa session, KHÔNG xóa giỏ hàng
                    session()->forget('buy_now_item');
                } else {
                    // Luồng "Giỏ hàng": Chỉ xóa các item đã đặt, giữ lại các item chưa được chọn
                    $this->cartService->removeMany($items->pluck('display_id')->all());
                    session()->forget('checkout_selected_items');
                }
                session()->forget('applied_coupons');

                return $order;
            });

            // Gửi thông báo Telegram cho đơn hàng mới tạo
            $this->telegramNotificationService->notifyNewOrder($order);

            // Điều hướng theo phương thức thanh toán
            if ($order->payment_method === 'cod') {
                return redirect()->route('checkout.success', $order)
                    ->with('success', 'Đặt hàng thành công!');
            }

            // VNPay: ghi nhớ đơn này để nếu user back lại trang /checkout trước khi
            // hoàn tất thanh toán, ta vẫn nhận diện được và mời tiếp tục thanh toán.
            session()->put('pending_payment_order_id', $order->id);

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
     * Cũng được dùng lại cho luồng "Tiếp tục thanh toán" (đơn pending chưa thanh toán) -
     * KHÔNG tạo đơn hàng mới, chỉ tạo thêm một PaymentTransaction pending cho đơn đã có.
     */
    public function vnpayCreate(Request $request, Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        if ($order->payment_status === 'paid') {
            return redirect()->route('checkout.success', $order);
        }

        if ($order->status !== 'pending') {
            return redirect()->route('orders.show', $order)->with('error', 'Đơn hàng này không thể tiếp tục thanh toán.');
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
            $oldStatus = $order->status;

            $order->update([
                'payment_status' => 'paid',
                'status'         => 'confirmed',
            ]);

            // pending → confirmed: cộng sold_count
            $this->soldCountService->syncOnStatusChange($order, $oldStatus, 'confirmed');

            if (session('pending_payment_order_id') == $order->id) {
                session()->forget('pending_payment_order_id');
            }

            return redirect()->route('checkout.success', $order)
                ->with('success', 'Thanh toán VNPay thành công!');
        }

        // Thanh toán thất bại hoặc bị hủy: đơn vẫn giữ nguyên trạng thái "pending" để
        // khách hàng có thể dùng nút "Tiếp tục thanh toán" thử lại, KHÔNG khóa đơn vĩnh viễn.
        return redirect()->route('checkout.success', $order)
            ->with('error', 'Thanh toán không thành công (mã lỗi: ' . $responseCode . ').');
    }

    /**
     * Lưu địa chỉ giao hàng vào database (nếu chưa tồn tại)
     */
    private function saveShippingAddress(CheckoutRequest $request)
    {
        $user = Auth::user();

        // Kiểm tra xem địa chỉ này đã tồn tại chưa
        $existingAddress = $user->addresses()
            ->where('full_name', $request->shipping_full_name)
            ->where('phone', $request->shipping_phone)
            ->where('address', $request->shipping_address)
            ->where('ward', $request->shipping_ward)
            ->where('district', $request->shipping_district)
            ->where('province', $request->shipping_province)
            ->first();

        // Nếu địa chỉ đã tồn tại, không cần lưu lại
        if ($existingAddress) {
            return;
        }

        // Nếu người dùng chưa có địa chỉ nào, set is_default = true
        $isDefault = $user->addresses()->count() === 0;

        // Tạo địa chỉ mới
        Address::create([
            'user_id' => $user->id,
            'full_name' => $request->shipping_full_name,
            'phone' => $request->shipping_phone,
            'address' => $request->shipping_address,
            'ward' => $request->shipping_ward,
            'district' => $request->shipping_district,
            'province' => $request->shipping_province,
            'is_default' => $isDefault,
        ]);
    }
}
