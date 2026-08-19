<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReturnRequest;
use App\Models\Order;
use App\Models\ReturnRequest;
use App\Services\RefundReceiptService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ReturnRequestController extends Controller
{
    public function create(Order $order)
    {
        $this->authorizeOrder($order);

        if (! $order->canRequestReturn()) {
            return redirect()->route('orders.show', $order)
                ->with('error', 'Đơn hàng không còn đủ điều kiện tạo yêu cầu hoàn hàng.');
        }

        $order->load('items');

        return view('returns.create', compact('order'));
    }

    public function store(StoreReturnRequest $request, Order $order)
    {
        $this->authorizeOrder($order);

        if (! $order->canRequestReturn()) {
            return redirect()->route('orders.show', $order)
                ->with('error', 'Đơn hàng không còn đủ điều kiện tạo yêu cầu hoàn hàng.');
        }

        $order->load('items');
        $quantities = collect($request->validated('items'))
            ->map(fn ($quantity) => (int) $quantity)
            ->filter(fn ($quantity) => $quantity > 0);

        if ($quantities->isEmpty()) {
            throw ValidationException::withMessages(['items' => 'Vui lòng chọn ít nhất một sản phẩm cần hoàn.']);
        }

        foreach ($quantities as $itemId => $quantity) {
            $item = $order->items->firstWhere('id', (int) $itemId);
            if (! $item || $quantity > $item->quantity) {
                throw ValidationException::withMessages(['items' => 'Số lượng sản phẩm hoàn không hợp lệ.']);
            }
        }

        $returnRequest = DB::transaction(function () use ($request, $order, $quantities) {
            $returnRequest = ReturnRequest::create([
                'order_id' => $order->id,
                'user_id' => $request->user()->id,
                'reason' => $request->validated('reason'),
                'note' => $request->validated('note'),
                'refund_method' => $order->payment_method === 'vnpay' ? 'vnpay' : 'bank_transfer',
                'refund_bank_name' => $request->validated('refund_bank_name'),
                'refund_bank_account' => $request->validated('refund_bank_account'),
                'refund_account_name' => mb_strtoupper((string) $request->validated('refund_account_name')),
            ]);

            $orderItemsTotal = max(1, (float) $order->items->sum('subtotal'));
            $refundableOrderTotal = max(0, (float) $order->total_amount - (float) $order->shipping_fee);

            foreach ($quantities as $itemId => $quantity) {
                $item = $order->items->firstWhere('id', (int) $itemId);
                $snapshottedLineTotal = (float) $item->taxable_amount + (float) $item->tax_amount;
                $refundAmount = $snapshottedLineTotal > 0
                    ? $snapshottedLineTotal * $quantity / max(1, (int) $item->quantity)
                    : ((float) $item->price * $quantity) / $orderItemsTotal * $refundableOrderTotal;
                $returnRequest->items()->create([
                    'order_item_id' => $item->id,
                    'quantity' => $quantity,
                    'refund_amount' => round($refundAmount, 2),
                ]);
            }

            $isFullReturn = $order->items->every(
                fn ($item) => (int) ($quantities[$item->id] ?? 0) === (int) $item->quantity
            );
            $returnRequest->update([
                'original_shipping_refund' => $isFullReturn ? $order->shipping_fee : 0,
            ]);

            foreach ($request->file('evidence', []) as $file) {
                $path = $file->store('returns/'.$returnRequest->return_code, 'public');
                $returnRequest->media()->create([
                    'path' => $path,
                    'media_type' => str_starts_with((string) $file->getMimeType(), 'video/') ? 'video' : 'image',
                    'original_name' => $file->getClientOriginalName(),
                ]);
            }

            return $returnRequest;
        });

        return redirect()->route('returns.show', $returnRequest)
            ->with('success', 'Đã tạo yêu cầu hoàn hàng. Vui lòng gửi sản phẩm về cửa hàng.');
    }

    public function show(ReturnRequest $returnRequest)
    {
        $this->authorizeReturn($returnRequest);
        $returnRequest->load(['order', 'items.orderItem', 'media']);

        return view('returns.show', compact('returnRequest'));
    }

    public function receipt(ReturnRequest $returnRequest, RefundReceiptService $receiptService)
    {
        abort_unless(
            $returnRequest->user_id === auth()->id() || auth()->user()?->isAdmin(),
            403
        );
        abort_unless($returnRequest->status === 'completed', 404);

        return response($receiptService->pdf($returnRequest), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="hoa-don-'.$returnRequest->return_code.'.pdf"',
        ]);
    }

    public function markShipped(Request $request, ReturnRequest $returnRequest)
    {
        $this->authorizeReturn($returnRequest);
        $validated = $request->validate([
            'shipping_carrier' => ['required', 'string', 'max:100'],
            'tracking_code' => ['required', 'string', 'max:100'],
        ]);

        if ($returnRequest->status !== 'requested') {
            return back()->with('error', 'Yêu cầu này không còn chờ gửi hàng.');
        }

        $returnRequest->update($validated + ['status' => 'return_shipping', 'shipped_at' => now()]);

        return back()->with('success', 'Đã xác nhận gửi hàng về cửa hàng.');
    }

    public function updateRefundAccount(Request $request, ReturnRequest $returnRequest)
    {
        $this->authorizeReturn($returnRequest);
        abort_unless($returnRequest->order()->where('payment_method', 'cod')->exists(), 422);
        abort_if(in_array($returnRequest->status, ['completed', 'rejected'], true), 422);

        $validated = $request->validate([
            'refund_bank_name' => ['required', 'string', 'max:100'],
            'refund_bank_account' => ['required', 'regex:/^[0-9]{6,20}$/'],
            'refund_account_name' => ['required', 'string', 'max:100', 'regex:/^[\pL\s]+$/u'],
        ]);
        $returnRequest->update([
            ...$validated,
            'refund_account_name' => mb_strtoupper($validated['refund_account_name']),
            'refund_method' => 'bank_transfer',
        ]);

        return back()->with('success', 'Đã cập nhật tài khoản nhận tiền hoàn.');
    }

    private function authorizeOrder(Order $order): void
    {
        abort_unless($order->user_id === auth()->id(), 403);
    }

    private function authorizeReturn(ReturnRequest $returnRequest): void
    {
        abort_unless($returnRequest->user_id === auth()->id(), 403);
    }
}
