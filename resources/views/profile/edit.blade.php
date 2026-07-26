@extends('layouts.app')
@section('title', 'Cập nhật hồ sơ - NovaPhone')

@section('content')
<div class="space-y-3">
    <div class="flex">
        <span class="rounded-full border border-[#e8e4de] bg-white px-3 py-1 text-[11px] font-semibold text-[#444] shadow-sm">Cập nhật hồ sơ</span>
    </div>

    <section class="rounded-[28px] border border-[#ece8e2] bg-white p-5 sm:p-6 shadow-[0_10px_35px_rgba(0,0,0,.04)]">
        @if (session('status'))
            <div class="mb-4 rounded-[16px] border border-green-200 bg-green-50 p-4 text-sm text-green-800">
                {{ session('status') }}
            </div>
        @endif

        <h1 class="text-2xl font-bold">Cập nhật hồ sơ</h1>
        <p class="mt-1 text-sm text-[#8b8b8b]">Cập nhật thông tin cá nhân của bạn</p>

        <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="mt-6 space-y-4">
            @csrf
            @method('PUT')

            {{-- Avatar --}}
            <div>
                <label class="text-sm font-semibold text-[#171717]">Ảnh đại diện</label>
                <div class="mt-3 flex items-end gap-4">
                    <div class="size-20 rounded-[16px] border border-[#ece8e2] bg-[#fbfaf8] overflow-hidden">
                        @if ($user->avatar)
                            <img src="{{ str_starts_with($user->avatar, 'http') ? $user->avatar : asset($user->avatar) }}" class="size-full object-cover">
                        @else
                            <div class="size-full flex items-center justify-center text-2xl">👤</div>
                        @endif
                    </div>
                    <input type="file" name="avatar" accept="image/*" class="flex-1 rounded-[12px] border border-[#e8e4de] bg-[#fbfaf8] px-4 py-2.5 text-sm outline-none transition focus:border-black">
                </div>
                @error('avatar') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Name --}}
            <div>
                <label class="text-sm font-semibold text-[#171717]">Họ và tên</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" class="mt-2 w-full rounded-[12px] border border-[#e8e4de] bg-[#fbfaf8] px-4 py-3 text-sm outline-none transition focus:border-black @error('name') border-red-500 @enderror">
                @error('name') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Email is managed by the authentication flow and is not writable by this endpoint. --}}
            <div>
                <label class="text-sm font-semibold text-[#171717]">Email</label>
                <div class="mt-2 rounded-[12px] border border-[#e8e4de] bg-[#f3f1ed] px-4 py-3 text-sm text-[#666]">{{ $user->email }}</div>
            </div>

            {{-- Phone --}}
            <div>
                <label class="text-sm font-semibold text-[#171717]">Số điện thoại</label>
                <input type="tel" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="(tùy chọn)" class="mt-2 w-full rounded-[12px] border border-[#e8e4de] bg-[#fbfaf8] px-4 py-3 text-sm outline-none transition focus:border-black @error('phone') border-red-500 @enderror">
                @error('phone') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Submit --}}
            <div class="flex gap-3 pt-4">
                <button type="submit" class="rounded-full bg-black px-6 py-3 text-sm font-semibold text-white transition hover:bg-[#222]">
                    Lưu thay đổi
                </button>
                <a href="{{ route('account.show') }}" class="rounded-full border border-[#e8e4de] px-6 py-3 text-sm font-semibold text-[#111] transition hover:bg-[#fbfaf8]">
                    Hủy
                </a>
            </div>
        </form>
    </section>
</div>
@endsection
