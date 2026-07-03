@extends('admin.layout')

@section('title', 'Sửa danh mục')
@section('page-title', 'Sửa danh mục')

@section('content')

<a href="{{ route('admin.categories.index') }}" class="mb-4 inline-flex items-center text-sm font-semibold text-brand-400 transition-colors hover:text-brand-300">
    <svg class="mr-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
    </svg>
    Quay lại danh sách
</a>

<div class="mb-6 rounded-2xl border border-white/5 bg-night-soft p-6">
    <form method="POST" action="{{ route('admin.categories.update', $category) }}" class="space-y-4">
        @csrf
        @method('PUT')

        {{-- Name --}}
        <div>
            <label for="name" class="block text-sm font-semibold text-gray-300 mb-2">Tên danh mục <span class="text-red-400">*</span></label>
            <input type="text" id="name" name="name" value="{{ old('name', $category->name) }}"
                   class="w-full rounded-xl border {{ $errors->has('name') ? 'border-red-500/50' : 'border-white/10' }} bg-white/5 px-4 py-2.5 text-white placeholder:text-gray-500 outline-none transition focus:border-brand-500/50 focus:ring-2 focus:ring-brand-500/25"
                   placeholder="Ví dụ: Điện thoại, Tablet...">
            @error('name')
                <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
            @enderror
        </div>

        {{-- Description --}}
        <div>
            <label for="description" class="block text-sm font-semibold text-gray-300 mb-2">Mô tả</label>
            <textarea id="description" name="description" rows="4"
                      class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-2.5 text-white placeholder:text-gray-500 outline-none transition focus:border-brand-500/50 focus:ring-2 focus:ring-brand-500/25"
                      placeholder="Mô tả chi tiết về danh mục...">{{ old('description', $category->description) }}</textarea>
            @error('description')
                <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
            @enderror
        </div>

        {{-- Status --}}
        <div>
            <label for="is_active" class="block text-sm font-semibold text-gray-300 mb-2">Trạng thái <span class="text-red-400">*</span></label>
            <select id="is_active" name="is_active"
                    class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-2.5 text-white outline-none transition focus:border-brand-500/50 focus:ring-2 focus:ring-brand-500/25">
                <option value="">-- Chọn trạng thái --</option>
                <option value="1" @selected(old('is_active', $category->is_active) == '1')>Active</option>
                <option value="0" @selected(old('is_active', $category->is_active) == '0')>Inactive</option>
            </select>
            @error('is_active')
                <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
            @enderror
        </div>

        {{-- Buttons --}}
        <div class="mt-6 flex gap-2 pt-4 border-t border-white/5">
            <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-brand-600 px-5 py-2.5 text-sm font-semibold text-white shadow-lg shadow-brand-600/25 transition hover:bg-brand-500">
                <svg class="size-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                Cập nhật danh mục
            </button>
            <a href="{{ route('admin.categories.index') }}" class="rounded-xl border border-white/10 px-5 py-2.5 text-sm font-semibold text-gray-300 transition hover:bg-white/5">
                Huỷ
            </a>
        </div>
    </form>
</div>

@endsection
