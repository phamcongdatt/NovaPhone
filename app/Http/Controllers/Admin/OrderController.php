<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\OrdrerRequest;
use App\Models\Order;
use App\Models\OrderStatusHistory;
use App\Services\SoldCountService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function __construct(
        private readonly SoldCountService $soldCountService
    ) {
    }

    /**
     * Các bước chuyển trạng thái hợp lệ (state machine).
     * Đơn đã "delivered" hoặc "cancelled" là trạng thái kết thúc.
     */
    private const TRANSITIONS = [
        'pending'    => ['confirmed', 'cancelled'],
        'confirmed'  => ['processing', 'cancelled'],
        'processing' => ['shipping'],
        'shipping'   => ['delivered'],
        'delivered'  => [],
        'cancelled'  => [],
    ];

    public const STATUS_LABELS = [
        'pending'    => 'Chờ xác nhận',
        'confirmed'  => 'Đã xác nhận',
        'processing' => 'Đang xử lý',
        'shipping'   => 'Đang giao hàng',
        'delivered'  => 'Đã giao',
        'cancelled'  => 'Đã hủy',
    ];

    // ─── Danh sách đơn hàng ─────────────────────────────────────

    public function index(Request $request)
    {
        $query = Order::query()->with('user');

        // Tìm theo mã đơn, tên hoặc SĐT người nhận
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('order_code', 'like', "%{$search}%")
                    ->orWhere('shipping_full_name', 'like', "%{$search}%")
                    ->orWhere('shipping_phone', 'like', "%{$search}%");
            });
        }

        // Lọc theo trạng thái đơn
        if (array_key_exists($request->input('status'), self::STATUS_LABELS)) {
            $query->where('status', $request->input('status'));
        }

        // Lọc theo trạng thái thanh toán
        if (in_array($request->input('payment_status'), ['pending', 'paid', 'refunded'], true)) {
            $query->where('payment_status', $request->input('payment_status'));
        }

        $orders = $query->latest()->paginate(15)->withQueryString();

        return view('admin.orders.index', [
            'orders'       => $orders,
            'filters'      => $request->only(['search', 'status', 'payment_status']),
            'statusLabels' => self::STATUS_LABELS,
        ]);
    }

    // ─── Chi tiết đơn hàng ──────────────────────────────────────

    public function show(Order $order)
    {
        $order->load([
            'user',
            'items',
            'statusHistories.creator',
            'cancelledBy',
            'payments',
        ]);

        return view('admin.orders.show', [
            'order'           => $order,
            'statusLabels'    => self::STATUS_LABELS,
            'nextStatuses'    => self::TRANSITIONS[$order->status] ?? [],
        ]);
    }

    // ─── Xác nhận / Cập nhật trạng thái ─────────────────────────

    public function updateStatus(OrdrerRequest $request, Order $order)
    {
        $newStatus = $request->validated()['status'];
        $allowed   = self::TRANSITIONS[$order->status] ?? [];

        if (! in_array($newStatus, $allowed, true)) {
            return back()->withErrors([
                'status' => "Không thể chuyển đơn từ \"" . self::STATUS_LABELS[$order->status]
                    . "\" sang \"" . (self::STATUS_LABELS[$newStatus] ?? $newStatus) . "\".",
            ]);
        }

        DB::transaction(function () use ($order, $newStatus, $request) {
            $oldStatus = $order->status;

            $order->update(['status' => $newStatus]);

            OrderStatusHistory::create([
                'order_id'   => $order->id,
                'status'     => $newStatus,
                'note'       => $request->input('note'),
                'created_by' => $request->user()->id,
            ]);

            $this->soldCountService->syncOnStatusChange($order, $oldStatus, $newStatus);
        });

        return back()->with('success', 'Đã cập nhật trạng thái đơn sang "' . self::STATUS_LABELS[$newStatus] . '".');
    }

    // ─── Hủy đơn ────────────────────────────────────────────────

    public function cancel(Request $request, Order $order)
    {
        $validated = $request->validate([
            'cancelled_reason' => ['required', 'string', 'max:1000'],
        ], [
            'cancelled_reason.required' => 'Vui lòng nhập lý do hủy đơn.',
        ]);

        if (! $order->isCancellable()) {
            return back()->withErrors([
                'cancelled_reason' => 'Đơn hàng ở trạng thái hiện tại không thể hủy.',
            ]);
        }

        DB::transaction(function () use ($order, $validated, $request) {
            $oldStatus = $order->status;

            $order->update([
                'status'           => 'cancelled',
                'cancelled_reason' => $validated['cancelled_reason'],
                'cancelled_by'     => $request->user()->id,
            ]);

            OrderStatusHistory::create([
                'order_id'   => $order->id,
                'status'     => 'cancelled',
                'note'       => $validated['cancelled_reason'],
                'created_by' => $request->user()->id,
            ]);

            // Nếu đơn đã thuộc nhóm sales thì trừ sold_count
            $this->soldCountService->syncOnStatusChange($order, $oldStatus, 'cancelled');

            // Hoàn lại tồn kho & ghi lịch sử
            foreach ($order->items as $item) {
                $inventory = $item->variant_id
                    ? \App\Models\Inventory::where('product_id', $item->product_id)->where('variant_id', $item->variant_id)->first()
                    : \App\Models\Inventory::where('product_id', $item->product_id)->whereNull('variant_id')->first();

                if ($inventory) {
                    $inventory->increment('quantity', $item->quantity);
                }

                \App\Models\InventoryHistory::create([
                    'product_id' => $item->product_id,
                    'variant_id' => $item->variant_id,
                    'type'       => 'import',
                    'quantity'   => $item->quantity,
                    'note'       => 'Hoàn kho tự động do huỷ đơn hàng #' . $order->order_code,
                    'user_id'    => $request->user()->id,
                ]);
            }
        });

        return back()->with('success', 'Đã hủy đơn hàng "' . $order->order_code . '".');
    }
}
