@component('mail::message')
# Đơn hàng đã bị hủy

Mã đơn hàng **{{ $order->order_code }}** đã bị hủy thành công.

Lý do: {{ $reason }}

@component('mail::button', ['url' => $orderUrl])
Xem đơn hàng
@endcomponent

Trân trọng,<br>
NovaPhone
@endcomponent
