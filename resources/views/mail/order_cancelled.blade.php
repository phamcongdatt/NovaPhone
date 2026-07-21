@component('mail::message')
# Đơn hàng {{ $order->order_code }} đã bị hủy

Xin chào **{{ $user->name }}**,

Rất tiếc, đơn hàng của bạn đã bị hủy.

@if ($isAdminCancel)
## Thông tin hủy đơn

- **Được hủy bởi:** {{ $adminName ?? 'Quản trị viên' }}
- **Ngày hủy:** {{ $cancelledAt->format('H:i d/m/Y') }}
@endif

## Chi tiết đơn hàng

| Thông tin | Nội dung |
|-----------|---------|
| **Mã đơn hàng** | {{ $order->order_code }} |
| **Ngày đặt** | {{ $order->created_at->format('H:i d/m/Y') }} |
| **Lý do hủy** | {{ $reason }} |
| **Tổng giá trị** | {{ number_format($order->total_amount, 0, ',', '.') }}đ |

@if ($order->payment_status === 'paid')

## Hoàn tiền

Tiền thanh toán sẽ được hoàn lại vào tài khoản của bạn trong vòng **3-5 ngày làm việc**.

@endif

## Sản phẩm trong đơn

@foreach ($order->items as $item)
- **{{ $item->product_name }}** (x{{ $item->quantity }}) — {{ number_format($item->subtotal, 0, ',', '.') }}đ
  @if ($item->variant_name)
  - Mẫu mã: {{ $item->variant_name }}
  @endif
@endforeach

---

@component('mail::button', ['url' => route('orders.show', $order)])
Xem chi tiết đơn hàng
@endcomponent

Nếu bạn có bất kỳ câu hỏi nào hoặc muốn biết thêm chi tiết, vui lòng liên hệ với chúng tôi qua:
- **Email:** support@novaphone.com
- **Hotline:** 1800-1234
- **Chat:** Trực tiếp trên website

Cảm ơn bạn đã tin tưởng **NovaPhone**!

@endcomponent
