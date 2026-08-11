<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer_email' => ['required', 'email', 'max:255'],
            'shipping_full_name' => ['required', 'string', 'min:2', 'max:100'],
            'shipping_phone' => ['required', 'regex:/^(?:\\+84|0)(?:3|5|7|8|9)\\d{8}$/'],
            'province_code' => ['required', 'regex:/^\\d{2}$/'],
            'ward_code' => ['required', 'regex:/^\\d{5}$/'],
            'shipping_address' => ['required', 'string', 'min:8', 'max:255'],
            'payment_method' => ['required', 'in:cod,vnpay'],
            'note' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'customer_email.required' => 'Email nhận thông báo là bắt buộc.',
            'customer_email.email' => 'Email nhận thông báo không hợp lệ.',
            'shipping_full_name.required' => 'Họ và tên người nhận là bắt buộc.',
            'shipping_phone.required' => 'Số điện thoại là bắt buộc.',
            'shipping_phone.regex' => 'Số điện thoại Việt Nam không hợp lệ.',
            'province_code.required' => 'Vui lòng chọn tỉnh/thành phố.',
            'province_code.regex' => 'Mã tỉnh/thành phố không hợp lệ.',
            'ward_code.required' => 'Vui lòng chọn phường/xã.',
            'ward_code.regex' => 'Mã phường/xã không hợp lệ.',
            'shipping_address.required' => 'Địa chỉ giao hàng là bắt buộc.',
            'shipping_address.min' => 'Vui lòng nhập số nhà, tên đường hoặc thông tin địa chỉ chi tiết.',
            'payment_method.required' => 'Vui lòng chọn phương thức thanh toán.',
            'payment_method.in' => 'Phương thức thanh toán không hợp lệ.',
        ];
    }
}
