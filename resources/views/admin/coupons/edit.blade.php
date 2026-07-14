@extends('admin.layout')

@section('title', 'Cập nhật mã giảm giá')
@section('page-title', 'Cập nhật mã giảm giá: ' . $coupon->code)

@section('content')
<div class="mx-auto max-w-3xl rounded-2xl border border-white/5 bg-night-soft p-6">
    <form method="POST" action="{{ route('admin.coupons.update', $coupon) }}" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="grid gap-6 md:grid-cols-2">
            <div>
                <label class="mb-2 block text-sm font-semibold text-gray-300">Mã giảm giá (Code) <span class="text-red-400">*</span></label>
                <input type="text" name="code" value="{{ old('code', $coupon->code) }}" required
                    class="w-full uppercase rounded-xl border {{ $errors->has('code') ? 'border-red-500/50' : 'border-white/10' }} bg-white/5 px-4 py-2.5 text-white placeholder:text-gray-500 outline-none transition focus:border-brand-500/50 focus:ring-2 focus:ring-brand-500/25">
                @error('code')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="mb-2 block text-sm font-semibold text-gray-300">Trạng thái</label>
                <div class="flex items-center h-[46px]">
                    <label class="relative inline-flex cursor-pointer items-center">
                        <input type="checkbox" name="is_active" value="1" class="peer sr-only" {{ old('is_active', $coupon->is_active) ? 'checked' : '' }}>
                        <div class="peer h-6 w-11 rounded-full bg-gray-600 after:absolute after:left-[2px] after:top-[2px] after:h-5 after:w-5 after:rounded-full after:border after:border-gray-300 after:bg-white after:transition-all after:content-[''] peer-checked:bg-brand-500 peer-checked:after:translate-x-full peer-checked:after:border-white peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-brand-500/50"></div>
                        <span class="ml-3 text-sm font-medium text-gray-300">Hoạt động</span>
                    </label>
                </div>
            </div>

            <div class="md:col-span-2">
                <label class="mb-2 block text-sm font-semibold text-gray-300">Mô tả</label>
                <input type="text" name="description" value="{{ old('description', $coupon->description) }}"
                    class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-2.5 text-white placeholder:text-gray-500 outline-none transition focus:border-brand-500/50 focus:ring-2 focus:ring-brand-500/25">
                @error('description')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="mb-2 block text-sm font-semibold text-gray-300">Loại giảm giá <span class="text-red-400">*</span></label>
                <select name="type" required
                    class="w-full rounded-xl border {{ $errors->has('type') ? 'border-red-500/50' : 'border-white/10' }} bg-white/5 px-4 py-2.5 text-white outline-none transition focus:border-brand-500/50 focus:ring-2 focus:ring-brand-500/25">
                    <option value="fixed" {{ old('type', $coupon->type) === 'fixed' ? 'selected' : '' }}>Giảm tiền (VND)</option>
                    <option value="percent" {{ old('type', $coupon->type) === 'percent' ? 'selected' : '' }}>Giảm phần trăm (%)</option>
                </select>
            </div>

            <div>
                <label class="mb-2 block text-sm font-semibold text-gray-300">Giá trị giảm <span class="text-red-400">*</span></label>
                <input type="number" step="0.01" name="value" value="{{ old('value', (float)$coupon->value) }}" required min="0"
                    class="w-full rounded-xl border {{ $errors->has('value') ? 'border-red-500/50' : 'border-white/10' }} bg-white/5 px-4 py-2.5 text-white placeholder:text-gray-500 outline-none transition focus:border-brand-500/50 focus:ring-2 focus:ring-brand-500/25">
                @error('value')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="mb-2 block text-sm font-semibold text-gray-300">Mức giảm tối đa (nếu giảm theo %)</label>
                <input type="number" step="0.01" name="max_discount" value="{{ old('max_discount', (float)$coupon->max_discount) }}" min="0"
                    class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-2.5 text-white placeholder:text-gray-500 outline-none transition focus:border-brand-500/50 focus:ring-2 focus:ring-brand-500/25">
            </div>

            <div>
                <label class="mb-2 block text-sm font-semibold text-gray-300">Đơn hàng tối thiểu</label>
                <input type="number" step="0.01" name="min_order_amount" value="{{ old('min_order_amount', (float)$coupon->min_order_amount) }}" min="0"
                    class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-2.5 text-white placeholder:text-gray-500 outline-none transition focus:border-brand-500/50 focus:ring-2 focus:ring-brand-500/25">
            </div>

            <div>
                <label class="mb-2 block text-sm font-semibold text-gray-300">Giới hạn số lần dùng (Tổng)</label>
                <input type="number" name="usage_limit" value="{{ old('usage_limit', $coupon->usage_limit) }}" min="1"
                    class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-2.5 text-white placeholder:text-gray-500 outline-none transition focus:border-brand-500/50 focus:ring-2 focus:ring-brand-500/25"
                    placeholder="Để trống nếu không giới hạn">
            </div>

            <div>
                <label class="mb-2 block text-sm font-semibold text-gray-300">Giới hạn số lần dùng (Mỗi KH)</label>
                <input type="number" name="per_user_limit" value="{{ old('per_user_limit', $coupon->per_user_limit) }}" min="1"
                    class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-2.5 text-white placeholder:text-gray-500 outline-none transition focus:border-brand-500/50 focus:ring-2 focus:ring-brand-500/25">
            </div>

            <div>
                <label class="mb-2 block text-sm font-semibold text-gray-300">Thời gian bắt đầu</label>
                <input type="datetime-local" name="starts_at" value="{{ old('starts_at', $coupon->starts_at ? $coupon->starts_at->format('Y-m-d\TH:i') : '') }}"
                    class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-2.5 text-white outline-none transition focus:border-brand-500/50 focus:ring-2 focus:ring-brand-500/25"
                    style="color-scheme: dark;">
            </div>

            <div>
                <label class="mb-2 block text-sm font-semibold text-gray-300">Thời gian kết thúc</label>
                <input type="datetime-local" name="expires_at" value="{{ old('expires_at', $coupon->expires_at ? $coupon->expires_at->format('Y-m-d\TH:i') : '') }}"
                    class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-2.5 text-white outline-none transition focus:border-brand-500/50 focus:ring-2 focus:ring-brand-500/25"
                    style="color-scheme: dark;">
                @error('expires_at')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
            </div>
            
            <div class="md:col-span-2">
                <p class="text-sm text-gray-400">Mã giảm giá này đã được sử dụng <strong>{{ $coupon->used_count }}</strong> lần.</p>
            </div>
        </div>

        <div class="flex items-center gap-3 border-t border-white/5 pt-6">
            <button type="submit" class="rounded-xl bg-brand-600 px-6 py-2.5 text-sm font-semibold text-white shadow-lg shadow-brand-600/25 transition-all duration-200 hover:-translate-y-0.5 hover:bg-brand-500">
                Lưu thay đổi
            </button>
            <a href="{{ route('admin.coupons.index') }}" class="rounded-xl border border-white/10 px-6 py-2.5 text-sm font-semibold text-gray-300 transition-colors hover:bg-white/5">
                Huỷ bỏ
            </a>
        </div>
    </form>
</div>
@endsection
