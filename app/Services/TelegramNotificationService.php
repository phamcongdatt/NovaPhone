<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramNotificationService
{
    /**
     * Gửi thông báo đơn hàng mới qua Telegram.
     */
    public function notifyNewOrder(Order $order): bool
    {
        $enabled = Setting::get('telegram_notify_enabled', '0');
        if ($enabled !== '1') {
            return false;
        }

        $botToken = trim(Setting::get('telegram_bot_token', ''));
        $chatId = trim(Setting::get('telegram_chat_id', ''));

        if (empty($botToken) || empty($chatId)) {
            Log::warning('Telegram Notification failed: Bot Token or Chat ID is missing.');
            return false;
        }

        $message = $this->buildOrderMessage($order);

        try {
            $response = Http::withoutVerifying()->post("https://api.telegram.org/bot{$botToken}/sendMessage", [
                'chat_id' => $chatId,
                'text' => $message,
                'parse_mode' => 'HTML',
            ]);

            if ($response->successful()) {
                return true;
            }

            Log::error('Telegram Notification failed: ' . $response->body());
            return false;

        } catch (\Exception $e) {
            Log::error('Telegram Notification exception: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Tạo nội dung tin nhắn Telegram.
     */
    private function buildOrderMessage(Order $order): string
    {
        $paymentMethod = strtoupper($order->payment_method);
        $total = number_format($order->total_amount, 0, ',', '.') . ' ₫';
        $time = $order->created_at->format('H:i d/m/Y');
        
        $customerName = htmlspecialchars($order->shipping_full_name, ENT_QUOTES, 'UTF-8');
        $phone = htmlspecialchars($order->shipping_phone, ENT_QUOTES, 'UTF-8');

        $text = "🔥 <b>ĐƠN HÀNG MỚI (#{$order->order_code})</b>\n\n";
        $text .= "👤 Khách hàng: <b>{$customerName}</b>\n";
        $text .= "📞 Số điện thoại: <code>{$phone}</code>\n";
        $text .= "💰 Tổng tiền: <b>{$total}</b>\n";
        $text .= "💳 Thanh toán: <b>{$paymentMethod}</b>\n";
        $text .= "⏰ Thời gian: {$time}\n\n";
        
        $text .= "📦 <b>Sản phẩm:</b>\n";
        foreach ($order->items as $item) {
            $productName = htmlspecialchars($item->product_name, ENT_QUOTES, 'UTF-8');
            $variantName = $item->variant_name ? htmlspecialchars($item->variant_name, ENT_QUOTES, 'UTF-8') : "";
            $variant = $variantName ? " ({$variantName})" : "";
            $text .= "- {$item->quantity}x {$productName}{$variant}\n";
        }

        if (!empty($order->note)) {
            $note = htmlspecialchars($order->note, ENT_QUOTES, 'UTF-8');
            $text .= "\n📝 <b>Ghi chú:</b> {$note}";
        }

        return $text;
    }
}
