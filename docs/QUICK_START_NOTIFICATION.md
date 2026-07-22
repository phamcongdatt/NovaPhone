# Hướng Dẫn Nhanh - Hệ Thống Thông Báo Đơn Hàng

## 🚀 Khởi Động Nhanh

### 1. Setup Mail Configuration

Thêm vào `.env`:
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_FROM_ADDRESS="support@novaphone.com"
MAIL_FROM_NAME="NovaPhone"
```

> **Lưu ý:** Với Gmail, cần dùng "App Password" chứ không phải mật khẩu thường.

### 2. Chạy Migration

```bash
php artisan migrate
```

Tạo bảng `notifications` để lưu trữ thông báo trong database.

### 3. Chạy Queue Worker

```bash
php artisan queue:work
```

Queue worker sẽ xử lý việc gửi email nền.

## ✨ Tính Năng

### Email được gửi tự động trong các trường hợp:

✅ **Khi khách hàng đặt hàng thành công (COD)**
- Email xác nhận đơn hàng
- Lưu thông báo trong hệ thống

✅ **Khi khách hàng thanh toán VNPay thành công**
- Email xác nhận thanh toán
- Cập nhật trạng thái

✅ **Khi đơn hàng bị hủy**
- Email thông báo hủy đơn
- Hiển thị lý do và thông tin hoàn tiền

## 📧 Email Template

### Order Created Email (`order_created.blade.php`)

Nội dung:
```
- Tiêu đề: Xác nhận đơn hàng [CODE]
- Lời chào khách hàng
- Chi tiết đơn hàng (mã, ngày, phương thức thanh toán, trạng thái)
- Địa chỉ giao hàng
- Danh sách sản phẩm với giá cả
- Tính toán tổng (subtotal, discount, shipping, total)
- Link để xem chi tiết đơn hàng
- Hướng dẫn tiếp theo
- Thông tin liên hệ hỗ trợ
```

## 🔧 Cấu Trúc Files

```
app/
├── Events/
│   └── OrderCreated.php                 # Sự kiện đơn hàng được tạo
├── Notifications/
│   └── OrderCreatedNotification.php      # Logic gửi email
├── Listeners/
│   └── SendOrderCreatedNotification.php  # Xử lý event
└── Http/Controllers/
    └── CheckoutController.php           # Dispatch event
    
resources/views/mail/
└── order_created.blade.php              # Template email

database/migrations/
└── 2026_07_21_084431_create_notifications_table.php

tests/Feature/
└── OrderCreatedNotificationTest.php     # Unit tests
```

## 💬 Cách Hoạt Động

```
1. Khách hàng đặt hàng
        ↓
2. CheckoutController::store() được gọi
        ↓
3. Order được tạo trong database
        ↓
4. OrderCreated::dispatch($order) được gọi
        ↓
5. SendOrderCreatedNotification listener xử lý
        ↓
6. OrderCreatedNotification gửi email
        ↓
7. Email được đưa vào queue
        ↓
8. Queue worker xử lý và gửi email thực tế
```

## 🧪 Test

### Chạy Unit Tests

```bash
php artisan test tests/Feature/OrderCreatedNotificationTest.php
```

### Test Email Manually

**Option 1: Log to file**
```env
MAIL_MAILER=log
```
Email sẽ được log vào `storage/logs/laravel.log`

**Option 2: Use Tinker**
```bash
php artisan tinker

>>> use App\Models\Order;
>>> use App\Events\OrderCreated;
>>> $order = Order::find(1);
>>> OrderCreated::dispatch($order);
```

## 📊 Kiểm Tra Notifications

### Lấy notifications từ user:

```php
$user = Auth::user();

// Tất cả thông báo
$all = $user->notifications;

// Chưa đọc
$unread = $user->unreadNotifications;

// Đánh dấu đã đọc
$user->unreadNotifications->markAsRead();
```

### SQL Query:

```sql
SELECT * FROM notifications 
WHERE notifiable_id = {USER_ID} 
ORDER BY created_at DESC;
```

## ⚠️ Troubleshooting

### Email không được gửi?

1. **Kiểm tra queue worker đang chạy:**
   ```bash
   ps aux | grep queue:work
   ```

2. **Kiểm tra logs:**
   ```bash
   tail -f storage/logs/laravel.log
   ```

3. **Kiểm tra mail config:**
   ```bash
   php artisan tinker
   >>> config('mail.from')
   >>> config('mail.mailers.smtp.host')
   ```

4. **Test gửi email:**
   ```bash
   php artisan tinker
   >>> Mail::raw('Test', fn($msg) => $msg->to('test@gmail.com'))
   ```

### Notifications không được lưu?

1. Kiểm tra bảng `notifications` tồn tại:
   ```bash
   php artisan migrate
   ```

2. Kiểm tra notification class có `database` channel:
   ```php
   public function via($notifiable): array {
       return ['mail', 'database'];
   }
   ```

## 📚 Thêm Chi Tiết

Xem đầy đủ tại: [docs/ORDER_NOTIFICATION_SYSTEM.md](./ORDER_NOTIFICATION_SYSTEM.md)

## 🎯 Tiếp Theo

- [ ] Cấu hình email (Gmail/SMTP)
- [ ] Chạy migration
- [ ] Chạy queue worker
- [ ] Test gửi email
- [ ] Kiểm tra notifications trong database
- [ ] Deploy lên production

---

**Câu hỏi?** Tham khảo `docs/ORDER_NOTIFICATION_SYSTEM.md` hoặc đọc comments trong code.
