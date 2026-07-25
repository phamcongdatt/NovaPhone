# ✅ KIỂM TRA CÁC LỖI ĐÃ ĐƯỢC SỬA

## 🔧 GỌI LỆNH KIỂM TRA

### 1. **Kiểm tra Routes đã thêm**
```bash
php artisan route:list | grep coupons
```
**Kết quả mong đợi**: Thấy 3 routes
- `GET /coupons` → coupons.index
- `POST /coupons/{coupon}/save` → coupons.save
- `POST /coupons/apply` → coupons.apply ✅ (MỚI)

---

### 2. **Kiểm tra Session Driver**
```bash
grep SESSION_DRIVER .env
```
**Kết quả mong đợi**: `SESSION_DRIVER=database` ✅

---

### 3. **Kiểm tra Cache Store**
```bash
grep CACHE_STORE .env
```
**Kết quả mong đợi**: `CACHE_STORE=database` ✅

---

## 🧪 TEST TRÊN BROWSER

### **Test 1: Trang Login**
```
1. URL: http://127.0.0.1:8000/login
2. DevTools → Network tab (F12)
3. Nhập:
   - Email: demo@novaphone.vn
   - Password: password123
4. Ấn Login
```

**Mong đợi**:
- ✅ Form submit → Network thấy POST /login (status 302 redirect)
- ✅ Redirect đến / (home)
- ✅ Sau refresh trang → vẫn logged in (không bị logout)
- ✅ Không thấy lỗi 404, 500

---

### **Test 2: Trang Cart**
```
1. URL: http://127.0.0.1:8000/cart
2. DevTools → Network tab (F12)
3. Chờ trang load
```

**Mong đợi**:
- ✅ Trang load xong (không loading mãi)
- ✅ Thấy list sản phẩm trong giỏ (nếu có)
- ✅ Không thấy lỗi "Route [coupons.apply] not defined"
- ✅ Network tab thấy các requests:
  - GET /cart (200)
  - Có thể thêm các AJAX calls khác

---

### **Test 3: Trang Coupons**
```
1. URL: http://127.0.0.1:8000/coupons
2. DevTools → Network tab (F12)
3. Chờ trang load
4. Ấn "Lưu mã" trên 1 coupon bất kỳ
```

**Mong đợi**:
- ✅ Trang load xong
- ✅ Thấy danh sách mã giảm giá
- ✅ Ấn "Lưu mã" → Network thấy POST request (status 200)
- ✅ Response có `"success": true`

---

### **Test 4: Trang Checkout**
```
1. Đăng nhập (xong Test 1)
2. Thêm sản phẩm vào giỏ
3. URL: http://127.0.0.1:8000/checkout
4. DevTools → Network tab (F12)
5. Chờ trang load
```

**Mong đợi**:
- ✅ Trang load xong
- ✅ Thấy form checkout
- ✅ Không thấy lỗi route undefined
- ✅ Network tab thấy GET /checkout (200)

---

## 🐛 NẾU VẪN CÓ LỖI

### **Nếu vẫn thấy "Route not defined"**
```bash
# Xóa config cache
php artisan config:clear
php artisan config:cache

# Xóa route cache
php artisan route:clear
php artisan route:cache
```

---

### **Nếu login vẫn không được (logout lại sau refresh)**
```bash
# 1. Tạo table sessions
php artisan session:table
php artisan migrate

# 2. Test user tồn tại
php artisan tinker
> \App\Models\User::where('email', 'demo@novaphone.vn')->first();
# Nếu null → tạo user
> \App\Models\User::create([
    'name' => 'Demo User',
    'email' => 'demo@novaphone.vn',
    'password' => bcrypt('password123'),
    'role' => 'user',
    'status' => 'active'
]);
```

---

### **Nếu vẫn load mãi không xong**
```bash
# Xem log error
tail -50 storage/logs/laravel.log

# Hoặc real-time
php artisan tail
```

---

## 📋 CÁC FILE ĐÃ SỬA

| File | Thay đổi |
|------|----------|
| `.env` | SESSION_DRIVER=database, CACHE_STORE=database |
| `routes/web.php` | Thêm `Route::post('/coupons/apply', ...)` |
| `app/Http/Controllers/CouponController.php` | Thêm method `apply()` |

---

## ✨ CÁC LỆNH ĐÃ CHẠY

```bash
php artisan cache:clear
php artisan config:cache
php artisan view:clear
```

---

**Ngày**: 24/07/2026
**Trạng thái**: ✅ Tất cả sửa xong, chờ kiểm tra
