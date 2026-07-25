# 🔧 HƯỚNG DẪN KHẮC PHỤC VẤN ĐỀ - NovaPhone

## ✅ Vấn đề đã sửa (24/07/2026)

### 1. **Route `coupons.apply` bị thiếu**
- **Lỗi**: `Route [coupons.apply] not defined.`
- **Nguyên nhân**: View cart gọi route không được định nghĩa
- **Giải pháp**: Thêm route vào `routes/web.php` dòng 115 + thêm phương thức `apply()` vào CouponController

### 2. **Session Driver sai**
- **Cũ**: `SESSION_DRIVER=file` (lưu ở file, không persistent)
- **Mới**: `SESSION_DRIVER=database` (lưu ở database, ổn định)
- **Tác động**: Sửa lỗi login không được vì session không lưu được

### 3. **Cache Store không optimize**
- **Cũ**: `CACHE_STORE=file` (chậm, dễ gặp lỗi)
- **Mới**: `CACHE_STORE=database` (ổn định, hiệu quả)

---

## 🧪 CÁCH TEST LẠI

### Test 1: Kiểm tra Login có hoạt động
```bash
1. Truy cập: http://127.0.0.1:8000/login
2. Nhập:
   - Email: demo@novaphone.vn
   - Password: password123
3. Hoặc ấn "Quick Login" để test nhanh
4. Kiểm tra: Trang load xong chưa? Login thành công không?
```

### Test 2: Kiểm tra Coupons Route
```bash
1. Truy cập: http://127.0.0.1:8000/coupons
2. Kiểm tra: Trang load xong chưa? Có lỗi trong Network tab không?
3. Ấn "Lưu mã" trên một coupon
4. Kiểm tra response: Success hay fail?
```

### Test 3: Kiểm tra Cart
```bash
1. Truy cập: http://127.0.0.1:8000/cart
2. Kiểm tra: Trang load xong chưa?
3. Đăng nhập rồi vào Checkout
4. Kiểm tra: Trang checkout load xong chưa?
```

### Test 4: Kiểm tra Network
```bash
1. Mở Chrome DevTools (F12)
2. Tab "Network"
3. Làm lại các bước trên
4. Kiểm tra:
   - Có request nào Failed (code 404, 500) không?
   - Response time bao lâu?
   - API endpoint gọi đúng không?
```

---

## 📊 CÓ DÙNG CÁI GÌ ĐÓ CHẬM?

### Kiểm tra performance:
```bash
# Kiểm tra database sessions
php artisan tinker
> \Illuminate\Support\Facades\DB::table('sessions')->count();

# Kiểm tra cache
> \Illuminate\Support\Facades\Cache::store('database')->get('test');

# Kiểm tra queue (nếu có)
> \Illuminate\Support\Facades\DB::table('jobs')->count();
```

---

## 🚨 Nếu còn lỗi sau sửa

### A. Nếu vẫn thấy lỗi route
```bash
php artisan route:clear
php artisan route:cache
php artisan config:clear
```

### B. Nếu login vẫn không được
```bash
# Xóa session cũ
php artisan session:table
php artisan migrate

# Test user tồn tại không
php artisan tinker
> \App\Models\User::count();
> \App\Models\User::where('email', 'demo@novaphone.vn')->first();
```

### C. Nếu vẫn có lỗi trong log
```bash
# Xem log mới nhất
tail -100 storage/logs/laravel.log

# Hoặc xem bằng artisan
php artisan tail
```

---

## 📝 DANH SÁCH ROUTES ĐÃ CÓ

```
✅ GET    /login                     - Trang đăng nhập
✅ POST   /login                     - Xử lý đăng nhập
✅ POST   /logout                    - Đăng xuất
✅ GET    /register                  - Trang đăng ký
✅ POST   /register                  - Xử lý đăng ký
✅ GET    /coupons                   - Danh sách mã giảm giá
✅ POST   /coupons/{coupon}/save     - Lưu mã giảm giá
✅ POST   /coupons/apply             - Áp dụng mã (MỚI THÊM)
✅ GET    /cart                      - Giỏ hàng
✅ POST   /cart                      - Thêm vào giỏ
✅ GET    /checkout                  - Trang thanh toán
✅ POST   /checkout                  - Đặt hàng
```

---

## 🎯 CÁC BỮ ĐỢI ĐƯỢC KIỂM TRA

1. ✅ Session được lưu đúng (database driver)
2. ✅ Cache được lưu đúng (database store)
3. ✅ Tất cả routes đã định nghĩa
4. ✅ Controllers có phương thức tương ứng
5. ✅ Views không có lỗi route undefined

---

**Ngày: 24/07/2026**
**User: claude@lachongtech.vn**
