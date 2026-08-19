<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreReturnRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        $isCod = $this->route('order')?->payment_method === 'cod';

        return [
            'reason' => ['required', 'string', 'max:255'],
            'note' => ['required', 'string', 'min:10', 'max:2000'],
            'items' => ['required', 'array', 'min:1'],
            'items.*' => ['nullable', 'integer', 'min:0'],
            'evidence' => ['required', 'array', 'min:1', 'max:5'],
            'evidence.*' => ['required', 'file', 'mimes:jpg,jpeg,png,webp,mp4,mov,avi,webm', 'max:20480'],
            'refund_bank_name' => [Rule::requiredIf($isCod), 'nullable', 'string', 'max:100'],
            'refund_bank_account' => [Rule::requiredIf($isCod), 'nullable', 'string', 'regex:/^[0-9]{6,20}$/'],
            'refund_account_name' => [Rule::requiredIf($isCod), 'nullable', 'string', 'max:100', 'regex:/^[\pL\s]+$/u'],
        ];
    }

    public function messages(): array
    {
        return [
            'note.required' => 'Vui lòng mô tả tình trạng sản phẩm.',
            'note.min' => 'Ghi chú cần có ít nhất 10 ký tự.',
            'items.required' => 'Vui lòng chọn ít nhất một sản phẩm cần hoàn.',
            'evidence.required' => 'Vui lòng tải lên ít nhất một ảnh hoặc video làm bằng chứng.',
            'evidence.max' => 'Chỉ được tải tối đa 5 tệp bằng chứng.',
            'evidence.*.mimes' => 'Bằng chứng phải là ảnh JPG, PNG, WEBP hoặc video MP4, MOV, AVI, WEBM.',
            'evidence.*.max' => 'Mỗi ảnh hoặc video không được vượt quá 20 MB.',
            'refund_bank_name.required' => 'Vui lòng chọn ngân hàng nhận tiền hoàn.',
            'refund_bank_account.required' => 'Vui lòng nhập số tài khoản nhận tiền hoàn.',
            'refund_bank_account.regex' => 'Số tài khoản phải gồm 6 đến 20 chữ số.',
            'refund_account_name.required' => 'Vui lòng nhập tên chủ tài khoản.',
        ];
    }
}
