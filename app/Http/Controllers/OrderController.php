<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Hiển thị danh sách đơn hàng đã đặt.
     */
    public function index()
    {
        $orders = Auth::user()->orders()->latest()->paginate(10);

        return view('orders.index', compact('orders'));
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
