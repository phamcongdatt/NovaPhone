<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class OrdrerRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Đã được bảo vệ bởi middleware ['auth', 'admin']
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => ['required', 'in:pending,confirmed,processing,shipping,delivered,received,cancelled'],
            'note'   => ['nullable', 'string', 'max:1000'],
            'delivery_proof_image' => ['nullable', 'image', 'max:5120', 'mimes:jpeg,png,jpg,webp'],
        ];
    }

    public function messages(): array
    {
        return [
            'status.required' => 'Vui lòng chọn trạng thái đơn hàng.',
            'status.in'       => 'Trạng thái đơn hàng không hợp lệ.',
            'note.max'        => 'Ghi chú không được vượt quá 1000 ký tự.',
        ];
    }
}
