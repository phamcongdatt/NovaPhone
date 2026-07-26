# Backend Summary - NovaPhone E-commerce

## 📋 Mục lục
1. [Controllers & Routes](#controllers--routes)
2. [Models & Database](#models--database)
3. [Services](#services)
4. [Validation & Requests](#validation--requests)
5. [Authentication & Authorization](#authentication--authorization)
6. [API Responses](#api-responses)

---

## Controllers & Routes

### ProductController
| URL | Method | Middleware | Request | Response | Validation |
|-----|--------|-----------|---------|----------|-----------|
| `GET /products` | `index()` | public | query: `search`, `brand`, `price`, `sort` | View + `products` (paginate 12), `brands`, `categories`, `filters` | ❌ |
| `GET /products/{slug}` | `show()` | public | param: `product:slug` | View + `product`, `relatedProducts` | ❌ |
| `GET /search/quick` | `quickSearch()` | public | query: `q` | JSON array `[{name, url, price, old_price, thumbnail, relevance}]` (limit 5) | ❌ |

**Filtering & Sorting:**
- Search: name, description
- Brand: brand_id
- Price: under-5m, 5m-10m, 10m-20m, above-20m
- Sort: latest, price_asc, price_desc

---

### ProductDetailController
| URL | Method | Middleware | Request | Response | Validation |
|-----|--------|-----------|---------|----------|-----------|
| `GET /products/{slug}` | `show()` | public | param: `product:slug`, query: `order` (optional) | View + `product`, `relatedProducts`, `cartCount`, `detail`, `reviewStatus`, `reviewOrderId` | abort 404 if !is_active |
| `GET /api/products/{id}` | `apiShow()` | public | param: `product` | JSON `{data: detailPayload}` | abort 404 if !is_active |

**Detail Payload JSON:**
```json
{
  "id": int,
  "name": string,
  "slug": string,
  "sku": string,
  "description": string,
  "content": string,
  "brand": { "id": int, "name": string, "slug": string } | null,
  "category": { "id": int, "name": string, "slug": string } | null,
  "price": float,
  "sale_price": float | null,
  "effective_price": float,
  "discount_percent": int | null,
  "images": [{ "url": string, "is_primary": bool, "sort_order": int }],
  "variants": [{
    "id": int,
    "name": string,
    "storage": string,
    "color": string,
    "color_code": string,
    "additional_price": float,
    "sku": string,
    "available_quantity": int
  }],
  "specifications": [{ "label": string, "value": string }],
  "rating": {
    "average": float,
    "count": int,
    "breakdown": { "5": int, "4": int, "3": int, "2": int, "1": int }
  },
  "reviews": [{
    "id": int,
    "rating": int,
    "comment": string,
    "images": [string],
    "user": { "id": int, "name": string } | null,
    "created_at": string
  }],
  "inventory": { "available_quantity": int | null },
  "sold_count": int,
  "view_count": int
}
```

---

### AuthController
| URL | Method | Middleware | Request | Response | Validation |
|-----|--------|-----------|---------|----------|-----------|
| `GET /login` | `showLogin()` | guest | - | View `auth.login` | ❌ |
| `POST /login` | `login()` | guest | `email`, `password`, `remember` (optional) | redirect + session | ✅ email, password |
| `GET /register` | `showRegister()` | guest | - | View `auth.register` | ❌ |
| `POST /register` | `register()` | guest | `name`, `email`, `phone`, `password`, `password_confirmation`, `terms` | redirect + auto login | ✅ all fields |
| `POST /logout` | `logout()` | auth | - | redirect `/` | ❌ |
| `GET /password/change` | `showChangePassword()` | auth | - | View | ❌ |
| `POST /password/change` | `changePassword()` | auth | `current_password`, `password`, `password_confirmation` | redirect | ✅ all fields |
| `GET /forgot-password` | `showForgotPassword()` | guest | - | View | ❌ |
| `POST /forgot-password` | `sendResetLink()` | guest | `email` | redirect + `dev_link` | ✅ email exists |
| `GET /reset-password/{token}` | `showResetPassword()` | guest | param: `token`, query: `email` | View | ❌ |
| `POST /reset-password` | `resetPassword()` | guest | `token`, `email`, `password`, `password_confirmation` | redirect | ✅ token valid |
| `GET /auth/{provider}/redirect` | `redirectToProvider()` | guest | param: `provider` (google/facebook) | Redirect to OAuth | ❌ |
| `GET /auth/{provider}/callback` | `handleProviderCallback()` | guest | OAuth callback | redirect + login | ❌ |

---

### CartController
| URL | Method | Middleware | Request | Response | Validation |
|-----|--------|-----------|---------|----------|-----------|
| `GET /cart` | `index()` | public | - | View + cart items | ❌ |
| `POST /cart` | `store()` | public | `product_id`, `variant_id`, `quantity` | JSON/redirect | ✅ product, variant |
| `POST /cart/buy-now` | `buyNow()` | public | `product_id`, `variant_id`, `quantity` | redirect checkout | ✅ product, variant |
| `PATCH /cart/update/{item}` | `update()` | public | param: `item`, body: `quantity` | JSON | ✅ quantity > 0 |
| `DELETE /cart/remove/{item}` | `destroy()` | public | param: `item` | JSON/redirect | ❌ |
| `DELETE /cart/clear` | `clear()` | public | - | JSON/redirect | ❌ |
| `POST /cart/set-selection` | `setSelection()` | public | `selected_items` (array) | JSON | ❌ |

---

### CheckoutController
| URL | Method | Middleware | Request | Response | Validation |
|-----|--------|-----------|---------|----------|-----------|
| `GET /checkout` | `index()` | auth | - | View + items, total, addresses, coupons | ✅ cart not empty |
| `POST /checkout/apply-coupon` | `applyCoupon()` | auth | `code` | JSON/redirect | ✅ code exists |
| `POST /checkout/remove-coupon` | `removeCoupon()` | auth | `code` (optional) | JSON/redirect | ❌ |
| `POST /checkout` | `store()` | auth | 8 shipping fields + payment_method + note | redirect success/vnpay | ✅ all 8 fields + payment_method |
| `GET /checkout/success/{order}` | `success()` | auth | param: `order` | View | ✅ user owns order |
| `GET /checkout/vnpay/create/{order}` | `vnpayCreate()` | auth | param: `order` | redirect VNPay | ✅ user owns order |
| `GET /checkout/vnpay/return` | `vnpayReturn()` | public | VNPay callback | redirect + update payment | ❌ |

**Payment Methods:**
- `cod` → Success page immediately
- `vnpay` → Redirect to VNPay gateway

---

### CouponController
| URL | Method | Middleware | Request | Response | Validation |
|-----|--------|-----------|---------|----------|-----------|
| `GET /coupons` | `index()` | public | - | View + coupons, savedCouponIds | ❌ |
| `POST /coupons/{coupon}/save` | `save()` | auth | param: `coupon` | JSON | ✅ coupon active, not saved |
| `POST /coupons/apply` | `apply()` | public | `code` | JSON | ✅ code, date, limit |

---

### OrderController
| URL | Method | Middleware | Request | Response | Validation |
|-----|--------|-----------|---------|----------|-----------|
| `GET /orders` | `index()` | auth | query: `status`, `search`, `order_date` | View + orders (paginate 10) | ❌ |
| `GET /orders/{order}` | `show()` | auth | param: `order` | View + order details | ✅ user owns order |
| `POST /orders/{order}/cancel` | `cancel()` | auth | param: `order` | redirect | ✅ status == pending |
| `POST /orders/{order}/confirm-received` | `confirmReceived()` | auth | param: `order` | redirect | ✅ status == shipping |

---

### AddressController
| URL | Method | Middleware | Request | Response | Validation |
|-----|--------|-----------|---------|----------|-----------|
| `POST /addresses` | `store()` | auth | `name`, `phone`, `street`, `ward`, `district`, `province`, `is_default` | redirect + message | ✅ all fields |
| `GET /addresses/{address}` | `show()` | auth | param: `address` | JSON | ✅ user owns address |
| `PUT /addresses/{address}` | `update()` | auth | param: `address`, body: fields | redirect + message | ✅ all fields |
| `DELETE /addresses/{address}` | `destroy()` | auth | param: `address` | redirect + message | ✅ user owns address |
| `POST /addresses/{address}/set-default` | `setDefault()` | auth | param: `address` | redirect + message | ✅ user owns address |

---

### AccountController
| URL | Method | Middleware | Request | Response | Validation |
|-----|--------|-----------|---------|----------|-----------|
| `GET /account` | `show()` | auth | - | View + user info | ❌ |
| `GET /account/addresses` | `addresses()` | auth | - | View + user addresses | ❌ |

---

### WishlistController
| URL | Method | Middleware | Request | Response | Validation |
|-----|--------|-----------|---------|----------|-----------|
| `GET /wishlist` | `index()` | auth | - | View + wishlists | ❌ |
| `POST /wishlist/toggle` | `toggle()` | auth | `product_id` | JSON | ✅ product exists |

---

### ProfileController
| URL | Method | Middleware | Request | Response | Validation |
|-----|--------|-----------|---------|----------|-----------|
| `GET /profile` | `edit()` | auth | - | View + user form | ❌ |
| `PUT /profile` | `update()` | auth | `name`, `email`, `phone`, `avatar` | redirect | ✅ email unique |

---

## Models & Database

### User
- Fields: `id`, `name`, `email`, `phone`, `password`, `avatar`, `role`, `status`, `google_id`, `provider`, `provider_id`, `email_verified_at`, `created_at`, `updated_at`
- Relations: `orders()`, `addresses()`, `wishlists()`, `reviews()`, `savedCoupons()`, `cart()`
- Methods: `isAdmin()`, `isBlocked()`, `wishlistItems()` (alias)

### Product
- Fields: `id`, `name`, `slug`, `sku`, `description`, `content`, `price`, `sale_price`, `thumbnail`, `is_active`, `is_featured`, `brand_id`, `category_id`, `view_count`, `sold_count`, `created_at`, `updated_at`
- Relations: `brand`, `category`, `images`, `variants`, `reviews`, `inventory`, `activeFlashSaleItem`, `performance`

### Brand
- Fields: `id`, `name`, `slug`, `logo`, `description`, `is_active`, `created_at`, `updated_at`
- Relations: `products`

### Category
- Fields: `id`, `name`, `slug`, `description`, `status`, `is_active`, `created_at`, `updated_at`
- Relations: `products`

### ProductVariant
- Fields: `id`, `product_id`, `name`, `storage`, `color`, `color_code`, `additional_price`, `sku`, `is_active`, `created_at`, `updated_at`
- Relations: `product`, `inventory`

### ProductImage
- Fields: `id`, `product_id`, `url`, `is_primary`, `sort_order`, `created_at`, `updated_at`
- Relations: `product`

### Order
- Fields: `id`, `user_id`, `order_number`, `order_code`, `status`, `payment_method`, `payment_status`, `subtotal`, `discount_amount`, `shipping_fee`, `total_amount`, `shipping_full_name`, `shipping_phone`, `shipping_address`, `shipping_ward`, `shipping_district`, `shipping_province`, `note`, `coupon_id`, `coupon_code`, `created_at`, `updated_at`
- Relations: `user`, `items`, `reviews`, `coupons`
- Status: `pending`, `confirmed`, `processing`, `shipping`, `delivered`, `cancelled`

### Address
- Fields: `id`, `user_id`, `full_name`, `phone`, `address`, `ward`, `district`, `province`, `is_default`, `created_at`, `updated_at`
- Relations: `user`

### Cart & CartItem
- Cart: `id`, `user_id`, `created_at`, `updated_at`
- CartItem: `id`, `cart_id`, `product_id`, `variant_id`, `quantity`, `price`, `created_at`, `updated_at`

### OrderItem
- Fields: `id`, `order_id`, `product_id`, `variant_id`, `product_name`, `variant_name`, `product_thumbnail`, `price`, `quantity`, `subtotal`, `created_at`, `updated_at`
- Relations: `order`, `product`, `variant`

### Coupon
- Fields: `id`, `code`, `description`, `type`, `value`, `gift_product_id`, `max_discount`, `min_order_amount`, `usage_limit`, `used_count`, `per_user_limit`, `starts_at`, `expires_at`, `is_active`, `is_apply_sale`, `is_apply_flash_sale`, `is_stackable`, `created_at`, `updated_at`
- Relations: `orders`

### Review
- Fields: `id`, `user_id`, `product_id`, `order_id`, `rating`, `comment`, `images`, `is_visible`, `created_at`, `updated_at`

---

## Services

### CartService
- `add(productId, variantId, quantity)` → CartItem
- `update(itemIdOrKey, quantity)` → CartItem
- `remove(itemIdOrKey)` → CartItem|null
- `removeMany(itemIdsOrKeys)` → void
- `clear()` → void
- `getItems()` → Collection
- `getSelectedItems()` → Collection
- `getCount()` → int
- `getTotal()` → float
- `getAvailableStock(product, variant)` → int

### CouponService
- `apply(code, user, cartItems, totalAmount)` → array
- `applyMultiple(codes, user, cartItems, totalAmount)` → array

### OrderCancellationService
- `cancel(order, reason, cancelledBy)` → bool

### TelegramNotificationService
- `notifyNewOrder(order)` → bool

### VnpayService
- `createPaymentUrl(order, ipAddress)` → string
- `validateReturn(request)` → bool
- `isSuccessful(request)` → bool

### SoldCountService
- `syncOnStatusChange(order, oldStatus, newStatus)` → void

### CompareService
- (Handles product comparisons)

### ProductSearchService
- (Handles product search and filtering)

### ProductRankingService
- (Handles product ranking)

---

## Validation & Requests

### Login
```
email: required|email
password: required
```

### Register
```
name: required|string|max:255
email: required|email|unique:users|max:255
phone: nullable|string|max:15|unique:users
password: required|string|min:8|confirmed
terms: accepted
```

### Checkout
```
shipping_full_name: required|string|max:255
shipping_phone: required|string|max:15
shipping_province: required|string|max:255
shipping_district: required|string|max:255
shipping_ward: required|string|max:255
shipping_address: required|string|max:255
payment_method: required|in:cod,vnpay
note: nullable|string|max:1000
```

### Coupon Apply
```
code: required|string
```

### Address
```
name: required|string|max:255
phone: required|string|max:20
street: required|string|max:255
ward: required|string|max:255
district: required|string|max:255
province: required|string|max:255
is_default: nullable|boolean
```

---

## Authentication & Authorization

### Middleware
- `web`: Session-based
- `auth`: Require authenticated user
- `guest`: Only unauthenticated users
- `admin`: Require admin role
- `verified`: Require email verified

### Guard
- Default: `web` (session)
- Fallback: MustVerifyEmail if not verified

---

## API Responses

### Success Response
```json
{
  "success": true,
  "message": "string",
  "data": {}
}
```

### Error Response
```json
{
  "success": false,
  "message": "string"
}
```

### Pagination
- Default per page: 10-12
- Query string: `page`, `search`, `sort`

---

## Status Maps

### Order Status
- `pending` → Chờ xác nhận (yellow)
- `confirmed` → Đã xác nhận (blue)
- `processing` → Đang xử lý (blue)
- `shipping` → Đang giao (cyan)
- `delivered` → Đã giao (green)
- `cancelled` → Đã hủy (red)

### Payment Method
- `cod` → Thanh toán khi nhận hàng (COD) — allowed if total ≤ config('shop.cod_max_amount')
- `vnpay` → Thẻ tín dụng / VNPay — always allowed

---

**Last Updated:** 2026-07-25
