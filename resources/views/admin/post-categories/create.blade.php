@extends('admin.layout')
@section('page-title', 'Thêm danh mục bài viết')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <a href="{{ route('admin.post-categories.index') }}" class="flex items-center gap-2 text-sm font-medium text-slate-400 transition-colors hover:text-white">
        <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/></svg>
        Quay lại
    </a>
</div>

<div class="mx-auto max-w-2xl rounded-2xl border border-white/10 bg-[#0b1523] p-6 shadow-xl">
    <form action="{{ route('admin.post-categories.store') }}" method="POST">
        @csrf
        <div class="space-y-6">
            <div>
                <label for="name" class="mb-2 block text-sm font-medium text-slate-300">Tên danh mục <span class="text-red-500">*</span></label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required
                       class="w-full rounded-xl border border-white/10 bg-black/20 px-4 py-2.5 text-sm text-white outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                @error('name') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="description" class="mb-2 block text-sm font-medium text-slate-300">Mô tả (Tuỳ chọn)</label>
                <textarea id="description" name="description" rows="3"
                          class="w-full rounded-xl border border-white/10 bg-black/20 px-4 py-2.5 text-sm text-white outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">{{ old('description') }}</textarea>
                @error('description') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>

            <div class="rounded-xl border border-white/10 bg-white/[0.02] p-5">
                <h3 class="mb-4 text-sm font-bold text-white">Trạng thái</h3>
                <label class="relative inline-flex cursor-pointer items-center">
                    <input type="checkbox" name="is_active" value="1" class="peer sr-only" {{ old('is_active', true) ? 'checked' : '' }}>
                    <div class="peer h-6 w-11 rounded-full bg-slate-700 after:absolute after:left-[2px] after:top-[2px] after:h-5 after:w-5 after:rounded-full after:border after:border-gray-300 after:bg-white after:transition-all after:content-[''] peer-checked:bg-blue-600 peer-checked:after:translate-x-full peer-checked:after:border-white peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-800"></div>
                    <span class="ml-3 text-sm font-medium text-slate-300">Kích hoạt</span>
                </label>
            </div>

            <button type="submit" class="w-full rounded-xl bg-blue-600 px-4 py-3 text-sm font-bold text-white transition-colors hover:bg-blue-500">
                Lưu danh mục
            </button>
        </div>
    </form>
</div>
@endsection
