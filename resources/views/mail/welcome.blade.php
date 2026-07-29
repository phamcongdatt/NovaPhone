@component('mail::message')
# Chào mừng {{ $user->name }}!

Cảm ơn bạn đã đăng ký tài khoản tại **NovaPhone**.

Bạn có thể bắt đầu khám phá sản phẩm và quản lý đơn hàng của mình ngay bây giờ.

@component('mail::button', ['url' => url('/')])
Khám phá NovaPhone
@endcomponent

Trân trọng,<br>
Đội ngũ NovaPhone
@endcomponent
