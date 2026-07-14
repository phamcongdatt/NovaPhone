@extends('admin.layout')
@section('page-title', 'Sửa Banner')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <a href="{{ route('admin.banners.index') }}" class="flex items-center gap-2 text-sm font-medium text-slate-400 transition-colors hover:text-white">
        <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/></svg>
        Quay lại danh sách
    </a>
</div>

<div class="mx-auto max-w-4xl rounded-2xl border border-white/10 bg-[#0b1523] p-6 shadow-xl">
    <form action="{{ route('admin.banners.update', $banner) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="grid gap-8 lg:grid-cols-2">
            <!-- Cột trái: Nội dung -->
            <div class="space-y-5">
                <div>
                    <label for="badge" class="mb-2 block text-sm font-medium text-slate-300">Nhãn nổi bật (Badge)</label>
                    <input type="text" id="badge" name="badge" value="{{ old('badge', $banner->badge) }}" placeholder="Để trống nếu muốn ẩn"
                           class="w-full rounded-xl border border-white/10 bg-black/20 px-4 py-2.5 text-sm text-white outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                    @error('badge') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="title" class="mb-2 block text-sm font-medium text-slate-300">Tiêu đề (Hỗ trợ thẻ HTML)</label>
                    <textarea id="title" name="title" rows="2"
                              class="w-full rounded-xl border border-white/10 bg-black/20 px-4 py-2.5 text-sm font-mono text-white outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">{{ old('title', $banner->title) }}</textarea>
                    <p class="mt-1 text-[11px] text-slate-500">Cho phép dùng HTML để tạo hiệu ứng màu sắc.</p>
                    @error('title') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="description" class="mb-2 block text-sm font-medium text-slate-300">Mô tả phụ</label>
                    <textarea id="description" name="description" rows="2" placeholder="Để trống nếu muốn ẩn"
                              class="w-full rounded-xl border border-white/10 bg-black/20 px-4 py-2.5 text-sm text-white outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">{{ old('description', $banner->description) }}</textarea>
                    @error('description') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="highlights" class="mb-2 block text-sm font-medium text-slate-300">Thông tin nổi bật (Cách nhau bằng dấu phẩy)</label>
                    @php
                        $highlightsString = $banner->highlights ? implode(', ', $banner->highlights) : '';
                    @endphp
                    <input type="text" id="highlights" name="highlights" value="{{ old('highlights', $highlightsString) }}" placeholder="VD: Camera 200MP, S Pen"
                           class="w-full rounded-xl border border-white/10 bg-black/20 px-4 py-2.5 text-sm text-white outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                    @error('highlights') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="buy_url" class="mb-2 block text-sm font-medium text-slate-300">Link nút "Mua ngay"</label>
                        <input type="url" id="buy_url" name="buy_url" value="{{ old('buy_url', $banner->buy_url) }}" placeholder="Để trống nếu muốn ẩn"
                               class="w-full rounded-xl border border-white/10 bg-black/20 px-4 py-2.5 text-sm text-white outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        @error('buy_url') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="detail_url" class="mb-2 block text-sm font-medium text-slate-300">Link nút "Xem chi tiết"</label>
                        <input type="url" id="detail_url" name="detail_url" value="{{ old('detail_url', $banner->detail_url) }}" placeholder="Để trống nếu muốn ẩn"
                               class="w-full rounded-xl border border-white/10 bg-black/20 px-4 py-2.5 text-sm text-white outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        @error('detail_url') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <!-- Cột phải: Cài đặt & Hình ảnh -->
            <div class="space-y-5">
                <div class="rounded-xl border border-white/10 bg-white/[0.02] p-5">
                    <label for="image" class="mb-3 block text-sm font-bold text-white">Hình ảnh Banner (Bên phải)</label>
                    
                    @if($banner->image)
                        <div class="mb-3 w-full h-32 overflow-hidden rounded-xl border border-white/10 bg-black/20 flex items-center justify-center">
                            <img src="{{ asset('storage/' . $banner->image) }}" class="h-full object-contain">
                        </div>
                    @endif

                    <input type="file" id="image" name="image" accept="image/*"
                           class="w-full rounded-xl border border-white/10 bg-black/20 px-4 py-2.5 text-sm text-white outline-none file:mr-4 file:rounded-full file:border-0 file:bg-blue-600 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white hover:file:bg-blue-500 focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                    <p class="mt-2 text-xs text-slate-400">Bỏ trống nếu không muốn thay đổi ảnh.</p>
                    @error('image') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <div class="rounded-xl border border-white/10 bg-white/[0.02] p-5">
                    <h3 class="mb-4 text-sm font-bold text-white">Tuỳ chọn hiển thị</h3>
                    
                    <div class="mb-4">
                        <label for="sort_order" class="mb-2 block text-sm font-medium text-slate-300">Thứ tự sắp xếp (Nhỏ xếp trước)</label>
                        <input type="number" id="sort_order" name="sort_order" value="{{ old('sort_order', $banner->sort_order) }}"
                               class="w-full rounded-xl border border-white/10 bg-black/20 px-4 py-2 text-sm text-white outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                    </div>

                    <label class="relative inline-flex cursor-pointer items-center">
                        <input type="checkbox" name="is_active" value="1" class="peer sr-only" {{ old('is_active', $banner->is_active) ? 'checked' : '' }}>
                        <div class="peer h-6 w-11 rounded-full bg-slate-700 after:absolute after:left-[2px] after:top-[2px] after:h-5 after:w-5 after:rounded-full after:border after:border-gray-300 after:bg-white after:transition-all after:content-[''] peer-checked:bg-blue-600 peer-checked:after:translate-x-full peer-checked:after:border-white peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-800"></div>
                        <span class="ml-3 text-sm font-medium text-slate-300">Đang hiển thị ngoài trang chủ</span>
                    </label>
                </div>

                <div class="pt-4">
                    <button type="submit" class="w-full rounded-xl bg-blue-600 px-4 py-3 text-sm font-bold text-white transition-colors hover:bg-blue-500">
                        Lưu thay đổi
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
