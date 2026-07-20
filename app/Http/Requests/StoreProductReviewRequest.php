<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'rating' => ['required', 'integer', 'between:1,5'],
            'comment' => ['nullable', 'string', 'max:5000'],
            'images' => ['nullable', 'array', 'max:5'],
            'images.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'images.max' => 'Bạn chỉ có thể tải lên tối đa 5 ảnh.',
            'images.*.image' => 'Tệp tải lên phải là hình ảnh.',
            'images.*.mimes' => 'Ảnh đánh giá phải có định dạng JPG, JPEG, PNG hoặc WEBP.',
            'images.*.max' => 'Mỗi ảnh đánh giá không được vượt quá 2 MB.',
        ];
    }
}
