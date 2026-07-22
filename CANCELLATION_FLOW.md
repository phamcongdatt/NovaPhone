# Luồng Hủy Đơn Hàng Hoàn Thiện

## 📋 Tổng Quan

Khi admin hủy một đơn hàng, hệ thống sẽ:
1. ✅ Lưu lý do hủy và ai hủy
2. ✅ Gửi **email thông báo** cho khách hàng
3. ✅ Hoàn lại tồn kho
4. ✅ Giải phóng mã giảm giá
5. ✅ Ghi lịch sử trạng thái
6. ✅ Cập nhật doanh số bán

---

## 🔧 Các Thành Phần

### 1. **Event: OrderCancelled**
- **File:** `app/Events/OrderCancelled.php`
- Được dispatch khi đơn hàng bị hủy
- Chứa thông tin: Order, reason, cancelledBy

### 2. **Listener: SendOrderCancelledNotification**
- **File:** `app/Listeners/SendOrderCancelledNotification.php`
- Lắng nghe event `OrderCancelled`
- Gửi notification đến user
- Implement `ShouldQueue` để gửi asynchronously

### 3. **Notification: OrderCancelledNotification**
- **File:** `app/Notifications/OrderCancelledNotification.php`
- Gửi **email** + **database notification**
- Hiển thị: tên admin, lý do, thời gian hủy
- Support hoàn tiền thông báo

### 4. **Email Template**
- **File:** `resources/views/mail/order_cancelled.blade.php`
- Markdown format
- Hiển thị chi tiết đơn hàng, lý do, sản phẩm
- Thông báo hoàn tiền (nếu đã thanh toán)

### 5. **Service: OrderCancellationService**
- **File:** `app/Services/OrderCancellationService.php`
- Xử lý logic hủy đơn
- Dispatch event `OrderCancelled` sau transaction
- Hoàn kho, giải phóng coupon, sync sold_count

### 6. **Controller: Admin OrderController**
- **File:** `app/Http/Controllers/Admin/OrderController.php`
- Route: `PATCH /admin/orders/{order}/cancel`
- Form yêu cầu: `cancelled_reason` (bắt buộc)
- Gọi `$cancellationService->cancel()`

### 7. **Views**
- **Admin:** `resources/views/admin/orders/show.blade.php`
  - Form hủy với textarea lý do
  - Hiển thị thông tin hủy (tên admin, lý do, thời gian)
  
- **User:** `resources/views/orders/show.blade.php`
  - Hiển thị chi tiết hủy
  - Tên admin hủy, lý do, thời gian

---

## 🚀 Cách Sử Dụng

### Admin Hủy Đơn (UI)
1. Vào Admin → Orders → Chi tiết đơn
2. Tìm phần "Cập nhật trạng thái" hoặc "Hủy đơn hàng"
3. Nhập **lý do hủy** trong textarea
4. Bấm **"Hủy đơn hàng"**
5. Xác nhận hộp thoại
6. ✅ Xong! Email sẽ được gửi trong background

### Test Command
```bash
# Test hủy đơn không có admin
php artisan order:test-cancel <order_id>

# Test hủy đơn với admin
php artisan order:test-cancel <order_id> --admin-user=<admin_id>
```

---

## 📧 Email Flow

### Khi Khách Tự Hủy
- Email: "Đơn hàng đã bị hủy"
- Không hiển thị tên admin (không có admin)
- Hiển thị: mã đơn, ngày đặt, lý do, tổng tiền
- Nút: "Xem chi tiết đơn hàng"

### Khi Admin Hủy
- Email: "Đơn hàng đã bị hủy"
- Hiển thị tên admin hủy
- Hiển thị: mã đơn, ngày đặt, lý do, tổng tiền, tên admin, thời gian hủy
- **Nếu đã thanh toán:** Thông báo hoàn tiền 3-5 ngày làm việc
- Nút: "Xem chi tiết đơn hàng"

---

## 📊 Database Events

Khi hủy đơn, hệ thống tự động:

1. **`orders` table**
   - `status` → `'cancelled'`
   - `cancelled_reason` → lý do hủy
   - `cancelled_by` → ID admin (nếu admin hủy)

2. **`order_status_histories` table**
   - Tạo record mới
   - `status` → `'cancelled'`
   - `note` → lý do hủy
   - `created_by` → ID admin (nếu admin hủy)

3. **`notifications` table** (database notifications)
   - Lưu thông báo cho user
   - Hiển thị trong notification bell

4. **`inventory` table**
   - Increment quantity (hoàn lại hàng)

5. **`inventory_histories` table**
   - Ghi nhận nhập kho tự động

6. **`product_performance` table** (via SoldCountService)
   - Giảm `sold_count` nếu đơn thuộc nhóm sales

---

## ⚙️ Configuration

### Mail Configuration
File: `.env`
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=phamdat12213443@gmail.com
MAIL_PASSWORD=rmdssbuiojgvljdr
MAIL_FROM_ADDRESS="phamdat12213443@gmail.com"
MAIL_FROM_NAME="NovaPhone"
```

### Queue Configuration
File: `config/queue.php` hoặc `.env`
```env
QUEUE_CONNECTION=database
```

Email sẽ được gửi thông qua queue worker:
```bash
php artisan queue:work
```

---

## 🧪 Testing

### 1. Test via Artisan Command
```bash
php artisan order:test-cancel 1 --admin-user=2
```

### 2. Test via UI
1. Tạo đơn hàng test
2. Vào Admin → Orders → Chi tiết
3. Hủy đơn với lý do
4. Kiểm tra email (nếu queue running)
5. Kiểm tra database: `orders.cancelled_reason`, `order_status_histories`

### 3. Test Email Preview
```bash
# Dump email to log (development)
MAIL_DRIVER=log php artisan order:test-cancel 1
```

---

## 🔍 Kiểm Tra & Debug

### 1. Kiểm Tra Queue
```bash
# Xem các job chưa xử lý
SELECT * FROM jobs;

# Xem các job lỗi
SELECT * FROM failed_jobs;
```

### 2. Kiểm Tra Notification
```bash
# Database notifications
SELECT * FROM notifications WHERE notifiable_id = <user_id>;
```

### 3. Kiểm Tra Order Status
```bash
SELECT * FROM order_status_histories WHERE order_id = <order_id>;
```

### 4. Kiểm Tra Inventory
```bash
SELECT * FROM inventory_histories WHERE note LIKE '%huỷ%';
```

---

## 📝 Log Messages

Hệ thống ghi lại:
- ✅ Ai hủy (tên admin)
- ✅ Lý do hủy
- ✅ Khi nào hủy (timestamp)
- ✅ Mã đơn hàng
- ✅ Sản phẩm hoàn kho

---

## 🚨 Error Handling

### Nếu Email Không Gửi
1. Kiểm tra `QUEUE_CONNECTION` = `database`
2. Chạy queue worker: `php artisan queue:work`
3. Kiểm tra Gmail 2FA (nếu dùng Gmail)
4. Kiểm tra failed_jobs table

### Nếu Hủy Thất Bại
- Đơn không ở trạng thái `pending` hoặc `confirmed`
- Admin không có quyền
- Race condition (đơn vừa được thanh toán)

---

## 🎯 Flow Diagram

```
Admin bấm "Hủy đơn"
    ↓
Validate lý do hủy
    ↓
OrderController::cancel()
    ↓
OrderCancellationService::cancel()
    ↓ (Transaction)
├─ Cập nhật order (status='cancelled', ...)
├─ Ghi OrderStatusHistory
├─ Hoàn kho
├─ Giải phóng coupon
└─ Sync sold_count
    ↓
OrderCancelled::dispatch()
    ↓
SendOrderCancelledNotification::handle()
    ↓
$user->notify(OrderCancelledNotification)
    ↓
├─ Gửi Email (via Mail queue)
└─ Ghi Database Notification
    ↓
User thấy:
├─ Email trong inbox
├─ Notification bell
└─ Chi tiết đơn hàng (tên admin, lý do, thời gian)
```

---

## 📦 Migration Checklist

- ✅ `orders` table có `cancelled_reason` + `cancelled_by`
- ✅ `order_status_histories` table tồn tại
- ✅ `notifications` table tồn tại (Laravel default)
- ✅ `inventory_histories` table tồn tại

---

## 🎓 Next Steps

1. **Deploy & Test**
   - Test hủy đơn qua admin UI
   - Kiểm tra email trong queue

2. **Queue Worker**
   - Chạy `php artisan queue:work` trong production
   - Hoặc setup supervisor để auto-restart

3. **Notification Bell**
   - Thêm UI notification bell nếu chưa có
   - Show database notifications

4. **Webhooks (Future)**
   - Thêm webhook notifications (Telegram, Slack, etc.)
   - Thêm SMS notifications nếu cần

---

## ✅ Xác Nhận Hoàn Thiện

- ✅ Event + Listener pattern
- ✅ Queue support (async email)
- ✅ Email template đẹp
- ✅ Database notifications
- ✅ Admin tracking (ai hủy, khi nào)
- ✅ User notification
- ✅ Inventory rollback
- ✅ Status history
- ✅ Test command

**Luồng hủy đơn hàng đã hoàn thiện! 🎉**
