# Hệ Thống Thông Báo Đơn Hàng (Order Notification System)

## Tổng Quan

Hệ thống notification cho phép gửi thông báo email tự động khi có sự kiện quan trọng liên quan đến đơn hàng.

## Các Sự Kiện Được Hỗ Trợ

### 1. OrderCreated (Đơn hàng được tạo/thanh toán thành công)

**Khi nào được trigger:**
- Khi khách hàng tạo đơn hàng bằng phương thức COD (Thanh toán khi nhận hàng)
- Khi khách hàng hoàn tất thanh toán VNPay

**File liên quan:**
- **Event:** `app/Events/OrderCreated.php`
- **Notification:** `app/Notifications/OrderCreatedNotification.php`
- **Listener:** `app/Listeners/SendOrderCreatedNotification.php`
- **Email Template:** `resources/views/mail/order_created.blade.php`
- **Controller:** `app/Http/Controllers/CheckoutController.php`

**Chi tiết thông báo:**
- Gửi email xác nhận đơn hàng
- Lưu thông báo vào database (notifications table)
- Chứa thông tin: mã đơn, ngày đặt, địa chỉ, sản phẩm, giá cả, trạng thái thanh toán

**Email Content:**
```
- Tiêu đề: Xác nhận đơn hàng [ORDER_CODE]
- Nội dung:
  - Chi tiết đơn hàng (mã, ngày, phương thức thanh toán, trạng thái)
  - Địa chỉ giao hàng
  - Danh sách sản phẩm
  - Tính toán giá cả (subtotal, discount, shipping, total)
  - Link để xem chi tiết đơn hàng
  - Hướng dẫn tiếp theo
  - Thông tin liên hệ hỗ trợ
```

### 2. OrderCancelled (Đơn hàng bị hủy)

**Khi nào được trigger:**
- Khi khách hàng hủy đơn
- Khi admin hủy đơn

**File liên quan:**
- **Event:** `app/Events/OrderCancelled.php`
- **Notification:** `app/Notifications/OrderCancelledNotification.php`
- **Listener:** `app/Listeners/SendOrderCancelledNotification.php`
- **Email Template:** `resources/views/mail/order_cancelled.blade.php`

## Cấu Trúc Dữ Liệu

### OrderCreated Event

```php
class OrderCreated {
    public readonly Order $order;
}
```

### OrderCreatedNotification

**Channels:**
- `mail` - Gửi email
- `database` - Lưu vào bảng notifications

**Database Data:**
```php
[
    'order_id'      => int,
    'order_code'    => string,
    'total_amount'  => float,
    'message'       => string,
]
```

## Cấu Hình

### 1. Mail Configuration (.env)

```env
MAIL_MAILER=smtp
MAIL_SCHEME=tls
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_FROM_ADDRESS="support@novaphone.com"
MAIL_FROM_NAME="NovaPhone"
```

### 2. Event Service Provider

Đăng ký listeners trong `app/Providers/EventServiceProvider.php`:

```php
protected $listen = [
    OrderCreated::class => [
        SendOrderCreatedNotification::class,
    ],
    OrderCancelled::class => [
        SendOrderCancelledNotification::class,
    ],
];
```

### 3. Queue Configuration

Các listener implement `ShouldQueue` để được xử lý trong queue:

```env
QUEUE_CONNECTION=database
```

Chạy queue worker:
```bash
php artisan queue:work
```

## Luồng Hoạt Động

### Khi Đơn Hàng Được Tạo (COD)

```
CheckoutController::store()
    ↓
DB::transaction() {
    - Tạo Order record
    - Tạo OrderItem records
    - Trừ kho
    - Lưu coupons
}
    ↓
TelegramNotificationService::notifyNewOrder() [Notify Admin]
    ↓
OrderCreated::dispatch($order) [Notify Customer]
    ↓
Listener: SendOrderCreatedNotification
    ↓
Notification: OrderCreatedNotification
    ↓
Gửi Email + Lưu DB
```

### Khi Thanh Toán VNPay Thành Công

```
CheckoutController::vnpayReturn()
    ↓
VnpayService::validateReturn() [Xác thực chữ ký]
    ↓
if (success && matched amount) {
    - Update payment_status = 'paid'
    - Update status = 'confirmed'
    - Sync sold_count
}
    ↓
OrderCreated::dispatch($order) [Notify Customer]
    ↓
Notification: OrderCreatedNotification
    ↓
Gửi Email + Lưu DB
```

## Database Schema

### Notifications Table

```sql
CREATE TABLE notifications (
    id CHAR(36) PRIMARY KEY,
    notifiable_type VARCHAR(255) NOT NULL,
    notifiable_id BIGINT UNSIGNED NOT NULL,
    type VARCHAR(255) NOT NULL,
    data JSON NOT NULL,
    read_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INDEX: notifiable_type, notifiable_id
```

## Sử Dụng

### Lấy Notifications của User

```php
$user = Auth::user();

// Tất cả thông báo
$all = $user->notifications;

// Chưa đọc
$unread = $user->unreadNotifications;

// Đánh dấu là đã đọc
$notification->markAsRead();
$user->unreadNotifications->each->markAsRead();
```

### Manually Dispatch Event

```php
use App\Events\OrderCreated;
use App\Models\Order;

$order = Order::find(1);
OrderCreated::dispatch($order);
```

## Testing

### Chạy Unit Tests

```bash
php artisan test tests/Feature/OrderCreatedNotificationTest.php
```

### Test Email Manually

Cấu hình `.env` để sử dụng `log` driver:

```env
MAIL_MAILER=log
MAIL_LOG_CHANNEL=single
```

Email sẽ được log vào `storage/logs/laravel.log`

## Troubleshooting

### Email không được gửi

1. **Kiểm tra MAIL_MAILER** trong `.env` (phải là `smtp` hoặc `log`)
2. **Kiểm tra queue connection:**
   ```bash
   php artisan queue:work
   ```
3. **Kiểm tra logs:**
   ```bash
   tail -f storage/logs/laravel.log
   ```

### Notification không xuất hiện trong database

1. Kiểm tra notification channel bao gồm `database`
2. Kiểm tra bảng `notifications` tồn tại
3. Chạy `php artisan migrate`

### Lỗi "Undefined method via()"

Đảm bảo notification class implement `Notification` interface:
```php
class OrderCreatedNotification extends Notification
```

## Best Practices

1. **Luôn sử dụng Queue** cho email/notification để tránh block request:
   ```php
   class OrderCreatedNotification extends Notification implements ShouldQueue
   ```

2. **Test notification trước deploy:**
   ```bash
   php artisan test
   ```

3. **Giới hạn tần suất:** Đặt `delay()` nếu cần:
   ```php
   public function delay($notifiable)
   {
       return now()->addMinutes(5);
   }
   ```

4. **Theo dõi logs:**
   ```bash
   php artisan log:tail
   ```

5. **Verify Email Config:**
   ```bash
   php artisan tinker
   >>> Mail::to('test@example.com')->send(new TestMail());
   ```

## References

- [Laravel Notifications](https://laravel.com/docs/11.x/notifications)
- [Laravel Events](https://laravel.com/docs/11.x/events)
- [Laravel Queues](https://laravel.com/docs/11.x/queues)
