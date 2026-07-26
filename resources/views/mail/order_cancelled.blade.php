@component('mail::message')
# Đơn hàng đã bị hủy

Mã đơn hàng **{{ $order->order_number ?? $order->id ?? '' }}** đã được hủy thành công.

Trân trọng,<br>
NovaPhone
@endcomponent
