<?php

namespace App\Notifications;

use App\Models\ReturnRequest;
use App\Services\RefundReceiptService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReturnCompletedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public int $tries = 5;

    /** @var array<int, int> */
    public array $backoff = [60, 300, 900, 3600];

    private readonly string $returnCode;

    private readonly string $orderCode;

    private readonly float $refundAmount;

    private readonly string $refundReference;

    public function __construct(private readonly ReturnRequest $returnRequest)
    {
        // Notification được xử lý bất đồng bộ. Chụp lại dữ liệu tài chính ngay lúc
        // đóng phiếu để email không đọc phải model đã bị thay đổi/reset về sau.
        $this->returnCode = (string) $returnRequest->return_code;
        $this->orderCode = (string) $returnRequest->order->order_code;
        $this->refundAmount = (float) $returnRequest->refund_amount;
        $this->refundReference = (string) $returnRequest->refund_reference;
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $receiptReturn = clone $this->returnRequest;
        $receiptReturn->refund_amount = $this->refundAmount;
        $receiptReturn->refund_reference = $this->refundReference;
        $receipt = app(RefundReceiptService::class)->pdf($receiptReturn);

        return (new MailMessage)
            ->subject('Đã hoàn tiền yêu cầu '.$this->returnCode)
            ->greeting('Xin chào '.$notifiable->name.',')
            ->line('NovaPhone đã hoàn tiền cho yêu cầu trả hàng của đơn '.$this->orderCode.'.')
            ->line('Số tiền: '.number_format($this->refundAmount, 0, ',', '.').'₫')
            ->line('Mã giao dịch hoàn tiền: '.$this->refundReference)
            ->action('Xem yêu cầu hoàn hàng', route('returns.show', $this->returnRequest))
            ->attachData($receipt, 'hoa-don-hoan-tien-'.$this->returnCode.'.pdf', [
                'mime' => 'application/pdf',
            ])
            ->line('Yêu cầu đã hoàn tất và được đóng.');
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'return_request_id' => $this->returnRequest->id,
            'return_code' => $this->returnCode,
            'order_id' => $this->returnRequest->order_id,
            'refund_amount' => $this->refundAmount,
            'message' => 'Đã hoàn '.number_format($this->refundAmount, 0, ',', '.').'₫ cho yêu cầu '.$this->returnCode.'.',
        ];
    }
}
