@component('mail::message')
# Đơn hàng đã được tạo

Mã đơn hàng của bạn là **{{ $order->order_number ?? $order->id ?? '' }}**.

@component('mail::button', ['url' => url('/orders/' . ($order->id ?? ''))])
Xem đơn hàng
@endcomponent

Trân trọng,<br>
NovaPhone
@endcomponent
