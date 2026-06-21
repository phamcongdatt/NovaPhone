```blade
@extends('admin.layout')

@section('title', 'Danh mục')
@section('page-title', 'Quản lý danh mục')

@section('content')

<div class="flex flex-col gap-6 pt-4">

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

    {{-- Toolbar --}}
    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">

        <div class="flex flex-col gap-3 lg:flex-row lg:items-center">
            <h2 class="text-lg font-bold text-white">
                Danh sách danh mục
            </h2>

            <div class="relative min-w-[240px]">
                <input
                    type="search"
                    placeholder="Tìm danh mục..."
                    class="w-full rounded-xl border border-white/10 bg-white/5 py-2.5 pl-10 pr-4 text-sm text-white placeholder:text-gray-500 focus:border-brand-500 focus:ring-2 focus:ring-brand-500/25">

                <svg class="pointer-events-none absolute left-3 top-1/2 size-4 -translate-y-1/2 text-gray-500"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round"
                        stroke-linejoin="round"
                        d="m21 21-4.35-4.35M17 10.5a6.5 6.5 0 1 1-13 0 6.5 6.5 0 0 1 13 0Z"/>
                </svg>
            </div>
        </div>

        <button
            id="btn-create-category"
            type="button"
            class="rounded-xl bg-brand-600 px-5 py-2.5 text-sm font-bold text-white hover:bg-brand-500">

            Thêm danh mục
        </button>

    </div>

    @php
        $showForm = request('show') === 'create' || $errors->any() || old('name');
    @endphp

    {{-- Form --}}
    <div id="create-form"
        class="{{ $showForm ? '' : 'hidden' }} rounded-2xl border border-white/5 bg-night-soft p-6">

        <form method="POST"
            action="{{ route('admin.categories.store') }}"
            class="space-y-5">

            @csrf

            <div class="grid gap-4 lg:grid-cols-3">

                <div class="lg:col-span-2">

                    <label class="mb-2 block text-sm text-gray-300">
                        Tên danh mục
                    </label>

                    <input
                        name="name"
                        value="{{ old('name') }}"
                        class="w-full rounded-xl border {{ $errors->has('name') ? 'border-red-500/50' : 'border-white/10' }} bg-white/5 px-4 py-3 text-white"
                        placeholder="Ví dụ: Điện thoại, Tablet...">

                    @error('name')
                        <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                    @enderror

                </div>

                <div>

                    <label class="mb-2 block text-sm text-gray-300">
                        Trạng thái
                    </label>

                    <select
                        name="status"
                        class="w-full rounded-xl border {{ $errors->has('status') ? 'border-red-500/50' : 'border-white/10' }} bg-white/5 px-4 py-3 text-white">

                        <option value="">Chọn</option>

                        <option value="active" @selected(old('status') === 'active')>
                            Active
                        </option>

                        <option value="inactive" @selected(old('status') === 'inactive')>
                            Inactive
                        </option>

                    </select>

                    @error('status')
                        <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                    @enderror

                </div>

            </div>

            <div>

                <label class="mb-2 block text-sm text-gray-300">
                    Mô tả
                </label>

                <textarea
                    rows="3"
                    name="description"
                    class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-white">{{ old('description') }}</textarea>

            </div>

            <button
                class="rounded-xl bg-brand-600 px-5 py-2.5 text-white">
                Tạo danh mục
            </button>

        </form>


    </div>

    {{-- Table --}}
    <div class="overflow-hidden rounded-2xl border border-white/5 bg-night-soft">

        <table class="w-full">

            <thead>

                <tr class="border-b border-white/5">

                    <th class="px-4 py-4 text-left">
                        Tên
                    </th>

                    <th class="px-4 py-4">
                        Slug
                    </th>

                    <th class="px-4 py-4 text-center">
                        Sản phẩm
                    </th>

                    <th class="px-4 py-4 text-center">
                        Trạng thái
                    </th>

                    <th class="px-4 py-4 text-right">
                        Hành động
                    </th>

                </tr>

            </thead>

            <tbody>

                @forelse($categories as $category)

                <tr class="border-b border-white/5">

                    <td class="px-4 py-4">
                        {{ $category->name }}
                    </td>

                    <td class="px-4 py-4">
                        {{ $category->slug }}
                    </td>

                    <td class="px-4 py-4 text-center">
                        {{ $category->products_count }}
                    </td>

                    <td class="px-4 py-4 text-center">
                        {{ $category->status }}
                    </td>

                    <td class="px-4 py-4 text-right">

                        <form
                            method="POST"
                            action="{{ route('admin.categories.destroy', $category) }}"
                            onsubmit="return confirm('Xoá danh mục này? Nếu còn sản phẩm thì thao tác sẽ bị huỷ.')">

                            @csrf
                            @method('DELETE')

                            <button
                                type="submit"
                                @disabled($category->products_count > 0)
                                class="rounded-lg px-3 py-2 text-sm font-semibold transition duration-150
                                    {{ $category->products_count > 0 ? 'bg-gray-500/10 text-gray-400 cursor-not-allowed' : 'bg-red-500/15 text-red-400 hover:bg-red-500/20' }}">
                                Xoá
                            </button>

                        </form>

                    </td>

                </tr>

                @empty

                <tr>

                    <td colspan="5"
                        class="py-10 text-center text-gray-500">

                        Chưa có danh mục

                    </td>

                </tr>

                @endforelse

            </tbody>

        </table>

    </div>

</div>

<script>
document
.getElementById('btn-create-category')
?.addEventListener('click', function () {
    document
        .getElementById('create-form')
        .classList.toggle('hidden');
});
</script>

@endsection
```
