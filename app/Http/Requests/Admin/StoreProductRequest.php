<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'         => ['required', 'string', 'max:255'],
            'slug'         => ['nullable', 'string', 'max:255', 'unique:products,slug'],
            'category_id'  => ['required', 'exists:categories,id'],
            'brand_id'     => ['required', 'exists:brands,id'],
            'description'  => ['nullable', 'string'],
            'content'      => ['nullable', 'string'],

            // Thông số hiệu năng điện thoại
            'chipset'             => ['nullable', 'string', 'max:128'],
            'cpu_cores'           => ['nullable', 'string', 'max:255'],
            'gpu'                 => ['nullable', 'string', 'max:128'],
            'antutu_score'        => ['nullable', 'numeric', 'min:0', 'max:9999999.99'],
            'geekbench_single'    => ['nullable', 'numeric', 'min:0', 'max:9999999.99'],
            'geekbench_multi'     => ['nullable', 'numeric', 'min:0', 'max:9999999.99'],
            'display_size_inch'   => ['nullable', 'numeric', 'min:1', 'max:15'],
            'display_type'        => ['nullable', 'string', 'max:128'],
            'refresh_rate'        => ['nullable', 'string', 'max:64'],
            'main_camera_mp'      => ['nullable', 'string', 'max:64'],
            'ultra_wide_camera_mp'=> ['nullable', 'string', 'max:64'],
            'front_camera_mp'     => ['nullable', 'string', 'max:64'],
            'video_recording'     => ['nullable', 'string', 'max:128'],
            'battery_mah'         => ['nullable', 'integer', 'min:1', 'max:99999'],
            'charging_speed_w'    => ['nullable', 'integer', 'min:1', 'max:999'],
            'ram'                 => ['nullable', 'string', 'max:64'],
            'os'                  => ['nullable', 'string', 'max:128'],
            'network_support'     => ['nullable', 'string', 'max:255'],

            'price'        => ['required', 'numeric', 'min:0'],
            'sale_price'   => ['nullable', 'numeric', 'min:0', 'lt:price'],
            'sku'          => ['required', 'string', 'max:100', 'unique:products,sku'],
            'thumbnail'    => ['nullable', 'image', 'max:2048'],
            'is_active'    => ['boolean'],
            'is_featured'  => ['boolean'],

            // Biến thể
            'variants'                    => ['nullable', 'array'],
            'variants.*.name'             => ['required_with:variants', 'string', 'max:255'],
            'variants.*.storage'          => ['nullable', 'string', 'max:50'],
            'variants.*.color'            => ['nullable', 'string', 'max:50'],
            'variants.*.color_code'       => ['nullable', 'string', 'max:20'],
            'variants.*.additional_price' => ['nullable', 'numeric', 'min:0'],
            'variants.*.sku'              => ['nullable', 'string', 'max:100'],
            'variants.*.quantity'         => ['nullable', 'integer', 'min:0'],

            // Hình ảnh thư viện
            'images'   => ['nullable', 'array'],
            'images.*' => ['image', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'        => 'Vui lòng nhập tên sản phẩm.',
            'category_id.required' => 'Vui lòng chọn danh mục.',
            'category_id.exists'   => 'Danh mục không hợp lệ.',
            'brand_id.required'    => 'Vui lòng chọn thương hiệu.',
            'brand_id.exists'      => 'Thương hiệu không hợp lệ.',
            'price.required'       => 'Vui lòng nhập giá bán.',
            'price.numeric'        => 'Giá bán phải là số.',
            'sale_price.lt'        => 'Giá giảm phải nhỏ hơn giá bán.',
            'sku.required'         => 'Vui lòng nhập mã SKU.',
            'sku.unique'           => 'Mã SKU đã tồn tại.',
            'slug.unique'          => 'Slug đã tồn tại.',
            'thumbnail.image'      => 'Ảnh đại diện phải là hình ảnh.',
            'thumbnail.max'        => 'Ảnh đại diện tối đa 2MB.',

            // Thông số hiệu năng
            'chipset.string'                  => 'Tên chip phải là văn bản không quá 128 ký tự.',
            'cpu_cores.string'                => 'Mô tả CPU phải là văn bản không quá 255 ký tự.',
            'gpu.string'                      => 'Tên GPU phải là văn bản không quá 128 ký tự.',
            'antutu_score.numeric'            => 'Điểm Antutu phải là số.',
            'antutu_score.min'                => 'Điểm Antutu phải lớn hơn hoặc bằng 0.',
            'antutu_score.max'                => 'Điểm Antutu không hợp lệ (tối đa 9.999.999,99).',
            'geekbench_single.numeric'        => 'Điểm Geekbench Single-Core phải là số.',
            'geekbench_single.min'            => 'Điểm Geekbench Single-Core phải lớn hơn hoặc bằng 0.',
            'geekbench_single.max'            => 'Điểm Geekbench Single-Core không hợp lệ.',
            'geekbench_multi.numeric'         => 'Điểm Geekbench Multi-Core phải là số.',
            'geekbench_multi.min'             => 'Điểm Geekbench Multi-Core phải lớn hơn hoặc bằng 0.',
            'geekbench_multi.max'             => 'Điểm Geekbench Multi-Core không hợp lệ.',
            'display_size_inch.numeric'       => 'Kích thước màn hình phải là số.',
            'display_size_inch.min'           => 'Kích thước màn hình phải lớn hơn 1 inch.',
            'display_size_inch.max'           => 'Kích thước màn hình không hợp lệ (tối đa 15 inch).',
            'display_type.string'             => 'Loại màn hình không được vượt quá 128 ký tự.',
            'refresh_rate.string'             => 'Tần số quét không được vượt quá 64 ký tự.',
            'main_camera_mp.string'           => 'Camera chính không được vượt quá 64 ký tự.',
            'ultra_wide_camera_mp.string'     => 'Camera siêu rộng không được vượt quá 64 ký tự.',
            'front_camera_mp.string'          => 'Camera trước không được vượt quá 64 ký tự.',
            'video_recording.string'          => 'Quay video không được vượt quá 128 ký tự.',
            'battery_mah.integer'             => 'Dung lượng pin phải là số nguyên.',
            'battery_mah.min'                 => 'Dung lượng pin phải lớn hơn 0 mAh.',
            'battery_mah.max'                 => 'Dung lượng pin không hợp lệ.',
            'charging_speed_w.integer'        => 'Công suất sạc nhanh phải là số nguyên.',
            'charging_speed_w.min'            => 'Công suất sạc nhanh phải lớn hơn 0W.',
            'charging_speed_w.max'            => 'Công suất sạc nhanh không hợp lệ.',
            'ram.string'                      => 'RAM không được vượt quá 64 ký tự.',
            'os.string'                       => 'Hệ điều hành không được vượt quá 128 ký tự.',
            'network_support.string'          => 'Kết nối mạng không được vượt quá 255 ký tự.',
        ];
    }
}
