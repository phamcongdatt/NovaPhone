# Hệ Thống Quản Lý Giao Hàng - Tài Liệu Kỹ Thuật

## Tổng Quan

Hệ thống này cải thiện quy trình xác nhận giao hàng bằng cách phân tách vai trò giữa **Admin** (xác nhận đã giao) và **User** (xác nhận đã nhận).

## Các Thay Đổi Chính

### 1. **Trạng Thái Đơn Hàng Mới**

- **Trước đây:** `pending` → `confirmed` → `processing` → `shipping` → `delivered` (cuối cùng)
- **Sau cập nhật:** `pending` → `confirmed` → `processing` → `shipping` → `delivered` → `received` (cuối cùng)

**Ý nghĩa:**
- **`delivered`**: Admin xác nhận hàng đã được giao đi (cần hình ảnh chứng minh)
- **`received`**: Người dùng xác nhận đã nhận được hàng

### 2. **Cơ Sở Dữ Liệu**

#### Migration 1: `2026_07_21_000001_add_delivery_proof_to_order_status_histories`
- Thêm cột `delivery_proof_image` vào bảng `order_status_histories` → lưu đường dẫn hình ảnh chứng minh
- Thêm cột `user_received_at` vào bảng `orders` → lưu thời gian user xác nhận nhận hàng

#### Migration 2: `2026_07_21_000002_add_received_status_to_orders`
- Cập nhật `ENUM` của cột `status` trong `orders` table
- Cập nhật `ENUM` của cột `status` trong `order_status_histories` table
- Thêm giá trị `'received'` vào danh sách các trạng thái hợp lệ

### 3. **Backend - Controller**

#### `OrderController` (User)
**Phương thức mới:** `confirmReceived(Order $order)`
```php
POST /orders/{order}/confirm-received
```
- Kiểm tra user sở hữu đơn hàng
- Kiểm tra trạng thái = `delivered` mới cho phép xác nhận
- Cập nhật `order.status = 'received'`
- Cập nhật `order.user_received_at = now()`
- Tạo record lịch sử trạng thái

#### `Admin\OrderController`
**Cập nhật phương thức:** `updateStatus(OrdrerRequest $request, Order $order)`
- Bắt buộc upload hình ảnh khi chuyển sang status `delivered`
- Lưu hình ảnh vào thư mục `storage/app/public/deliveries/`
- Lưu đường dẫn vào `OrderStatusHistory.delivery_proof_image`

**State Machine (không thay đổi chức năng):**
```
pending    → [confirmed, cancelled]
confirmed  → [processing, cancelled]
processing → [shipping]
shipping   → [delivered]
delivered  → [received]  // MỚI
received   → []          // Trạng thái cuối
cancelled  → []          // Trạng thái cuối
```

### 4. **Form Request**

#### `Admin\OrdrerRequest`
- Cập nhật validation cho `status` column
- Thêm validation cho `delivery_proof_image`: `required` khi status = `delivered`
- Hỗ trợ định dạng: `jpeg, png, jpg, webp`
- Giới hạn kích thước: 5MB

### 5. **Frontend - Admin**

**File:** `resources/views/admin/orders/show.blade.php`

**Thay đổi:**
- Form cập nhật trạng thái có `enctype="multipart/form-data"`
- Thêm input file cho `delivery_proof_image` (ẩn/hiện động)
- JavaScript toggle hiển thị input file khi select `delivered`
- Hiển thị hình ảnh trong lịch sử trạng thái (thumbnail + link download)
- Badge color cho trạng thái `received` = emerald

### 6. **Frontend - User**

**File:** `resources/views/orders/show.blade.php`

**Thay đổi:**
- Thêm bước "Hoàn thành" (trạng thái `received`) vào timeline
- Cập nhật timeline từ 4 bước → 5 bước
- Nút "Xác nhận đã nhận hàng" xuất hiện khi status = `delivered`
- Nút có style gradient (emerald-cyan) tương tự nút thanh toán

### 7. **Model Updates**

#### `Order.php`
- Thêm `'user_received_at'` vào `fillable` array
- Không cần casts thêm (lưu dạng string)

#### `OrderStatusHistory.php`
- Thêm `'delivery_proof_image'` vào `fillable` array

### 8. **Routes**

**Thêm route mới:**
```php
POST /orders/{order}/confirm-received
Route: orders.confirm-received
```

## Quy Trình Sử Dụng

### Cho Admin:

1. Vào chi tiết đơn hàng
2. Chọn select status → "Đã giao"
3. **Bắt buộc upload hình ảnh** chứng minh giao hàng
4. Thêm ghi chú (tùy chọn)
5. Click "Cập nhật trạng thái"
6. Hình ảnh lưu trong lịch sử

### Cho User:

1. Vào chi tiết đơn hàng
2. Khi status = "Đã giao", hiển thị nút "Xác nhận đã nhận hàng"
3. Click nút → confirm dialog
4. Hệ thống cập nhật status → "Đã nhận"
5. Timestamp lưu vào `user_received_at`

## Lưu Trữ Hình Ảnh

- **Thư mục:** `storage/app/public/deliveries/`
- **Tên file:** `delivery_{order_id}_{timestamp}.{ext}`
- **Truy cập:** `/storage/deliveries/...`

## Bảo Mật

- User chỉ có thể xác nhận đơn hàng của chính mình (kiểm tra `user_id`)
- Admin phải upload hình ảnh hợp lệ (image, max 5MB)
- Hình ảnh được lưu trong `public` disk để có thể truy cập

## Testing Checklist

- [ ] Admin có thể upload hình ảnh khi chuyển sang "Đã giao"
- [ ] Hình ảnh hiển thị trong lịch sử trạng thái
- [ ] User nhìn thấy nút "Xác nhận đã nhận hàng" khi status = "delivered"
- [ ] User không thể xác nhận đơn hàng người khác
- [ ] Timeline hiển thị đúng 5 bước
- [ ] Badge color cho "Đã nhận" = emerald
