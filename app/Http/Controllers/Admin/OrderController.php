<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\OrdrerRequest;
use App\Models\Order;
use App\Models\OrderStatusHistory;
use App\Services\OrderCancellationService;
use App\Services\SoldCountService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function __construct(
        private readonly SoldCountService $soldCountService,
        private readonly OrderCancellationService $cancellationService
    ) {
    }

    /**
     * Các bước chuyển trạng thái hợp lệ (state machine).
     * Admin: pending → confirmed → processing → shipping → delivered
     * User: delivered → received (xác nhận đã nhận)
     * Trạng thái cuối cùng: "received" hoặc "cancelled"
     */
    private const TRANSITIONS = [
        'pending'    => ['confirmed', 'cancelled'],
        'confirmed'  => ['processing', 'cancelled'],
        'processing' => ['shipping'],
        'shipping'   => ['delivered'],
        'delivered'  => ['received'],
        'received'   => [],
        'cancelled'  => [],
    ];

    public const STATUS_LABELS = [
        'pending'    => 'Chờ xác nhận',
        'confirmed'  => 'Đã xác nhận',
        'processing' => 'Đang xử lý',
        'shipping'   => 'Đang giao hàng',
        'delivered'  => 'Đã giao',
        'received'   => 'Đã nhận',
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
        $validated = $request->validated();
        $newStatus = $validated['status'];
        $allowed   = self::TRANSITIONS[$order->status] ?? [];

        if (! in_array($newStatus, $allowed, true)) {
            return back()->withErrors([
                'status' => "Không thể chuyển đơn từ \"" . self::STATUS_LABELS[$order->status]
                    . "\" sang \"" . (self::STATUS_LABELS[$newStatus] ?? $newStatus) . "\".",
            ]);
        }

        // Require delivery proof image khi chuyển sang 'delivered'
        if ($newStatus === 'delivered' && ! $request->hasFile('delivery_proof_image')) {
            return back()->withErrors([
                'delivery_proof_image' => 'Vui lòng cung cấp hình ảnh chứng minh giao hàng.',
            ]);
        }

        DB::transaction(function () use ($order, $newStatus, $validated, $request) {
            $oldStatus = $order->status;

            $order->update(['status' => $newStatus]);

            $historyData = [
                'order_id'   => $order->id,
                'status'     => $newStatus,
                'note'       => $validated['note'] ?? null,
                'created_by' => $request->user()->id,
            ];

            // Lưu hình ảnh chứng minh giao hàng
            if ($newStatus === 'delivered' && $request->hasFile('delivery_proof_image')) {
                $file = $request->file('delivery_proof_image');
                $filename = 'delivery_' . $order->id . '_' . time() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('deliveries', $filename, 'public');
                $historyData['delivery_proof_image'] = $path;
            }

            OrderStatusHistory::create($historyData);

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

        $cancelled = $this->cancellationService->cancel($order, $validated['cancelled_reason'], $request->user());

        if (! $cancelled) {
            return back()->withErrors([
                'cancelled_reason' => 'Đơn hàng ở trạng thái hiện tại không thể hủy.',
            ]);
        }

        return back()->with('success', 'Đã hủy đơn hàng "' . $order->order_code . '".');
    }
}
