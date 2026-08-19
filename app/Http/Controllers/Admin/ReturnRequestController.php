<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ReturnRequest;
use App\Services\ReturnRefundService;
use App\Services\VnpayService;
use Illuminate\Http\Request;

class ReturnRequestController extends Controller
{
    public function __construct(
        private readonly ReturnRefundService $refundService,
        private readonly VnpayService $vnpayService,
    ) {}

    public function index(Request $request)
    {
        $query = ReturnRequest::with(['order', 'user']);
        if (array_key_exists($request->input('status'), ReturnRequest::STATUS_LABELS)) {
            $query->where('status', $request->input('status'));
        }
        if ($search = trim((string) $request->input('search'))) {
            $query->where(function ($builder) use ($search) {
                $builder->where('return_code', 'like', "%{$search}%")
                    ->orWhereHas('order', fn ($order) => $order->where('order_code', 'like', "%{$search}%"));
            });
        }

        return view('admin.returns.index', [
            'returnRequests' => $query->latest()->paginate(15)->withQueryString(),
            'statusLabels' => ReturnRequest::STATUS_LABELS,
        ]);
    }

    public function show(ReturnRequest $returnRequest)
    {
        $returnRequest->load(['order', 'user', 'items.orderItem', 'media', 'reviewer']);

        return view('admin.returns.show', compact('returnRequest'));
    }

    public function receive(ReturnRequest $returnRequest)
    {
        if ($returnRequest->status !== 'return_shipping') {
            return back()->with('error', 'Chỉ có thể nhận kiện hàng đang được vận chuyển về cửa hàng.');
        }
        $returnRequest->update(['status' => 'store_received', 'store_received_at' => now()]);

        return back()->with('success', 'Đã xác nhận cửa hàng nhận được kiện hoàn.');
    }

    public function review(Request $request, ReturnRequest $returnRequest)
    {
        // Chấp nhận định dạng tiền Việt Nam: 20.560.650, 20 560 650 hoặc 20560650.
        $formattedReturnShippingFee = (string) $request->input('return_shipping_fee', '0');
        $request->merge([
            'return_shipping_fee' => (int) preg_replace('/[^0-9]/', '', $formattedReturnShippingFee),
        ]);

        $validated = $request->validate([
            'decision' => ['required', 'in:approved,rejected'],
            'admin_note' => ['required', 'string', 'max:2000'],
            'return_shipping_fee' => ['nullable', 'integer', 'min:0', 'max:100000000'],
        ]);
        if ($returnRequest->status !== 'store_received') {
            return back()->with('error', 'Chỉ duyệt sau khi cửa hàng đã nhận kiện hoàn.');
        }
        $returnRequest->update([
            'status' => $validated['decision'], 'admin_note' => $validated['admin_note'],
            'reviewed_by' => $request->user()->id, 'reviewed_at' => now(),
            'return_shipping_fee' => $validated['decision'] === 'approved'
                && $returnRequest->order->payment_method === 'cod'
                ? ($validated['return_shipping_fee'] ?? 0)
                : 0,
            'completed_at' => $validated['decision'] === 'rejected' ? now() : null,
        ]);

        return back()->with('success', $validated['decision'] === 'approved' ? 'Đã duyệt yêu cầu hoàn hàng.' : 'Đã từ chối và đóng yêu cầu.');
    }

    public function refund(Request $request, ReturnRequest $returnRequest)
    {
        $returnRequest->loadMissing(['order.payments', 'items']);

        if (in_array($returnRequest->status, ['refund_processing', 'refund_review_required'], true)) {
            $queryInterval = (int) config('services.vnpay.refund_query_interval_minutes', 15);
            $nextCheckAt = $returnRequest->last_refund_checked_at?->addMinutes($queryInterval);
            if ($returnRequest->status === 'refund_processing' && $nextCheckAt?->isFuture()) {
                return back()->with('error', 'VNPay đang giới hạn truy vấn. Có thể kiểm tra lại sau '.$nextCheckAt->format('H:i:s').'; bấm liên tục không làm hoàn tiền nhanh hơn.');
            }

            $returnRequest->increment('refund_check_attempts');
            $returnRequest->update(['last_refund_checked_at' => now()]);
            try {
                $result = $this->vnpayService->queryRefund(
                    $returnRequest->order,
                    $request->ip(),
                    $returnRequest->refund_reference,
                );
            } catch (\RuntimeException $exception) {
                if ($exception->getCode() === 94) {
                    return back()->with('success', 'VNPay giới hạn kiểm tra liên tiếp. Phiếu vẫn chờ đối soát, không có yêu cầu hoàn mới được gửi.');
                }

                return back()->with('error', $exception->getMessage());
            }

            $isCompletedRefund = $this->vnpayService->isRefundCompleted($result);
            if (! $isCompletedRefund) {
                return back()->with('success', 'VNPay chưa xác nhận hoàn thành. Hệ thống tiếp tục đối soát theo lịch.');
            }

            $reference = (string) ($result['vnp_TransactionNo'] ?? '');
            if ($reference === '' || ! $this->refundService->complete($returnRequest, $request->user(), 'vnpay', $reference)) {
                return back()->with('error', 'Không thể hoàn tất đối soát giao dịch VNPay.');
            }

            return back()->with('success', 'VNPay đã hoàn tiền thành công. Hệ thống đã tạo hóa đơn và đóng yêu cầu.');
        }

        if ($returnRequest->order->payment_method === 'cod') {
            if (! $returnRequest->refund_bank_name
                || ! $returnRequest->refund_bank_account
                || ! $returnRequest->refund_account_name) {
                return back()->with('error', 'Phiếu COD chưa có đủ tài khoản ngân hàng nhận tiền hoàn.');
            }
            $validated = $request->validate([
                'refund_reference' => ['required', 'string', 'max:100'],
            ]);
            $method = 'bank_transfer';
            $reference = $validated['refund_reference'];
        } else {
            // VNPay chỉ cho hoàn tối đa số tiền của giao dịch gốc. Phí gửi trả
            // không thể cộng vào lệnh refund về phương thức thanh toán ban đầu.
            if ((float) $returnRequest->return_shipping_fee > 0) {
                $returnRequest->update(['return_shipping_fee' => 0]);
                $returnRequest->refresh();
            }

            try {
                $result = $this->vnpayService->refund(
                    $returnRequest->order,
                    $returnRequest->calculatedRefundAmount(),
                    $returnRequest->return_code,
                    $request->user()->name,
                    $request->ip(),
                );
            } catch (\RuntimeException $exception) {
                if ($exception->getCode() === 94) {
                    $returnRequest->update([
                        'status' => 'refund_processing',
                        'refund_requested_at' => $returnRequest->refund_requested_at ?? now(),
                    ]);

                    return back()->with('success', 'VNPay đã tiếp nhận yêu cầu và đang xử lý hoàn tiền. Không gửi lại yêu cầu; hãy dùng nút kiểm tra trạng thái.');
                }

                return back()->with('error', $exception->getMessage());
            }
            $transactionStatus = (string) ($result['vnp_TransactionStatus'] ?? '');
            if ($this->vnpayService->isRefundCompleted($result)) {
                $method = 'vnpay';
                $reference = (string) ($result['vnp_TransactionNo'] ?? $result['vnp_ResponseId'] ?? '');
                if ($reference === '') {
                    return back()->with('error', 'VNPay đã báo hoàn tiền thành công nhưng phản hồi thiếu mã tham chiếu.');
                }
            } elseif (in_array($transactionStatus, ['01', '02', '04', '05', '06', '07'], true)) {
                $returnRequest->update([
                    'status' => 'refund_processing',
                    'refund_requested_at' => $returnRequest->refund_requested_at ?? now(),
                    'refund_reference' => $result['vnp_TransactionNo'] ?? $returnRequest->refund_reference,
                ]);

                return back()->with('success', 'VNPay đã tiếp nhận và đang xử lý hoàn tiền. Hệ thống sẽ tiếp tục đối soát tự động.');
            } elseif ($transactionStatus === '') {
                $returnRequest->update([
                    'status' => 'refund_review_required',
                    'refund_requested_at' => $returnRequest->refund_requested_at ?? now(),
                    'refund_reference' => $result['vnp_TransactionNo'] ?? $returnRequest->refund_reference,
                    'refund_failure_reason' => 'VNPay không trả TransactionStatus; chưa có bằng chứng tiền đã hoàn.',
                ]);

                return back()->with('error', 'VNPay chưa xác nhận trạng thái hoàn tiền. Phiếu đã chuyển sang đối soát.');
            } elseif ($transactionStatus !== '00') {
                return back()->with('error', 'VNPay từ chối hoàn tiền: '.($result['vnp_Message'] ?? 'Trạng thái '.$transactionStatus));
            }

            $method ??= 'vnpay';
            $reference ??= (string) ($result['vnp_TransactionNo'] ?? $result['vnp_ResponseId'] ?? '');
            if ($reference === '') {
                return back()->with('error', 'VNPay đã báo hoàn tiền thành công nhưng phản hồi thiếu mã tham chiếu.');
            }
        }

        if (! $this->refundService->complete($returnRequest, $request->user(), $method, $reference)) {
            return back()->with('error', 'Yêu cầu không còn ở trạng thái có thể hoàn tiền.');
        }

        return back()->with('success', 'Đã hoàn tiền, gửi thông báo cho khách và đóng yêu cầu.');
    }
}
