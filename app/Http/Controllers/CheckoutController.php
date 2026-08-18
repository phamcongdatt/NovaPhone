<?php

namespace App\Http\Controllers;

use App\Events\OrderCreated;
use App\Http\Requests\CheckoutRequest;
use App\Models\Address;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PaymentTransaction;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\FlashSaleItem;
use App\Models\User;
use App\Services\AdministrativeAddressResolver;
use App\Services\CartService;
use App\Services\CouponService;
use App\Services\GuestOrderAccessService;
use App\Services\OrderCancellationService;
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
    protected AdministrativeAddressResolver $administrativeAddressResolver;
    protected VnpayService $vnpayService;
    protected TelegramNotificationService $telegramNotificationService;
    protected SoldCountService $soldCountService;
    protected CouponService $couponService;
    protected OrderCancellationService $cancellationService;
    protected GuestOrderAccessService $guestOrderAccess;

    public function __construct(
        CartService $cartService,
        AdministrativeAddressResolver $administrativeAddressResolver,
        VnpayService $vnpayService,
        TelegramNotificationService $telegramNotificationService,
        SoldCountService $soldCountService,
        CouponService $couponService,
        OrderCancellationService $cancellationService,
        GuestOrderAccessService $guestOrderAccess
    ) {
        $this->cartService = $cartService;
        $this->administrativeAddressResolver = $administrativeAddressResolver;
        $this->vnpayService = $vnpayService;
        $this->telegramNotificationService = $telegramNotificationService;
        $this->soldCountService = $soldCountService;
        $this->couponService = $couponService;
        $this->cancellationService = $cancellationService;
        $this->guestOrderAccess = $guestOrderAccess;
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
        // Hai luồng checkout không được dùng đồng thời. Nếu có selection từ giỏ,
        // ưu tiên selection để không bị session "Mua ngay" cũ làm mất các sản phẩm.
        $isBuyNow = session()->has('buy_now_item') && ! session()->has('checkout_selected_items');

        if ($isBuyNow) {
            $buyNowData = session()->get('buy_now_item');
            $product = Product::withTrashed()->findOrFail($buyNowData['product_id']);
            $variant = $this->cartService->getSelectedVariant(
                $product,
                $buyNowData['variant_id'] ?? null
            );

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
     * Tính số tiền phải thanh toán sau giảm giá và VAT.
     * VAT được tính trên giá trị thực trả của hàng hóa, trước phí vận chuyển.
     */
    private function calculateCheckoutAmounts(float $subtotal, float $discountAmount): array
    {
        $taxableAmount = round(max(0, $subtotal - $discountAmount), 2);
        // Lấy thuế VAT từ Settings (mặc định 10%), sau đó chuyển sang số thập phân (chia 100)
        $taxRatePercentage = (float) \App\Models\Setting::get('tax_rate', 10);
        $taxRate = $taxRatePercentage / 100;
        $taxAmount = round($taxableAmount * $taxRate, 0);
        $shippingFee = 0.0;
        $finalTotal = $taxableAmount + $taxAmount + $shippingFee;

        return compact('taxableAmount', 'taxRate', 'taxAmount', 'shippingFee', 'finalTotal');
    }

    /**
     * Hiển thị giao diện thanh toán.
     */
    public function index()
    {
        /** @var User|null $user */
        $user = Auth::user();

        // Nếu trước đó user đã tạo đơn nhưng rời khỏi cổng thanh toán online
        // (bấm Back/đóng tab) mà chưa hoàn tất, hiển thị lại đơn đó để "Tiếp tục thanh toán"
        // thay vì cho đặt hàng mới đè lên.
        $pendingPaymentOrder = null;
        if ($pendingOrderId = session('pending_payment_order_id')) {
            $query = Order::where('id', $pendingOrderId)
                ->where('status', 'pending')
                ->where('payment_status', 'pending')
                ->where('payment_method', 'vnpay');

            if ($user) {
                $query->where('user_id', $user->id);
            } else {
                $query->whereNull('user_id');
            }

            $pendingPaymentOrder = $query->first();

            if (! $pendingPaymentOrder) {
                session()->forget('pending_payment_order_id');
            }
        }

        // Nếu khách quay lại từ VNPay, luôn đưa về trang trạng thái đơn hàng đang chờ
        // thanh toán thay vì dựng lại checkout từ giỏ hàng hiện tại.
        if ($pendingPaymentOrder) {
            return redirect()->route('checkout.success', $pendingPaymentOrder);
        }

        ['items' => $items, 'total' => $total, 'isBuyNow' => $isBuyNow] = $this->resolveCheckoutData();

        if (! $isBuyNow && $items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Vui lòng chọn ít nhất một sản phẩm để thanh toán.');
        }

        $unavailableItems = $items->filter(function ($item) {
            return ! $item->product
                || $item->product->trashed()
                || ! $item->product->is_active
                || ($item->variant && ! $item->variant->is_active);
        });

        if ($unavailableItems->isNotEmpty()) {
            if ($isBuyNow) {
                session()->forget('buy_now_item');
            }

            return redirect()->route('cart.index')->with(
                'error',
                'Một hoặc nhiều sản phẩm đã ngừng bán. Vui lòng bỏ chọn hoặc xóa sản phẩm đó khỏi giỏ hàng.'
            );
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
        $defaultAddress = null;
        $walletCoupons = collect();

        if ($user) {
            $defaultAddress = $user->addresses()->where('is_default', true)->first()
                ?? $user->addresses()->first();

            $now = now();
            $walletCoupons = $user->savedCoupons()
                ->with(['eligibleUsers'])
                ->where('coupons.is_active', true)
                ->where(function ($query) use ($now) {
                    $query->whereNull('coupons.starts_at')->orWhere('coupons.starts_at', '<=', $now);
                })
                ->where(function ($query) use ($now) {
                    $query->whereNull('coupons.expires_at')->orWhere('coupons.expires_at', '>=', $now);
                })
                ->where(function ($query) {
                    $query->whereNull('coupons.usage_limit')->orWhereColumn('coupons.used_count', '<', 'coupons.usage_limit');
                })
                ->orderByPivot('created_at', 'desc')
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
        }

        $amounts = $this->calculateCheckoutAmounts($total, $discountAmount);
        $taxAmount = $amounts['taxAmount'];
        $taxRate = $amounts['taxRate'];
        $finalTotal = $amounts['finalTotal'];

        $codMaxAmount = (float) config('shop.cod_max_amount');
        $disableCod = $finalTotal > $codMaxAmount;
        $defaultPaymentMethod = $disableCod ? 'vnpay' : 'cod';

        // Bắt buộc trình duyệt phải gọi lại server (không phục hồi từ bfcache) khi user
        // bấm nút Back từ cổng thanh toán VNPay, để banner "Tiếp tục thanh toán" luôn cập nhật.
        return response()
            ->view('checkout.index', compact(
                'items', 'total', 'defaultAddress', 'disableCod', 'defaultPaymentMethod',
                'codMaxAmount', 'discountAmount', 'appliedCouponsData', 'walletCoupons', 'isBuyNow',
                'pendingPaymentOrder', 'taxAmount', 'taxRate', 'finalTotal'
            ))
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache');
    }

    public function applyCoupon(Request $request)
    {
        $request->validate(['code' => 'required|string']);

        ['items' => $items, 'total' => $total] = $this->resolveCheckoutData();

        /** @var User|null $user */
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
        /** @var User|null $user */
        $user = Auth::user();

        ['items' => $items, 'total' => $total, 'isBuyNow' => $isBuyNow] = $this->resolveCheckoutData();

        if (! $isBuyNow && $items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Vui lòng chọn ít nhất một sản phẩm để thanh toán.');
        }

        $shippingAddress = $this->administrativeAddressResolver->resolve(
            $request->province_code,
            $request->ward_code,
            $request->shipping_address,
        );

        try {
            $order = DB::transaction(function () use ($request, $items, $total, $isBuyNow, $user, $shippingAddress) {
                // 1. Kiểm tra tồn kho và Flash Sale của tất cả sản phẩm

                foreach ($items as $item) {
                    // Kiểm tra sản phẩm còn tồn tại
                    if (!$item->product) {
                        throw new Exception("Sản phẩm không còn tồn tại hoặc đã bị xóa khỏi hệ thống.");
                    }

                    // Kiểm tra sản phẩm chưa bị ẩn
                    if ($item->product->trashed() || ! $item->product->is_active) {
                        throw new Exception("Sản phẩm {$item->product->name} hiện không khả dụng (đã bị ẩn hoặc ngừng bán).");
                    }

                    // Nếu có biến thể thì kiểm tra biến thể
                    if ($item->product->variants()->exists() && ! $item->variant_id) {
                        throw new Exception("Vui lĂ²ng chá»n biáº¿n thá»ƒ cho sáº£n pháº©m {$item->product->name}.");
                    }

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
                    $result = $this->couponService->applyMultiple($codes, $user, $items, $total);
                    if ($result['success']) {
                        $discountAmount = $result['discount_amount'];
                        $appliedCouponsList = $result['coupons'];
                    }
                }

                $amounts = $this->calculateCheckoutAmounts($total, $discountAmount);
                $taxAmount = $amounts['taxAmount'];
                $shippingFee = $amounts['shippingFee'];
                $finalTotal = $amounts['finalTotal'];

                // Kiểm tra lại theo tổng tiền thực trả, bao gồm VAT.
                $codMaxAmount = (float) config('shop.cod_max_amount');
                if ($request->payment_method === 'cod' && $finalTotal > $codMaxAmount) {
                    throw new Exception('Đơn hàng có tổng giá trị vượt quá ' . number_format($codMaxAmount, 0, ',', '.') . 'đ không hỗ trợ phương thức thanh toán COD. Vui lòng chọn phương thức thanh toán trực tuyến.');
                }

                // 3. Tạo bản ghi đơn hàng
                $order = Order::create([
                    'user_id' => $user?->id,
                    'customer_email' => $request->customer_email,
                    'status' => 'pending',
                    'payment_method' => $request->payment_method,
                    'payment_status' => 'pending',
                    'subtotal' => $total,
                    'discount_amount' => $discountAmount,
                    'tax_amount' => $taxAmount,
                    'coupon_id' => !empty($appliedCouponsList) ? $appliedCouponsList[0]['coupon']->id : null,
                    'coupon_code' => !empty($appliedCouponsList) ? $appliedCouponsList[0]['coupon']->code : null,
                    'shipping_fee' => $shippingFee,
                    'total_amount' => $finalTotal,
                    'shipping_full_name' => $request->shipping_full_name,
                    'shipping_phone' => $request->shipping_phone,
                    'shipping_address' => $shippingAddress['street_address'],
                    'shipping_ward' => $shippingAddress['ward_name'],
                    'shipping_district' => null,
                    'shipping_province' => $shippingAddress['province_name'],
                    'shipping_province_code' => $shippingAddress['province_code'],
                    'shipping_ward_code' => $shippingAddress['ward_code'],
                    'administrative_version' => $shippingAddress['administrative_version'],
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
                    $flashSaleItem = $this->matchingFlashSaleItem($product, $variant, (float) $item->price);

                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                        'variant_id' => $variant ? $variant->id : null,
                        'flash_sale_item_id' => $flashSaleItem?->id,
                        'product_name' => $product->name,
                        'variant_name' => $variant ? $variant->name : null,
                        'product_thumbnail' => $variant?->image ?: $product->thumbnail,
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
                            'user_id'    => $user?->id,
                        ]);
                    }

                    // Tăng số lượng đã bán của Flash Sale (nếu mua với giá Flash Sale)
                    if ($flashSaleItem) {
                        $flashSaleItem->increment('sold', $item->quantity);
                    }
                }

                // 5. Lưu địa chỉ vào database (nếu chưa tồn tại)
                $this->saveShippingAddress($request, $shippingAddress);

                // 6. Xóa dữ liệu tạm thời
                if (! $user) {
                    session()->put('guest_checkout_order_id', $order->id);
                }

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

            // Gửi email xác nhận đơn hàng cho khách hàng
            OrderCreated::dispatch($order);

            // Điều hướng theo phương thức thanh toán
            switch ($order->payment_method) {
                case 'cod':
                    // Thanh toán khi nhận hàng: hoàn tất ngay
                    return redirect()->route('checkout.success', $order)
                        ->with('success', 'Đặt hàng thành công!');

                case 'bank':
                    // Chuyển khoản ngân hàng: hiển thị thông tin ngân hàng
                    return redirect()->route('checkout.success', $order)
                        ->with('success', 'Đơn hàng đã được tạo. Vui lòng chuyển khoản theo thông tin được gửi trong email.');

                case 'vnpay':
                case 'ewallet':
                    // VNPay/E-wallet: ghi nhớ đơn này để nếu user back lại trang /checkout trước khi
                    // hoàn tất thanh toán, ta vẫn nhận diện được và mời tiếp tục thanh toán.
                    session()->put('pending_payment_order_id', $order->id);

                    // Chuyển sang cổng thanh toán thật
                    return redirect()->route('checkout.vnpay.create', $order);

                default:
                    return redirect()->route('checkout.success', $order)
                        ->with('success', 'Đặt hàng thành công!');
            }

        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    /**
     * Trang hoàn tất đặt hàng thành công.
     */
    public function success(Order $order)
    {
        if (! $this->canAccessOrder($order)) {
            abort(403);
        }

        $timeoutMinutes = (int) config('shop.pending_order_timeout_minutes');
        if ($order->payment_method === 'vnpay'
            && $order->status === 'pending'
            && $order->payment_status === 'pending'
            && $order->created_at?->lte(now()->subMinutes($timeoutMinutes))) {
            $this->cancellationService->cancel(
                $order,
                "Tự động hủy do quá hạn thanh toán ({$timeoutMinutes} phút)."
            );
            $order->refresh();
        }

        $guestOrderUrl = $order->user_id === null
            ? $this->guestOrderAccess->showUrl($order)
            : null;
        $guestPaymentUrl = $order->user_id === null
            && $order->payment_method === 'vnpay'
            && $order->status === 'pending'
            && $order->payment_status === 'pending'
            ? $this->guestOrderAccess->paymentUrl($order)
            : null;

        return view('checkout.success', compact('order', 'guestOrderUrl', 'guestPaymentUrl'));
    }

    public function guestShow(Order $order)
    {
        abort_unless($order->user_id === null && $order->customer_email, 403);

        $order->load(['items', 'cancelledBy']);
        $guestCancelUrl = $order->status === 'pending'
            ? $this->guestOrderAccess->cancelUrl($order)
            : null;
        $guestPaymentUrl = $order->payment_method === 'vnpay'
            && $order->status === 'pending'
            && $order->payment_status === 'pending'
            ? $this->guestOrderAccess->paymentUrl($order)
            : null;

        return view('orders.guest-show', compact('order', 'guestCancelUrl', 'guestPaymentUrl'));
    }

    public function guestPay(Order $order)
    {
        abort_unless($order->user_id === null && $order->customer_email, 403);

        if ($order->payment_method !== 'vnpay' || $order->status !== 'pending' || $order->payment_status !== 'pending') {
            return redirect()->to($this->guestOrderAccess->showUrl($order))
                ->with('error', 'Đơn hàng này không còn ở trạng thái có thể thanh toán.');
        }

        // Cho phép vnpayCreate() xác thực đúng khách vãng lai vừa dùng signed URL.
        session()->put('guest_checkout_order_id', $order->id);
        session()->put('pending_payment_order_id', $order->id);

        return redirect()->route('checkout.vnpay.create', $order);
    }

    public function guestCancel(Order $order)
    {
        abort_unless($order->user_id === null && $order->customer_email, 403);

        if ($order->status !== 'pending') {
            return back()->with('error', 'Chỉ đơn hàng đang chờ xác nhận mới có thể hủy.');
        }

        if (! $this->cancellationService->cancel($order, 'Khách vãng lai hủy đơn')) {
            return back()->with('error', 'Đơn hàng vừa được xử lý nên không thể hủy.');
        }

        return back()->with('success', 'Đơn hàng đã được hủy thành công.');
    }

    /**
     * Tạo URL và chuyển hướng người dùng sang cổng VNPay.
     * Cũng được dùng lại cho luồng "Tiếp tục thanh toán" (đơn pending chưa thanh toán) -
     * KHÔNG tạo đơn hàng mới, chỉ tạo thêm một PaymentTransaction pending cho đơn đã có.
     */
    public function vnpayCreate(Request $request, Order $order)
    {
        if (! $this->canAccessOrder($order)) {
            abort(403);
        }

        if ($order->payment_status === 'paid') {
            return redirect()->route('checkout.success', $order);
        }

        if ($order->status !== 'pending') {
            return redirect()->route('checkout.success', $order)->with('error', 'Đơn hàng này không thể tiếp tục thanh toán.');
        }

        // Không cho tạo phiên VNPay mới sau deadline nếu scheduler chưa kịp chạy.
        $timeoutMinutes = (int) config('shop.pending_order_timeout_minutes');
        if ($order->payment_status === 'pending'
            && $order->created_at?->lte(now()->subMinutes($timeoutMinutes))) {
            $this->cancellationService->cancel(
                $order,
                "Tự động hủy do quá hạn thanh toán ({$timeoutMinutes} phút)."
            );

            return redirect()->route('checkout.success', $order->fresh())
                ->with('error', 'Đơn hàng đã hết thời gian thanh toán và được tự động hủy.');
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
        /** @var PaymentTransaction|null $transaction */
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

        // 3. Cập nhật đơn hàng một cách nguyên tử. Nếu lệnh tự hủy đã khóa và
        // chuyển đơn sang cancelled trước đó thì callback muộn không được hồi sinh đơn.
        if ($isSuccess && $amountMatched && $order->payment_status !== 'paid') {
            $timeoutMinutes = (int) config('shop.pending_order_timeout_minutes');
            $paymentAccepted = DB::transaction(function () use ($order, $timeoutMinutes) {
                $lockedOrder = Order::whereKey($order->id)->lockForUpdate()->first();

                if (! $lockedOrder
                    || $lockedOrder->status !== 'pending'
                    || $lockedOrder->payment_status === 'paid'
                    || $lockedOrder->created_at?->lte(now()->subMinutes($timeoutMinutes))) {
                    return false;
                }

                // Thanh toán thành công không đồng nghĩa admin đã xác nhận đơn.
                // Giữ status = pending để đơn chờ admin duyệt và chuyển trạng thái.
                $lockedOrder->update(['payment_status' => 'paid']);

                return true;
            });

            if ($paymentAccepted) {
                $order->refresh();

                // Gửi email xác nhận thanh toán
                OrderCreated::dispatch($order);

                if (session('pending_payment_order_id') == $order->id) {
                    session()->forget('pending_payment_order_id');
                }

                return redirect()->route('checkout.success', $order)
                    ->with('success', 'Thanh toán VNPay thành công!');
            }

            $order->refresh();
            if ($order->status === 'pending'
                && $order->payment_status === 'pending'
                && $order->created_at?->lte(now()->subMinutes($timeoutMinutes))) {
                $this->cancellationService->cancel(
                    $order,
                    "Tự động hủy do quá hạn thanh toán ({$timeoutMinutes} phút)."
                );
                $order->refresh();
            }

            return redirect()->route('checkout.success', $order)
                ->with('error', 'Thanh toán được trả về sau thời hạn hoặc đơn hàng đã bị hủy. Vui lòng liên hệ hỗ trợ nếu tài khoản đã bị trừ tiền.');
        }

        // Thanh toán thất bại hoặc bị hủy: đơn vẫn giữ nguyên trạng thái "pending" để
        // khách hàng có thể dùng nút "Tiếp tục thanh toán" thử lại, KHÔNG khóa đơn vĩnh viễn.
        return redirect()->route('checkout.success', $order)
            ->with('error', 'Thanh toán không thành công (mã lỗi: ' . $responseCode . ').');
    }

    /**
     * Lưu địa chỉ giao hàng vào database (nếu chưa tồn tại)
     */
    private function saveShippingAddress(CheckoutRequest $request, array $shippingAddress): void
    {
        /** @var User|null $user */
        $user = Auth::user();

        if (! $user) {
            return;
        }

        // Kiểm tra xem địa chỉ này đã tồn tại chưa
        $existingAddress = $user->addresses()
            ->where('full_name', $request->shipping_full_name)
            ->where('phone', $request->shipping_phone)
            ->where('address', $shippingAddress['street_address'])
            ->where('ward_code', $shippingAddress['ward_code'])
            ->where('province_code', $shippingAddress['province_code'])
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
            'address' => $shippingAddress['street_address'],
            'ward' => $shippingAddress['ward_name'],
            'district' => null,
            'province' => $shippingAddress['province_name'],
            'province_code' => $shippingAddress['province_code'],
            'ward_code' => $shippingAddress['ward_code'],
            'administrative_version' => $shippingAddress['administrative_version'],
            'validated_at' => now(),
            'is_default' => $isDefault,
        ]);
    }

    private function matchingFlashSaleItem(Product $product, ?ProductVariant $variant, float $price): ?FlashSaleItem
    {
        $flashSaleItem = $product->activeFlashSaleItem;

        if (! $flashSaleItem) {
            return null;
        }

        $flashSalePrice = (float) ($product->price * (1 - $flashSaleItem->discount_percent / 100));
        $expectedPrice = $flashSalePrice + ($variant ? (float) $variant->additional_price : 0);

        return abs($price - $expectedPrice) < 0.01 ? $flashSaleItem : null;
    }

    private function canAccessOrder(Order $order): bool
    {
        $user = Auth::user();

        if ($user && $order->user_id === $user->id) {
            return true;
        }

        return session()->get('guest_checkout_order_id') == $order->id;
    }
}
