@extends('admin.layout')
@section('page-title', 'Thêm bài viết mới')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <a href="{{ route('admin.posts.index') }}" class="flex items-center gap-2 text-sm font-medium text-slate-400 transition-colors hover:text-white">
        <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/></svg>
        Quay lại
    </a>
</div>

<div class="rounded-2xl border border-white/10 bg-[#0b1523] p-6 shadow-xl">
    <form action="{{ route('admin.posts.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="grid gap-6 lg:grid-cols-3">
            <!-- Cột trái: Thông tin chính -->
            <div class="space-y-6 lg:col-span-2">
                <div>
                    <label for="title" class="mb-2 block text-sm font-medium text-slate-300">Tiêu đề bài viết <span class="text-red-500">*</span></label>
                    <input type="text" id="title" name="title" value="{{ old('title') }}" required
                           class="w-full rounded-xl border border-white/10 bg-black/20 px-4 py-2.5 text-sm text-white outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                    @error('title') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="post_category_id" class="mb-2 block text-sm font-medium text-slate-300">Danh mục</label>
                    <select id="post_category_id" name="post_category_id"
                            class="w-full rounded-xl border border-white/10 bg-black/20 px-4 py-2.5 text-sm text-white outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        <option value="">-- Không có danh mục --</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('post_category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('post_category_id') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="summary" class="mb-2 block text-sm font-medium text-slate-300">Tóm tắt</label>
                    <textarea id="summary" name="summary" rows="3"
                              class="w-full rounded-xl border border-white/10 bg-black/20 px-4 py-2.5 text-sm text-white outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">{{ old('summary') }}</textarea>
                    @error('summary') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="content" class="mb-2 block text-sm font-medium text-slate-300">Nội dung <span class="text-red-500">*</span></label>
                    <textarea id="content" name="content" rows="15"
                              class="w-full rounded-xl border border-white/10 bg-black/20 px-4 py-2.5 text-sm text-white outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">{{ old('content') }}</textarea>
                    @error('content') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- Cột phải: Sidebar cấu hình -->
            <div class="space-y-6">
                <div class="rounded-xl border border-white/10 bg-white/[0.02] p-5">
                    <h3 class="mb-4 text-sm font-bold text-white">Trạng thái</h3>
                    <label class="relative inline-flex cursor-pointer items-center">
                        <input type="checkbox" name="is_published" value="1" class="peer sr-only" {{ old('is_published') ? 'checked' : '' }}>
                        <div class="peer h-6 w-11 rounded-full bg-slate-700 after:absolute after:left-[2px] after:top-[2px] after:h-5 after:w-5 after:rounded-full after:border after:border-gray-300 after:bg-white after:transition-all after:content-[''] peer-checked:bg-blue-600 peer-checked:after:translate-x-full peer-checked:after:border-white peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-800"></div>
                        <span class="ml-3 text-sm font-medium text-slate-300">Xuất bản bài viết ngay</span>
                    </label>
                </div>

                <div class="rounded-xl border border-white/10 bg-white/[0.02] p-5">
                    <h3 class="mb-4 text-sm font-bold text-white">Ảnh đại diện</h3>
                    <div class="flex flex-col items-center justify-center">
                        <label for="thumbnail" class="group relative flex h-48 w-full cursor-pointer flex-col items-center justify-center rounded-xl border-2 border-dashed border-white/20 bg-black/20 hover:border-blue-500/50 hover:bg-black/40 transition-all">
                            <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                <svg class="mb-3 size-8 text-slate-400 group-hover:text-blue-400 transition-colors" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 16.5V9.75m0 0l3 3m-3-3l-3 3M6.75 19.5a4.5 4.5 0 01-1.41-8.775 5.25 5.25 0 0110.233-2.33 3 3 0 013.758 3.848A3.742 3.742 0 0115 19.5H6.75z"/></svg>
                                <p class="mb-1 text-sm text-slate-400"><span class="font-semibold text-white">Nhấn để chọn</span> hoặc kéo thả ảnh vào đây</p>
                                <p class="text-xs text-slate-500">PNG, JPG, WEBP (Max: 2MB)</p>
                            </div>
                            <input id="thumbnail" name="thumbnail" type="file" class="hidden" accept="image/*" onchange="previewImage(this)">
                            <img id="image-preview" class="absolute inset-0 h-full w-full rounded-xl object-cover hidden">
                        </label>
                    </div>
                    @error('thumbnail') <p class="mt-2 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <button type="submit" class="w-full rounded-xl bg-blue-600 px-4 py-3 text-sm font-bold text-white transition-colors hover:bg-blue-500">
                    Lưu bài viết
                </button>
            </div>
        </div>
    </form>
</div>

<script>
    function previewImage(input) {
        const preview = document.getElementById('image-preview');
        const container = input.parentElement.querySelector('div');
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.classList.remove('hidden');
                container.classList.add('hidden');
            }
            reader.readAsDataURL(input.files[0]);
        } else {
            preview.src = '';
            preview.classList.add('hidden');
            container.classList.remove('hidden');
        }
    }
</script>

<!-- Thêm CKEditor cho phần Nội dung (Tuỳ chọn) -->
<script src="https://cdn.ckeditor.com/ckeditor5/40.2.0/classic/ckeditor.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if(typeof ClassicEditor !== 'undefined') {
            ClassicEditor
                .create(document.querySelector('#content'))
                .catch(error => {
                    console.error(error);
                });
        }
    });
</script>

<style>
    /* Chỉnh sửa style cơ bản cho CKEditor trong chế độ Dark Mode */
    .ck-editor__editable {
        min-height: 400px;
        background-color: rgba(0, 0, 0, 0.2) !important;
        color: white !important;
        border-color: rgba(255, 255, 255, 0.1) !important;
    }
    .ck-toolbar {
        background-color: #0b1523 !important;
        border-color: rgba(255, 255, 255, 0.1) !important;
    }
    .ck-button {
        color: white !important;
    }
    .ck.ck-button:hover, .ck.ck-button.ck-on {
        background-color: rgba(255, 255, 255, 0.1) !important;
    }
</style>
@endsection
