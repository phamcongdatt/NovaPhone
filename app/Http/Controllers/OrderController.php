<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\OrderCancellationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function __construct(
        private readonly OrderCancellationService $cancellationService
    ) {
        $this->middleware('auth');
    }

    /**
     * Hiển thị danh sách đơn hàng đã đặt.
     */
    public function index()
    {
        $query = Auth::user()->orders()->with('items');

        if ($status = request('status')) {
            $query->where('status', $status);
        }

        if ($orderCode = request('search')) {
            $query->where('order_code', 'like', '%' . trim($orderCode) . '%');
        }

        if ($orderDate = request('order_date')) {
            $query->whereDate('created_at', $orderDate);
        }

        $orders = $query->latest()->paginate(10)->withQueryString();

        return view('orders.index', compact('orders'));
    }

    /**
     * Huỷ đơn hàng (chỉ đơn pending).
     */
    public function cancel(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        if ($order->status !== 'pending') {
            return back()->with('error', 'Chỉ đơn hàng đang chờ xác nhận mới có thể hủy.');
        }

        $cancelled = $this->cancellationService->cancel($order, 'Khách hàng hủy đơn');

        if (! $cancelled) {
            return back()->with('error', 'Đơn hàng không còn ở trạng thái có thể hủy (có thể vừa được thanh toán/xử lý).');
        }

        return back()->with('success', 'Đơn hàng đã được hủy thành công.');
    }

    /**
     * Hiển thị chi tiết đơn hàng (Timeline, Sản phẩm, Thanh toán).
     */
    public function show(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        $order->load(['items.product', 'items.variant']);

        return view('orders.show', compact('order'));
    }
}
