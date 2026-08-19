<?php

namespace App\Console\Commands;

use App\Models\ReturnRequest;
use App\Models\User;
use App\Services\ReturnRefundService;
use App\Services\VnpayService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ReconcileVnpayRefunds extends Command
{
    protected $signature = 'returns:reconcile-vnpay';

    protected $description = 'Đối soát và đóng các yêu cầu hoàn tiền VNPay đang xử lý';

    public function handle(VnpayService $vnpayService, ReturnRefundService $refundService): int
    {
        $requests = ReturnRequest::with(['order.payments', 'reviewer'])
            ->where('status', 'refund_processing')->get();

        foreach ($requests as $returnRequest) {
            $requestedAt = $returnRequest->refund_requested_at ?? $returnRequest->updated_at;
            $reviewAfterHours = (int) config('services.vnpay.refund_review_after_hours', 24);
            if ($requestedAt?->lte(now()->subHours($reviewAfterHours))) {
                $returnRequest->update([
                    'status' => 'refund_review_required',
                    'refund_failure_reason' => "VNPay chưa xác nhận kết quả sau {$reviewAfterHours} giờ.",
                ]);
                $this->warn($returnRequest->return_code.': chuyển sang đối soát thủ công.');

                continue;
            }

            $queryInterval = (int) config('services.vnpay.refund_query_interval_minutes', 15);
            if ($returnRequest->last_refund_checked_at?->gt(now()->subMinutes($queryInterval))) {
                continue;
            }

            $returnRequest->increment('refund_check_attempts');
            $returnRequest->update(['last_refund_checked_at' => now()]);
            try {
                $result = $vnpayService->queryRefund(
                    $returnRequest->order,
                    (string) config('services.vnpay.server_ip'),
                    $returnRequest->refund_reference,
                );
                $type = (string) ($result['vnp_TransactionType'] ?? '');
                $status = (string) ($result['vnp_TransactionStatus'] ?? '');

                if (in_array($type, ['02', '03'], true) && $status === '00') {
                    $admin = $returnRequest->reviewer ?? User::where('role', 'admin')->first();
                    if (! $admin) {
                        throw new \RuntimeException('Không tìm thấy tài khoản admin để hoàn tất đối soát.');
                    }
                    $refundService->complete(
                        $returnRequest,
                        $admin,
                        'vnpay',
                        (string) $result['vnp_TransactionNo'],
                    );
                    $this->info($returnRequest->return_code.': đã hoàn tiền và đóng phiếu.');
                } elseif (in_array($type, ['02', '03'], true) && $status === '09') {
                    $returnRequest->update([
                        'status' => 'refund_failed',
                        'refund_failure_reason' => 'VNPay từ chối giao dịch hoàn tiền.',
                        'admin_note' => trim($returnRequest->admin_note."\nVNPay từ chối giao dịch hoàn tiền."),
                    ]);
                    $this->warn($returnRequest->return_code.': VNPay từ chối hoàn tiền.');
                } else {
                    $this->line($returnRequest->return_code.': vẫn đang xử lý.');
                }
            } catch (\Throwable $exception) {
                if ($exception->getCode() === 94) {
                    $this->line($returnRequest->return_code.': VNPay giới hạn truy vấn, sẽ thử lại ở lượt sau.');

                    continue;
                }

                Log::warning('Không thể đối soát hoàn tiền VNPay', [
                    'return_code' => $returnRequest->return_code,
                    'message' => $exception->getMessage(),
                ]);
                $this->error($returnRequest->return_code.': '.$exception->getMessage());
            }
        }

        return self::SUCCESS;
    }
}
