# 📊 Visual Guide - Order Cancellation System

## 🎬 User Flow Diagram

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                        ADMIN CANCELS ORDER                                  │
│                                                                              │
│  Admin Panel → Orders → Select Order → Enter Reason → "Hủy đơn"            │
└─────────────────────────────────────────────────────────────────────────────┘
                                    ↓
┌─────────────────────────────────────────────────────────────────────────────┐
│                    BACKEND PROCESSES CANCELLATION                           │
│                                                                              │
│  1. Validate form (reason required)                                         │
│  2. Check order cancellable (pending/confirmed only)                        │
│  3. Check authorization (admin only)                                        │
│  4. Call OrderCancellationService::cancel()                                 │
└─────────────────────────────────────────────────────────────────────────────┘
                                    ↓
┌─────────────────────────────────────────────────────────────────────────────┐
│                      DATABASE TRANSACTION                                    │
│                                                                              │
│  BEGIN TRANSACTION                                                          │
│  ├─ Lock order row (prevent race condition)                                │
│  ├─ Update order.status = 'cancelled'                                      │
│  ├─ Save cancelled_reason & cancelled_by                                   │
│  ├─ Create OrderStatusHistory record                                        │
│  ├─ Sync SoldCountService (decrease sold_count)                            │
│  ├─ Restore inventory (increment stock)                                    │
│  ├─ Create InventoryHistory record                                          │
│  ├─ Release coupon usage (decrement used_count)                            │
│  COMMIT TRANSACTION                                                         │
└─────────────────────────────────────────────────────────────────────────────┘
                                    ↓
┌─────────────────────────────────────────────────────────────────────────────┐
│                      DISPATCH EVENT                                          │
│                                                                              │
│  OrderCancelled::dispatch(                                                  │
│      order: Order,                                                          │
│      reason: string,                                                        │
│      cancelledBy: User                                                      │
│  )                                                                           │
└─────────────────────────────────────────────────────────────────────────────┘
                                    ↓
┌─────────────────────────────────────────────────────────────────────────────┐
│                     QUEUE JOB CREATED                                        │
│                                                                              │
│  Queue Table (jobs):                                                        │
│  ├─ ID: 123                                                                 │
│  ├─ Queue: default                                                          │
│  ├─ Payload: {event, order_id, reason, admin_id}                          │
│  ├─ Created: 2026-07-21 08:30:00                                            │
│  └─ Attempts: 0                                                             │
└─────────────────────────────────────────────────────────────────────────────┘
                                    ↓
┌─────────────────────────────────────────────────────────────────────────────┐
│                   QUEUE WORKER PROCESSES JOB                                │
│                                                                              │
│  php artisan queue:work (running)                                           │
│  ├─ Poll jobs table every 3 seconds                                        │
│  ├─ Find job with ID 123                                                    │
│  ├─ Deserialize event                                                       │
│  └─ Fire event listener                                                     │
└─────────────────────────────────────────────────────────────────────────────┘
                                    ↓
┌─────────────────────────────────────────────────────────────────────────────┐
│              SendOrderCancelledNotification LISTENER                         │
│                                                                              │
│  handle(OrderCancelled $event)                                              │
│  {                                                                           │
│      $event->order->user->notify(                                           │
│          new OrderCancelledNotification(...)                                │
│      );                                                                      │
│  }                                                                           │
└─────────────────────────────────────────────────────────────────────────────┘
                                    ↓
┌─────────────────────────────────────────────────────────────────────────────┐
│              OrderCancelledNotification CHANNELS                            │
│                                                                              │
│  via() → ['mail', 'database']                                               │
│  ├─ toMail() → Send email via SMTP                                         │
│  └─ toDatabase() → Save notification record                                 │
└─────────────────────────────────────────────────────────────────────────────┘
                                    ↓
        ┌────────────────────────────┴────────────────────────────┐
        ↓                                                           ↓
┌──────────────────────────────┐                   ┌──────────────────────────────┐
│     EMAIL SENT               │                   │   DB NOTIFICATION SAVED      │
├──────────────────────────────┤                   ├──────────────────────────────┤
│ To: user@email.com           │                   │ notifications table:         │
│ From: NovaPhone              │                   │ ├─ notifiable_id: 5          │
│ Subject: Đơn đã hủy          │                   │ ├─ type: OrderCancelled      │
│                              │                   │ ├─ data: {reason, admin}     │
│ Body:                        │                   │ ├─ created_at: now()         │
│ ├─ Tên admin: Nguyễn Văn A  │                   │ └─ read_at: null             │
│ ├─ Lý do: Out of stock       │                   └──────────────────────────────┘
│ ├─ Thời gian: 08:30 21/7    │
│ ├─ Mã đơn: NVP-XXXXX         │
│ ├─ Sản phẩm: [list]          │
│ └─ Button: Xem chi tiết      │
└──────────────────────────────┘
        ↓
┌──────────────────────────────┐
│  USER OPENS EMAIL INBOX      │
└──────────────────────────────┘
        ↓
┌──────────────────────────────┐
│ Email from NovaPhone         │
│ Subject: Đơn hàng đã bị hủy  │
│ [Reads details]              │
│ [Clicks view order link]     │
└──────────────────────────────┘
        ↓
┌──────────────────────────────────────────────────────────────────────────────┐
│           USER VIEWS ORDER ON WEBSITE (orders/show)                          │
├──────────────────────────────────────────────────────────────────────────────┤
│                                                                               │
│ ┌──────────────────────────────────────────────────────────────────────────┐ │
│ │ ⚠️  THÔNG BÁO                                                            │ │
│ │ Đơn hàng đã bị hủy                                                       │ │
│ │                                                                          │ │
│ │ Lý do hủy: Out of stock                                                 │ │
│ │ Được hủy bởi: Nguyễn Văn A                    ← WHO ✨                  │ │
│ │ Thời gian hủy: 08:30 21/07/2026              ← WHEN ✨                 │ │
│ └──────────────────────────────────────────────────────────────────────────┘ │
│                                                                               │
│ [Chi tiết sản phẩm]                                                          │
│ [Thông tin giao hàng]                                                        │
│ [Hoá đơn thanh toán]                                                         │
│   ...                                                                        │
│   Nếu đã thanh toán: Tiền sẽ hoàn lại 3-5 ngày                             │
│                                                                               │
└──────────────────────────────────────────────────────────────────────────────┘
        ↓
┌──────────────────────────────────────────────────────────────────────────────┐
│                  ADMIN ALSO SEES CANCELLATION                                │
├──────────────────────────────────────────────────────────────────────────────┤
│                                                                               │
│ Admin Orders → Chi tiết đơn:                                                 │
│ ├─ Đơn đã bị hủy                                                             │
│ │  ├─ Lý do hủy: Out of stock                                               │
│ │  ├─ Hủy bởi: Nguyễn Văn A (tên admin tự động)                            │
│ │  └─ Thời gian hủy: 08:30 21/07/2026 (timestamp)                           │
│ │                                                                            │
│ └─ Lịch sử trạng thái:                                                       │
│    └─ Cancelled - Out of stock - 08:30 21/07 · Nguyễn Văn A                 │
│                                                                               │
└──────────────────────────────────────────────────────────────────────────────┘
```

---

## 🗄️ Database State Changes

```
BEFORE CANCELLATION:
┌─────────────┐
│   Orders    │
├─────────────┤
│ id: 1       │
│ status: pending ← Not cancelled yet
│ cancelled_reason: null
│ cancelled_by: null
└─────────────┘

AFTER CANCELLATION:
┌──────────────────────┐
│     Orders           │
├──────────────────────┤
│ id: 1                │
│ status: cancelled ✅ ← Changed
│ cancelled_reason: "Out of stock" ✅ ← New
│ cancelled_by: 2 ✅ ← Admin ID
└──────────────────────┘

NEW RECORDS:
┌──────────────────────────────────────────┐
│        OrderStatusHistory                │
├──────────────────────────────────────────┤
│ order_id: 1                              │
│ status: cancelled                        │
│ note: Out of stock                       │
│ created_by: 2 (admin)                    │
│ created_at: 2026-07-21 08:30:00          │
└──────────────────────────────────────────┘

┌──────────────────────────────────────────┐
│        Notifications (Database)          │
├──────────────────────────────────────────┤
│ id: 123                                  │
│ notifiable_id: 5 (user)                  │
│ notifiable_type: User                    │
│ type: OrderCancelledNotification         │
│ data: {                                  │
│   order_id: 1,                           │
│   reason: "Out of stock",                │
│   admin_name: "Nguyễn Văn A"             │
│ }                                        │
│ created_at: 2026-07-21 08:30:00          │
│ read_at: null                            │
└──────────────────────────────────────────┘

┌──────────────────────────────────────────┐
│     InventoryHistory (Restore)           │
├──────────────────────────────────────────┤
│ product_id: 5                            │
│ variant_id: null                         │
│ type: import ✅ (restore/refund)         │
│ quantity: 2 ✅                           │
│ note: Hoàn kho do huỷ đơn #NVP-XXXXX    │
│ user_id: 2 (admin)                       │
│ created_at: 2026-07-21 08:30:00          │
└──────────────────────────────────────────┘
```

---

## 📧 Email Template Visual

```
╔════════════════════════════════════════════════════════════════════╗
║                         NovaPhone Store                             ║
║                  📬 Notification Email                             ║
╠════════════════════════════════════════════════════════════════════╣
║                                                                    ║
║ Xin chào Nguyễn Văn B,                                             ║
║                                                                    ║
║ Rất tiếc, đơn hàng của bạn đã bị hủy.                             ║
║                                                                    ║
║ ┌──────────────────────────────────────────────────────────────┐  ║
║ │ Thông tin hủy đơn                                            │  ║
║ │                                                              │  ║
║ │ Được hủy bởi: Nguyễn Văn A                                 │  ║
║ │ Ngày hủy: 21/07/2026 08:30                                  │  ║
║ └──────────────────────────────────────────────────────────────┘  ║
║                                                                    ║
║ ┌──────────────────────────────────────────────────────────────┐  ║
║ │ Chi tiết đơn hàng                                            │  ║
║ │                                                              │  ║
║ │ Mã đơn hàng: NVP-XXXXX                                       │  ║
║ │ Ngày đặt: 21/07/2026 08:20                                   │  ║
║ │ Lý do hủy: Out of stock - Sắp có hàng                       │  ║
║ │ Tổng giá trị: 3.000.000đ                                    │  ║
║ └──────────────────────────────────────────────────────────────┘  ║
║                                                                    ║
║ ┌──────────────────────────────────────────────────────────────┐  ║
║ │ Sản phẩm trong đơn                                           │  ║
║ │                                                              │  ║
║ │ • iPhone 15 Pro Max (x1) — 3.000.000đ                      │  ║
║ │   Màu: Titanium Black                                        │  ║
║ │ • AppleCare+ (không bao gồm)                                │  ║
║ │                                                              │  ║
║ └──────────────────────────────────────────────────────────────┘  ║
║                                                                    ║
║ ┌──────────────────────────────────────────────────────────────┐  ║
║ │ Hoàn tiền                                                    │  ║
║ │                                                              │  ║
║ │ Tiền thanh toán sẽ được hoàn lại vào tài khoản của bạn      │  ║
║ │ trong vòng 3-5 ngày làm việc.                               │  ║
║ │                                                              │  ║
║ └──────────────────────────────────────────────────────────────┘  ║
║                                                                    ║
║ ┌─────────────────────────────────────────────────────────────┐   ║
║ │  [Xem chi tiết đơn hàng]                                    │   ║
║ └─────────────────────────────────────────────────────────────┘   ║
║                                                                    ║
║ Nếu bạn có bất kỳ câu hỏi nào, vui lòng liên hệ với chúng tôi:   ║
║ • Email: support@novaphone.com                                     ║
║ • Hotline: 1800-1234                                              ║
║ • Chat: Trực tiếp trên website                                     ║
║                                                                    ║
║ Cảm ơn bạn đã tin tưởng NovaPhone!                                ║
║                                                                    ║
║ Trân trọng,                                                        ║
║ Đội ngũ NovaPhone                                                  ║
║                                                                    ║
╚════════════════════════════════════════════════════════════════════╝
```

---

## 🔄 Status History Visual

```
Timeline của Order:

created_at           confirmed_at         shipping_at         delivered_at
   ↓                     ↓                    ↓                    ↓
[Pending]  ──────→  [Confirmed]  ──────→  [Shipping]  ──────→  [Delivered]
   ↑
   └─ Tạo đơn hàng (2026-07-21 08:20)

    OR

[Pending]  ──────→  [Confirmed]  ──────→  [CANCELLED]
                                              ↑
                                      Hủy bởi: Nguyễn Văn A
                                      Lý do: Out of stock
                                      Lúc: 2026-07-21 08:30

Status History Record:
┌─────────────────────────────────────────────────────────────────────┐
│ Status: cancelled                                                    │
│ Note: Out of stock - Sắp có hàng                                   │
│ Created By: Nguyễn Văn A (Admin)                                   │
│ Created At: 2026-07-21 08:30:00                                    │
└─────────────────────────────────────────────────────────────────────┘
```

---

## 🎛️ Admin Panel Visual

```
Admin Orders Detail Page:

┌─────────────────────────────────────────────────────────────────────┐
│ ← Quay lại danh sách                              [Đã hủy]          │
│                                                                      │
│ Chi tiết đơn hàng                                                   │
│ NVP-XXXXX              Đặt lúc 08:20 21/07/2026                     │
│                                                                      │
│ [Sản phẩm 1]                                      3.000.000đ        │
│ [Sản phẩm 2]                                        500.000đ        │
│                                                                      │
│ Tạm tính: 3.500.000đ                                                │
│ Giảm giá (SUMMER): -350.000đ                                        │
│ Phí vận chuyển: 0đ                                                  │
│ ─────────────────────────────────────                               │
│ Tổng cộng: 3.150.000đ                                               │
│                                                                      │
│ ┌─────────────────────────────────────────────────────────────┐    │
│ │ Lịch sử trạng thái                                          │    │
│ │                                                             │    │
│ │ [cancelled] Đã hủy                                          │    │
│ │ Out of stock - Sắp có hàng                                 │    │
│ │ 08:30 21/07/2026 · Nguyễn Văn A                           │    │
│ │                                                             │    │
│ │ [confirmed] Đã xác nhận                                     │    │
│ │ 08:21 21/07/2026 · System                                  │    │
│ │                                                             │    │
│ │ [pending] Chờ xác nhận                                      │    │
│ │ 08:20 21/07/2026 · System                                  │    │
│ │                                                             │    │
│ └─────────────────────────────────────────────────────────────┘    │
│                                                                      │
│ ┌─────────────────────────────────────────────────────────────┐    │
│ │ Người nhận                                                  │    │
│ │ Họ tên: Nguyễn Văn B                                       │    │
│ │ Số điện thoại: 0901234567                                  │    │
│ │ Địa chỉ: 123 Đường ABC, Phường XYZ, Quận 1, TP.HCM        │    │
│ └─────────────────────────────────────────────────────────────┘    │
│                                                                      │
│ ┌─────────────────────────────────────────────────────────────┐    │
│ │ Thanh toán                                                  │    │
│ │ Phương thức: COD (khi nhận hàng)                            │    │
│ │ Trạng thái: Chưa thanh toán                                │    │
│ └─────────────────────────────────────────────────────────────┘    │
│                                                                      │
│ ┌─────────────────────────────────────────────────────────────┐    │
│ │ ⚠️ Đơn đã bị hủy                                             │    │
│ │                                                             │    │
│ │ Lý do hủy: Out of stock - Sắp có hàng                      │    │
│ │ Hủy bởi: Nguyễn Văn A                   ← WHO ✨           │    │
│ │ Thời gian hủy: 08:30 21/07/2026        ← WHEN ✨          │    │
│ │                                                             │    │
│ └─────────────────────────────────────────────────────────────┘    │
│                                                                      │
└─────────────────────────────────────────────────────────────────────┘
```

---

## 📱 User Portal Visual

```
User Orders Detail Page:

┌─────────────────────────────────────────────────────────────────────┐
│ ← Quay lại danh sách                              [Đặt 08:20 21/7] │
│                                                                      │
│ Chi tiết đơn hàng                                                   │
│                                                                      │
│ ┌─────────────────────────────────────────────────────────────┐    │
│ │ ⚠️ THÔNG BÁO                                                 │    │
│ │                                                             │    │
│ │ ✕ Đơn hàng đã bị hủy                                        │    │
│ │                                                             │    │
│ │ Lý do hủy: Out of stock - Sắp có hàng                      │    │
│ │ Được hủy bởi: Nguyễn Văn A              ← WHO ✨           │    │
│ │ Thời gian hủy: 08:30 21/07/2026        ← WHEN ✨          │    │
│ │                                                             │    │
│ └─────────────────────────────────────────────────────────────┘    │
│                                                                      │
│ ┌─────────────────────────────────────────────────────────────┐    │
│ │ Sản phẩm trong đơn hàng                                     │    │
│ │                                                             │    │
│ │ [Thumb] iPhone 15 Pro Max                    3.000.000đ   │    │
│ │         Mẫu mã: Titanium Black                            │    │
│ │         Số lượng: x1                                       │    │
│ │                                                             │    │
│ └─────────────────────────────────────────────────────────────┘    │
│                                                                      │
│ ┌─────────────────────────────────────────────────────────────┐    │
│ │ Thông tin nhận hàng                                         │    │
│ │ Họ tên: Nguyễn Văn B                                       │    │
│ │ Số điện thoại: 0901234567                                  │    │
│ │ Địa chỉ: 123 Đường ABC, Phường XYZ, Quận 1, TP.HCM        │    │
│ └─────────────────────────────────────────────────────────────┘    │
│                                                                      │
│ ┌─────────────────────────────────────────────────────────────┐    │
│ │ Hoá đơn thanh toán                                          │    │
│ │ Hình thức: Thanh toán COD (Khi nhận hàng)                  │    │
│ │ Trạng thái: Chưa thanh toán                                │    │
│ │ Tiền hàng: 3.000.000đ                                       │    │
│ │ Phí giao hàng: Miễn phí                                     │    │
│ │ ─────────────────────────────────────                       │    │
│ │ Tổng tiền: 3.000.000đ                                       │    │
│ │                                                             │    │
│ │ 💡 Nếu bạn có câu hỏi về lý do hủy, vui lòng liên hệ       │    │
│ │    support@novaphone.com hoặc gọi 1800-1234.              │    │
│ │                                                             │    │
│ └─────────────────────────────────────────────────────────────┘    │
│                                                                      │
└─────────────────────────────────────────────────────────────────────┘
```

---

## 🔄 Code Execution Flow

```
REQUEST: POST /admin/orders/{order}/cancel
  ↓
MIDDLEWARE: auth, admin
  ↓
CONTROLLER: Admin\OrderController@cancel()
  ├─ Validate form (cancelled_reason required)
  ├─ Check $order->isCancellable()
  └─ Call $cancellationService->cancel()
       ↓
SERVICE: OrderCancellationService@cancel()
  ├─ DB::transaction(function() {
  │   ├─ Order::lockForUpdate()
  │   ├─ Check isCancellable()
  │   ├─ Update order
  │   ├─ Create OrderStatusHistory
  │   ├─ Sync SoldCountService
  │   ├─ Restore inventory
  │   ├─ Release coupons
  │   └─ Return true
  │ })
  │ ↓ (if true)
  └─ OrderCancelled::dispatch()
       ↓
EVENT: OrderCancelled dispatched to Queue
  ├─ Create job in jobs table
  ├─ Job waits in queue
  └─ Queue worker processes it
       ↓
LISTENER: SendOrderCancelledNotification@handle()
  └─ $user->notify(OrderCancelledNotification)
       ↓
NOTIFICATION: OrderCancelledNotification
  ├─ via() → ['mail', 'database']
  ├─ toMail()
  │  └─ Send email via SMTP
  │     ├─ Subject: Đơn hàng đã hủy
  │     ├─ Template: mail.order_cancelled
  │     └─ Include: admin name, reason, timestamp
  │
  └─ toDatabase()
     └─ Save notification record
        ├─ notifiable_id
        ├─ data (reason, admin_name)
        └─ created_at

RESULT:
  ✅ Order cancelled
  ✅ Email queued → sent
  ✅ Notification saved
  ✅ User informed
  ✅ Inventory restored
  ✅ Coupon released
  ✅ Status history recorded
```

---

**Visual guides complete! 📊**

These diagrams help understand the complete flow from admin action to user notification.
