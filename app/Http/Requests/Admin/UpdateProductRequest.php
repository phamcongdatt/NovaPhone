<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $productId = $this->route('product')->id;

        return [
            'name'         => ['required', 'string', 'max:255'],
            'slug'         => ['nullable', 'string', 'max:255', Rule::unique('products', 'slug')->ignore($productId)],
            'category_id'  => ['required', 'exists:categories,id'],
            'brand_id'     => ['required', 'exists:brands,id'],
            'description'  => ['nullable', 'string'],
            'content'      => ['nullable', 'string'],
            'price'        => ['required', 'numeric', 'min:0'],
            'sale_price'   => ['nullable', 'numeric', 'min:0', 'lt:price'],
            'sku'          => ['required', 'string', 'max:100', Rule::unique('products', 'sku')->ignore($productId)],
            'thumbnail'    => ['nullable', 'image', 'max:2048'],
            'is_active'    => ['boolean'],
            'is_featured'  => ['boolean'],

            // Biến thể
            'variants'                    => ['nullable', 'array'],
            'variants.*.id'               => ['nullable', 'integer', 'exists:product_variants,id'],
            'variants.*.name'             => ['required_with:variants', 'string', 'max:255'],
            'variants.*.storage'          => ['nullable', 'string', 'max:50'],
            'variants.*.color'            => ['nullable', 'string', 'max:50'],
            'variants.*.color_code'       => ['nullable', 'string', 'max:20'],
            'variants.*.additional_price' => ['nullable', 'numeric', 'min:0'],
            'variants.*.sku'              => ['nullable', 'string', 'max:100'],
            'variants.*.quantity'         => ['nullable', 'integer', 'min:0'],

            // Xoá biến thể (mảng id)
            'deleted_variants'   => ['nullable', 'array'],
            'deleted_variants.*' => ['integer', 'exists:product_variants,id'],

            // Hình ảnh thư viện
            'images'   => ['nullable', 'array'],
            'images.*' => ['image', 'max:2048'],

            // Xoá ảnh
            'deleted_images'   => ['nullable', 'array'],
            'deleted_images.*' => ['integer', 'exists:product_images,id'],
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
        ];
    }
}
