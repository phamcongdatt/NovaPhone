@extends('admin.layout')

@section('title', 'Thêm mã giảm giá')
@section('page-title', 'Thêm mã giảm giá mới')

@section('content')
<div class="mx-auto max-w-4xl rounded-2xl border border-white/5 bg-night-soft p-6">
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
    <style>
        .ts-control { background-color: rgba(255,255,255,0.05) !important; border: 1px solid rgba(255,255,255,0.1) !important; color: white !important; border-radius: 0.75rem !important; padding: 0.625rem 1rem !important; }
        .ts-control > input { color: white !important; }
        .ts-dropdown { background-color: #1e293b !important; border: 1px solid rgba(255,255,255,0.1) !important; color: white !important; border-radius: 0.75rem !important; margin-top: 0.25rem !important; }
        .ts-dropdown .option { padding: 0.5rem 1rem !important; }
        .ts-dropdown .option:hover, .ts-dropdown .active { background-color: rgba(255,255,255,0.1) !important; color: white !important; }
        .ts-wrapper.multi .ts-control > div { background-color: rgba(255,255,255,0.1) !important; color: white !important; border-radius: 0.375rem !important; border: none !important; }
    </style>
    <form method="POST" action="{{ route('admin.coupons.store') }}" class="space-y-8">
        @csrf

        {{-- Phần 1: Thông tin cơ bản & Điều kiện --}}
        <div>
            <h3 class="mb-4 text-lg font-bold text-white border-b border-white/10 pb-2">1. Điều Kiện (Conditions)</h3>
            <div class="grid gap-6 md:grid-cols-2">
                <div>
                    <label class="mb-2 block text-sm font-semibold text-gray-300">Mã giảm giá (Code) <span class="text-red-400">*</span></label>
                    <input type="text" name="code" value="{{ old('code') }}" required
                        class="w-full uppercase rounded-xl border {{ $errors->has('code') ? 'border-red-500/50' : 'border-white/10' }} bg-white/5 px-4 py-2.5 text-white placeholder:text-gray-500 outline-none transition focus:border-brand-500/50 focus:ring-2 focus:ring-brand-500/25"
                        placeholder="VD: NEW2026">
                    @error('code')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="mb-2 block text-sm font-semibold text-gray-300">Trạng thái & Cấu hình</label>
                    <div class="space-y-3">
                        <label class="relative inline-flex cursor-pointer items-center">
                            <input type="checkbox" name="is_active" value="1" class="peer sr-only" {{ old('is_active', true) ? 'checked' : '' }}>
                            <div class="h-6 w-11 rounded-full bg-gray-600 after:absolute after:left-[2px] after:top-[2px] after:h-5 after:w-5 after:rounded-full after:border after:border-gray-300 after:bg-white after:transition-all after:content-[''] peer-checked:bg-brand-500 peer-checked:after:translate-x-full peer-checked:after:border-white peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-brand-500/50"></div>
                            <span class="ml-3 text-sm font-medium text-gray-300 select-none">Hoạt động</span>
                        </label>
                        <br>
                        <label class="relative inline-flex cursor-pointer items-center">
                            <input type="checkbox" name="is_apply_sale" value="1" class="peer sr-only" {{ old('is_apply_sale', true) ? 'checked' : '' }}>
                            <div class="h-6 w-11 rounded-full bg-gray-600 after:absolute after:left-[2px] after:top-[2px] after:h-5 after:w-5 after:rounded-full after:border after:border-gray-300 after:bg-white after:transition-all after:content-[''] peer-checked:bg-brand-500 peer-checked:after:translate-x-full peer-checked:after:border-white peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-brand-500/50"></div>
                            <span class="ml-3 text-sm font-medium text-gray-300 select-none">Áp dụng chung với hàng Sale</span>
                        </label>
                        <br>
                        <label class="relative inline-flex cursor-pointer items-center">
                            <input type="checkbox" name="is_apply_flash_sale" value="1" class="peer sr-only" {{ old('is_apply_flash_sale', false) ? 'checked' : '' }}>
                            <div class="h-6 w-11 rounded-full bg-gray-600 after:absolute after:left-[2px] after:top-[2px] after:h-5 after:w-5 after:rounded-full after:border after:border-gray-300 after:bg-white after:transition-all after:content-[''] peer-checked:bg-brand-500 peer-checked:after:translate-x-full peer-checked:after:border-white peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-brand-500/50"></div>
                            <span class="ml-3 text-sm font-medium text-gray-300 select-none">Áp dụng chung với hàng Flash Sale</span>
                        </label>
                        <br>
                        <label class="relative inline-flex cursor-pointer items-center">
                            <input type="checkbox" name="is_stackable" value="1" class="peer sr-only" {{ old('is_stackable', false) ? 'checked' : '' }}>
                            <div class="h-6 w-11 rounded-full bg-gray-600 after:absolute after:left-[2px] after:top-[2px] after:h-5 after:w-5 after:rounded-full after:border after:border-gray-300 after:bg-white after:transition-all after:content-[''] peer-checked:bg-brand-500 peer-checked:after:translate-x-full peer-checked:after:border-white peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-brand-500/50"></div>
                            <span class="ml-3 text-sm font-medium text-gray-300 select-none">Có thể cộng dồn nhiều mã (Stackable)</span>
                        </label>
                    </div>
                </div>

                <div class="md:col-span-2">
                    <label class="mb-2 block text-sm font-semibold text-gray-300">Mô tả hiển thị</label>
                    <input type="text" name="description" value="{{ old('description') }}"
                        class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-2.5 text-white placeholder:text-gray-500 outline-none transition focus:border-brand-500/50 focus:ring-2 focus:ring-brand-500/25"
                        placeholder="Giảm 20% cho khách hàng mới">
                    @error('description')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="mb-2 block text-sm font-semibold text-gray-300">Đơn hàng tối thiểu (VND)</label>
                    <input type="number" step="0.01" name="min_order_amount" value="{{ old('min_order_amount', 0) }}" min="0"
                        class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-2.5 text-white placeholder:text-gray-500 outline-none transition focus:border-brand-500/50 focus:ring-2 focus:ring-brand-500/25">
                </div>

                <div>
                    <label class="mb-2 block text-sm font-semibold text-gray-300">Danh mục áp dụng</label>
                    <select name="categories[]" multiple class="tom-select w-full" placeholder="Chọn danh mục hoặc để trống áp dụng tất cả">
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ is_array(old('categories')) && in_array($category->id, old('categories')) ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="mb-2 block text-sm font-semibold text-gray-300">Sản phẩm áp dụng</label>
                    <select name="products[]" multiple class="tom-select w-full" placeholder="Chọn sản phẩm hoặc để trống áp dụng tất cả">
                        @foreach($products as $product)
                            <option value="{{ $product->id }}" {{ is_array(old('products')) && in_array($product->id, old('products')) ? 'selected' : '' }}>{{ $product->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label class="mb-2 block text-sm font-semibold text-gray-300">Khách hàng được phép dùng</label>
                    <select name="users[]" multiple class="tom-select w-full" placeholder="Chọn khách hàng hoặc để trống áp dụng tất cả">
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ is_array(old('users')) && in_array($user->id, old('users')) ? 'selected' : '' }}>{{ $user->name }} ({{ $user->email }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="mb-2 block text-sm font-semibold text-gray-300">Thời gian bắt đầu</label>
                    <input type="datetime-local" name="starts_at" value="{{ old('starts_at') }}"
                        class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-2.5 text-white outline-none transition focus:border-brand-500/50 focus:ring-2 focus:ring-brand-500/25"
                        style="color-scheme: dark;">
                </div>

                <div>
                    <label class="mb-2 block text-sm font-semibold text-gray-300">Thời gian kết thúc</label>
                    <input type="datetime-local" name="expires_at" value="{{ old('expires_at') }}"
                        class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-2.5 text-white outline-none transition focus:border-brand-500/50 focus:ring-2 focus:ring-brand-500/25"
                        style="color-scheme: dark;">
                    @error('expires_at')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="mb-2 block text-sm font-semibold text-gray-300">Giới hạn số lần dùng (Tổng số mã)</label>
                    <input type="number" name="usage_limit" value="{{ old('usage_limit') }}" min="1"
                        class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-2.5 text-white placeholder:text-gray-500 outline-none transition focus:border-brand-500/50 focus:ring-2 focus:ring-brand-500/25"
                        placeholder="Để trống nếu không giới hạn">
                </div>

                <div>
                    <label class="mb-2 block text-sm font-semibold text-gray-300">Giới hạn số lần dùng (Mỗi khách hàng)</label>
                    <input type="number" name="per_user_limit" value="{{ old('per_user_limit', 1) }}" min="1"
                        class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-2.5 text-white placeholder:text-gray-500 outline-none transition focus:border-brand-500/50 focus:ring-2 focus:ring-brand-500/25">
                </div>
            </div>
        </div>

        {{-- Phần 2: Hành động --}}
        <div>
            <h3 class="mb-4 text-lg font-bold text-white border-b border-white/10 pb-2">2. Hành Động (Actions)</h3>
            <div class="grid gap-6 md:grid-cols-2">
                <div>
                    <label class="mb-2 block text-sm font-semibold text-gray-300">Loại khuyến mãi <span class="text-red-400">*</span></label>
                    <select name="type" id="coupon-type" required
                        class="w-full rounded-xl border {{ $errors->has('type') ? 'border-red-500/50' : 'border-white/10' }} bg-white/5 px-4 py-2.5 text-white outline-none transition focus:border-brand-500/50 focus:ring-2 focus:ring-brand-500/25">
                        <option value="fixed" {{ old('type') === 'fixed' ? 'selected' : '' }}>Giảm tiền (VND)</option>
                        <option value="percent" {{ old('type') === 'percent' ? 'selected' : '' }}>Giảm phần trăm (%)</option>
                    </select>
                </div>

                <div id="field-value">
                    <label class="mb-2 block text-sm font-semibold text-gray-300" id="label-value">Giá trị giảm <span class="text-red-400">*</span></label>
                    <input type="number" step="0.01" name="value" id="input-value" value="{{ old('value', 0) }}" required min="0"
                        class="w-full rounded-xl border {{ $errors->has('value') ? 'border-red-500/50' : 'border-white/10' }} bg-white/5 px-4 py-2.5 text-white placeholder:text-gray-500 outline-none transition focus:border-brand-500/50 focus:ring-2 focus:ring-brand-500/25">
                    @error('value')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
                </div>

                <div id="field-max-discount">
                    <label class="mb-2 block text-sm font-semibold text-gray-300">Mức giảm tối đa (VND)</label>
                    <input type="number" step="0.01" name="max_discount" value="{{ old('max_discount') }}" min="0"
                        class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-2.5 text-white placeholder:text-gray-500 outline-none transition focus:border-brand-500/50 focus:ring-2 focus:ring-brand-500/25"
                        placeholder="Chỉ áp dụng khi giảm %">
                </div>

            </div>
        </div>

        <div class="flex items-center gap-3 border-t border-white/5 pt-6">
            <button type="submit" class="rounded-xl bg-brand-600 px-6 py-2.5 text-sm font-semibold text-white shadow-lg shadow-brand-600/25 transition-all duration-200 hover:-translate-y-0.5 hover:bg-brand-500">
                Lưu mã giảm giá
            </button>
            <a href="{{ route('admin.coupons.index') }}" class="rounded-xl border border-white/10 px-6 py-2.5 text-sm font-semibold text-gray-300 transition-colors hover:bg-white/5">
                Huỷ bỏ
            </a>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.tom-select').forEach(el => new TomSelect(el, { plugins: ['remove_button'] }));

        const typeSelect = document.getElementById('coupon-type');
        const fieldValue = document.getElementById('field-value');
        const labelValue = document.getElementById('label-value');
        const inputValue = document.getElementById('input-value');
        const fieldMaxDiscount = document.getElementById('field-max-discount');

        function toggleFields() {
            const type = typeSelect.value;
            
            if (type === 'percent') {
                labelValue.innerHTML = 'Phần trăm giảm (%) <span class="text-red-400">*</span>';
                inputValue.setAttribute('max', '100');
                fieldMaxDiscount.classList.remove('hidden');
            } else {
                labelValue.innerHTML = 'Số tiền giảm (VND) <span class="text-red-400">*</span>';
                inputValue.removeAttribute('max');
                fieldMaxDiscount.classList.add('hidden');
            }
        }

        typeSelect.addEventListener('change', toggleFields);
        toggleFields(); // Initial call
    });
</script>
@endpush
