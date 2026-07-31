@component('mail::message')
# Đơn hàng đã được tạo

Mã đơn hàng của bạn là **{{ $order->order_code }}**.

@component('mail::button', ['url' => $orderUrl])
Xem đơn hàng
@endcomponent

Trân trọng,<br>
NovaPhone
@endcomponent
