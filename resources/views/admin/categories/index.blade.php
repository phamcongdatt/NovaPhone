@extends('admin.layout')

@section('title', 'Danh mục')
@section('page-title', 'Quản lý danh mục')

@section('content')

<div class="flex flex-col gap-5">

    {{-- Flash messages --}}
    @if (session('success'))
        <div class="rounded-xl border border-green-500/20 bg-green-500/10 px-4 py-3 text-sm text-green-400">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->has('error'))
        <div class="rounded-xl border border-red-500/20 bg-red-500/10 px-4 py-3 text-sm text-red-400">
            {{ $errors->first('error') }}
        </div>
    @endif

    {{-- Thanh công cụ: tìm kiếm + nút thêm --}}
    <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
        <div class="relative min-w-[240px] flex-1 lg:max-w-sm">
            <input
                type="text" id="category-search"
                placeholder="Tìm danh mục..."
                class="w-full rounded-xl border border-white/10 bg-white/5 py-2.5 pl-10 pr-4 text-sm text-white outline-none transition-all duration-200 placeholder:text-gray-500 focus:border-brand-500 focus:ring-2 focus:ring-brand-500/25">
            <svg class="pointer-events-none absolute left-3.5 top-1/2 size-4 -translate-y-1/2 text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35M17 10.5a6.5 6.5 0 1 1-13 0 6.5 6.5 0 0 1 13 0Z"/></svg>
        </div>

        <button
            id="btn-create-category"
            type="button"
            class="inline-flex shrink-0 items-center justify-center gap-2 rounded-xl bg-brand-600 px-5 py-2.5 text-sm font-bold text-white shadow-lg shadow-brand-600/25 transition-all duration-200 hover:-translate-y-0.5 hover:bg-brand-500">
            <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
            Thêm danh mục
        </button>
    </div>

    @php
        $showForm = request('show') === 'create' || $errors->any() || old('name');
    @endphp

    {{-- Form thêm nhanh --}}
    <div id="create-form"
        class="{{ $showForm ? '' : 'hidden' }} rounded-2xl border border-white/5 bg-night-soft p-6">

        <h2 class="mb-4 text-base font-bold text-white">Thêm danh mục mới</h2>

        <form method="POST" action="{{ route('admin.categories.store') }}" class="space-y-5">
            @csrf

            <div class="grid gap-4 lg:grid-cols-3">
                <div class="lg:col-span-2">
                    <label class="mb-2 block text-sm font-semibold text-gray-300">Tên danh mục <span class="text-red-400">*</span></label>
                    <input
                        name="name" value="{{ old('name') }}"
                        class="w-full rounded-xl border {{ $errors->has('name') ? 'border-red-500/50' : 'border-white/10' }} bg-white/5 px-4 py-2.5 text-white placeholder:text-gray-500 outline-none transition focus:border-brand-500/50 focus:ring-2 focus:ring-brand-500/25"
                        placeholder="Ví dụ: Điện thoại, Tablet...">
                    @error('name')
                        <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="mb-2 block text-sm font-semibold text-gray-300">Trạng thái <span class="text-red-400">*</span></label>
                    <select
                        name="is_active"
                        class="w-full rounded-xl border {{ $errors->has('is_active') ? 'border-red-500/50' : 'border-white/10' }} bg-white/5 px-4 py-2.5 text-white outline-none transition focus:border-brand-500/50 focus:ring-2 focus:ring-brand-500/25">
                        <option value="">-- Chọn trạng thái --</option>
                        <option value="1" @selected(old('is_active') == '1')>Active</option>
                        <option value="0" @selected(old('is_active') == '0')>Inactive</option>
                    </select>
                    @error('is_active')
                        <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label class="mb-2 block text-sm font-semibold text-gray-300">Mô tả</label>
                <textarea
                    rows="3" name="description"
                    class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-2.5 text-white placeholder:text-gray-500 outline-none transition focus:border-brand-500/50 focus:ring-2 focus:ring-brand-500/25"
                    placeholder="Mô tả chi tiết về danh mục...">{{ old('description') }}</textarea>
            </div>

            <div class="flex gap-2 border-t border-white/5 pt-4">
                <button type="submit" class="rounded-xl bg-brand-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-brand-500">
                    Tạo danh mục
                </button>
                <button type="button" id="btn-cancel-category" class="rounded-xl border border-white/10 px-5 py-2.5 text-sm font-semibold text-gray-300 transition hover:bg-white/5">
                    Huỷ
                </button>
            </div>
        </form>
    </div>

    {{-- Bảng danh mục --}}
    <div class="overflow-x-auto rounded-2xl border border-white/5 bg-night-soft">
        <table class="w-full text-left text-sm">
            <thead>
                <tr class="border-b border-white/5 text-xs uppercase tracking-wider text-gray-500">
                    <th class="px-4 py-3">Tên</th>
                    <th class="px-4 py-3">Slug</th>
                    <th class="px-4 py-3 text-center">Sản phẩm</th>
                    <th class="px-4 py-3 text-center">Trạng thái</th>
                    <th class="px-4 py-3 text-right">Hành động</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5" id="category-tbody">
                @forelse($categories as $category)
                    <tr class="transition-colors duration-150 hover:bg-white/[0.03]" data-name="{{ \Illuminate\Support\Str::lower($category->name) }}">
                        <td class="px-4 py-3">
                            <p class="font-semibold text-white">{{ $category->name }}</p>
                            @if ($category->description)
                                <p class="mt-0.5 max-w-xs truncate text-xs text-gray-500">{{ $category->description }}</p>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-gray-400">{{ $category->slug }}</td>
                        <td class="px-4 py-3 text-center text-gray-300">{{ $category->products_count }}</td>
                        <td class="px-4 py-3 text-center">
                            <span class="rounded-full px-3 py-1 text-xs font-bold
                                {{ $category->is_active ? 'bg-green-500/15 text-green-400' : 'bg-gray-500/15 text-gray-400' }}">
                                {{ $category->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <div class="inline-flex items-center gap-1.5">
                                <a href="{{ route('admin.categories.edit', $category) }}"
                                   class="flex size-8 items-center justify-center rounded-lg bg-white/5 text-gray-400 transition-all duration-200 hover:bg-brand-600/20 hover:text-brand-400"
                                   title="Sửa">
                                    <svg class="size-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10"/></svg>
                                </a>
                                <form
                                    method="POST"
                                    action="{{ route('admin.categories.destroy', $category) }}"
                                    onsubmit="return confirm('Xoá danh mục này? Nếu còn sản phẩm thì thao tác sẽ bị huỷ.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        @disabled($category->products_count > 0)
                                        title="{{ $category->products_count > 0 ? 'Còn sản phẩm, không thể xoá' : 'Xoá' }}"
                                        class="flex size-8 items-center justify-center rounded-lg transition-all duration-200
                                            {{ $category->products_count > 0 ? 'cursor-not-allowed bg-white/5 text-gray-600' : 'bg-white/5 text-gray-400 hover:bg-red-500/20 hover:text-red-400' }}">
                                        <svg class="size-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-12 text-center text-gray-500">
                            Chưa có danh mục nào.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>

<script>
    const form = document.getElementById('create-form');
    document.getElementById('btn-create-category')?.addEventListener('click', () => {
        form.classList.toggle('hidden');
    });
    document.getElementById('btn-cancel-category')?.addEventListener('click', () => {
        form.classList.add('hidden');
    });

    // Tìm kiếm client-side
    document.getElementById('category-search')?.addEventListener('input', function () {
        const keyword = this.value.trim().toLowerCase();
        document.querySelectorAll('#category-tbody tr[data-name]').forEach(row => {
            row.classList.toggle('hidden', !row.dataset.name.includes(keyword));
        });
    });
</script>

@endsection
