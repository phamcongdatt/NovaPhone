@component('mail::message')
# Xác nhận đơn hàng {{ $order->order_code }}

Xin chào **{{ $user->name }}**,

Cảm ơn bạn đã đặt hàng tại **NovaPhone**. Đơn hàng của bạn đã được tiếp nhận thành công!

## Chi tiết đơn hàng

| Thông tin | Nội dung |
|-----------|---------|
| **Mã đơn hàng** | {{ $order->order_code }} |
| **Ngày đặt** | {{ $order->created_at->format('H:i d/m/Y') }} |
| **Phương thức thanh toán** | @if($order->payment_method === 'cod') Thanh toán khi nhận hàng @else Thanh toán online (VNPay) @endif |
| **Trạng thái thanh toán** | @if($order->payment_status === 'paid') <span style="color: green; font-weight: bold;">Đã thanh toán</span> @else <span style="color: orange; font-weight: bold;">Chờ thanh toán</span> @endif |

## Địa chỉ giao hàng

- **Người nhận:** {{ $order->shipping_full_name }}
- **Số điện thoại:** {{ $order->shipping_phone }}
- **Địa chỉ:** {{ $order->shipping_address }}, {{ $order->shipping_ward }}, {{ $order->shipping_district }}, {{ $order->shipping_province }}

## Sản phẩm trong đơn

@foreach ($order->items as $item)
- **{{ $item->product_name }}** (x{{ $item->quantity }})
  - Giá: {{ number_format($item->price, 0, ',', '.') }}đ
  - Tổng: {{ number_format($item->subtotal, 0, ',', '.') }}đ
  @if ($item->variant_name)
  - Mẫu mã: {{ $item->variant_name }}
  @endif

@endforeach

## Tổng kết

| Hạng mục | Giá trị |
|---------|---------|
| **Tổng tiền sản phẩm** | {{ number_format($order->subtotal, 0, ',', '.') }}đ |
@if ($order->discount_amount > 0)
| **Giảm giá** | -{{ number_format($order->discount_amount, 0, ',', '.') }}đ |
@endif
| **Phí vận chuyển** | {{ number_format($order->shipping_fee, 0, ',', '.') }}đ |
| **Tổng tiền thanh toán** | **{{ number_format($order->total_amount, 0, ',', '.') }}đ** |

---

@if ($order->payment_status === 'pending')

## Tiếp tục thanh toán

Vui lòng hoàn tất thanh toán để chúng tôi có thể xử lý đơn hàng của bạn sớm nhất.

@component('mail::button', ['url' => route('checkout.vnpay.create', $order)])
Thanh toán ngay
@endcomponent

@endif

@component('mail::button', ['url' => route('orders.show', $order)])
Xem chi tiết đơn hàng
@endcomponent

## Tiếp theo?

1. Chúng tôi sẽ xác nhận và xử lý đơn hàng trong vòng **24 giờ**.
2. Bạn sẽ nhận được thông báo khi hàng được gửi đi (kèm mã vận chuyển).
3. Theo dõi tình trạng đơn hàng trực tiếp trong **[Tài khoản của tôi > Đơn hàng]** của website.

---

Nếu bạn có bất kỳ câu hỏi nào, vui lòng liên hệ với chúng tôi:
- **Email:** support@novaphone.com
- **Hotline:** 1800-1234
- **Chat:** Trực tiếp trên website

Cảm ơn bạn đã tin tưởng **NovaPhone**!

@endcomponent
