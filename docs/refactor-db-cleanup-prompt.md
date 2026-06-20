# PROMPT: Dọn dẹp & chuẩn hoá Database NovaPhone (dùng cho phiên sau)

> Copy toàn bộ nội dung dưới đây (phần trong khung) dán vào Claude Code khi muốn thực hiện.
> File này chỉ là bản lưu prompt — KHÔNG phải nhật ký thay đổi.

---

## BỐI CẢNH DỰ ÁN (đọc kỹ trước khi làm)

- Dự án **NovaPhone**: web bán điện thoại, **Laravel 12 + Blade SSR** (KHÔNG phải Angular/React dù CLAUDE.md ghi vậy).
- Xác thực: **session-based** (`config/auth.php` guard `web` → driver `session`; `SESSION_DRIVER=database`). **KHÔNG dùng JWT, KHÔNG dùng token API.**
- Lưu ý môi trường: thư mục dự án có tên Unicode bị nhân bản (phantom). **Khi đọc/ghi file dự án phải dùng tool PowerShell (Get-Content/Set-Content -Encoding utf8), KHÔNG dùng Read/Edit/Write** vì sẽ trúng bản phantom.
- Luôn trả lời bằng **tiếng Việt**.
- Quy trình bắt buộc: **lập kế hoạch (plan) và cho tôi xác nhận TRƯỚC khi sửa**. Mỗi nhóm việc tách commit riêng. Chạy `php artisan migrate:fresh --seed` (môi trường local) để kiểm tra sau khi đổi schema.

## MỤC TIÊU

Thực hiện các đề xuất dọn dẹp DB đã phân tích trước đó. Làm theo thứ tự ưu tiên, từng nhóm một, dừng lại xác nhận giữa các nhóm.

### NHÓM 1 — Gỡ cơ chế token thừa (Sanctum) [ưu tiên cao]
Lý do: app dùng session auth, không dùng token. `laravel/sanctum` tự nạp migration tạo bảng `personal_access_tokens` (từ vendor) dù không có file trong `database/migrations/`. Đây là bảng token thừa.
- Quyết định cùng tôi 1 trong 2 hướng:
  - (A) Gỡ hẳn: `composer remove laravel/sanctum`, thêm migration `Schema::dropIfExists('personal_access_tokens')`.
  - (B) Giữ package cho tương lai (API mobile) nhưng tắt bảng: gọi `Sanctum::ignoreMigrations()` trong `AppServiceProvider::register()` + migration drop bảng.
- GIỮ NGUYÊN (đây KHÔNG phải token thừa): `sessions`, `password_reset_tokens`, `users.remember_token` — session auth cần chúng.
- Kiểm tra `routes/api.php`: hiện chỉ có 1 route public `apiShow`, không gắn `auth:sanctum`. Xác nhận có cần giữ route API này không.

### NHÓM 2 — Gỡ dependency chết [ưu tiên cao]
- `spatie/laravel-permission` đang có trong composer nhưng KHÔNG dùng (phân quyền bằng enum `users.role` + `isAdmin()` + middleware `EnsureUserIsAdmin`; User không dùng trait `HasRoles`; chưa publish migration nên chưa tạo bảng).
- Đề xuất: `composer remove spatie/laravel-permission`. CHỈ giữ lại nếu quyết định nâng cấp sang role/permission động (khi đó phải thay enum, không để song song cả hai).

### NHÓM 3 — Chuẩn hoá denormalization [ưu tiên thấp, không gây lỗi]
- `product_variants.name` trùng với `storage` + `color` (suy ra được). Chọn: bỏ cột `name` + dựng accessor `getNameAttribute()` từ storage/color; HOẶC bỏ tách storage/color. KHÔNG giữ cả ba.
- `products.thumbnail` vs `product_images.is_primary`: chuẩn hoá thumbnail = ảnh primary (accessor). LƯU Ý vẫn cần một nguồn thumbnail để snapshot vào `order_items.product_thumbnail` (đây là chủ đích — giữ).
- Enum trạng thái khai báo trùng ở `orders.status` và `order_status_histories.status`: tách thành 1 PHP enum/constant dùng chung cho migration + validation.
- `cart_items.price`: cân nhắc lấy giá realtime từ product/variant khi hiển thị giỏ (chỉ snapshot khi sang `order_items`).
- `products.sku` vs `product_variants.sku`: thống nhất SKU bán hàng ở cấp variant.

## TUYỆT ĐỐI KHÔNG XÓA (đây là khung cho chức năng đã lên kế hoạch, chỉ là CHƯA code — không phải thừa)
- `wishlists` → chức năng "Lưu sản phẩm yêu thích".
- `reviews` + cột `is_visible`, `images`, `order_id` → "Đánh giá sản phẩm" + "Quản lý bình luận/đánh giá".
- `order_status_histories` → "Theo dõi/Cập nhật trạng thái đơn", "Xác nhận đơn".
- `inventories.reserved_quantity`, `low_stock_threshold` → "Quản lý tồn kho".
- `products.sold_count` → "Sản phẩm bán chạy".
- `users.status` (active/blocked) → "Khóa/Mở khóa tài khoản".
- `orders.cancelled_reason`, `cancelled_by` → "Hủy đơn hàng".
- `addresses` → hỗ trợ checkout/hồ sơ.

## ĐẦU RA MONG MUỐN
1. Một bản kế hoạch ngắn gọn (các bước, file đụng tới, rủi ro) để tôi duyệt.
2. Sau khi duyệt: thực hiện từng nhóm, commit riêng, báo cáo kết quả + lệnh test đã chạy.
3. Không tự ý làm nhóm khác ngoài phạm vi đã duyệt.
