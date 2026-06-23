<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone'    => ['nullable', 'string', 'regex:/^0\d{9}$/', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'terms'    => ['sometimes', 'accepted'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'          => 'Vui lòng nhập họ tên.',
            'email.required'         => 'Vui lòng nhập email.',
            'email.email'            => 'Email không hợp lệ.',
            'email.unique'           => 'Email này đã được sử dụng.',
            'phone.required'         => 'Vui lòng nhập số điện thoại.',
            'phone.regex'            => 'Số điện thoại không hợp lệ (10 số, bắt đầu bằng 0).',
            'phone.unique'           => 'Số điện thoại này đã được sử dụng.',
            'password.required'      => 'Vui lòng nhập mật khẩu.',
            'password.min'           => 'Mật khẩu phải có ít nhất 8 ký tự.',
            'password.confirmed'     => 'Xác nhận mật khẩu không khớp.',
            'terms.accepted'         => 'Bạn cần đồng ý với điều khoản dịch vụ.',
        ];
    }
}
