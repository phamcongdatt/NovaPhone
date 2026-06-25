<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfileUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20', 'unique:users,phone,' . $this->user()->id],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ];
    }
    
    public function messages(): array
    {
        return [
            'name.required' => 'Vui lòng nhập họ tên.',
            'name.max' => 'Họ tên không được vượt quá 255 ký tự.',
            'phone.unique' => 'Số điện thoại này đã được sử dụng.',
            'phone.max' => 'Số điện thoại không được vượt quá 20 ký tự.',
            'avatar.image' => 'Ảnh đại diện phải là một hình ảnh.',
            'avatar.mimes' => 'Ảnh đại diện phải có định dạng jpeg, png, jpg, hoặc gif.',
            'avatar.max' => 'Kích thước ảnh không được vượt quá 2MB.',
        ];
    }
}
