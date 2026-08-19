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

    public function __construct(private readonly ReturnRequest $returnRequest) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $receipt = app(RefundReceiptService::class)->pdf($this->returnRequest);

        return (new MailMessage)
            ->subject('Đã hoàn tiền yêu cầu '.$this->returnRequest->return_code)
            ->greeting('Xin chào '.$notifiable->name.',')
            ->line('NovaPhone đã hoàn tiền cho yêu cầu trả hàng của đơn '.$this->returnRequest->order->order_code.'.')
            ->line('Số tiền: '.number_format((float) $this->returnRequest->refund_amount, 0, ',', '.').'₫')
            ->line('Mã giao dịch hoàn tiền: '.$this->returnRequest->refund_reference)
            ->action('Xem yêu cầu hoàn hàng', route('returns.show', $this->returnRequest))
            ->attachData($receipt, 'hoa-don-hoan-tien-'.$this->returnRequest->return_code.'.pdf', [
                'mime' => 'application/pdf',
            ])
            ->line('Yêu cầu đã hoàn tất và được đóng.');
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'return_request_id' => $this->returnRequest->id,
            'return_code' => $this->returnRequest->return_code,
            'order_id' => $this->returnRequest->order_id,
            'refund_amount' => $this->returnRequest->refund_amount,
            'message' => 'Đã hoàn '.number_format((float) $this->returnRequest->refund_amount, 0, ',', '.').'₫ cho yêu cầu '.$this->returnRequest->return_code.'.',
        ];
    }
}
