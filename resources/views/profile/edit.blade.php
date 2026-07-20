@extends('layouts.app')

@section('title', 'Cập nhật hồ sơ - NovaPhone')

@section('content')
<section class="bg-night py-10 sm:py-14">
    <div class="mx-auto max-w-3xl px-4 sm:px-6">
        <div class="mb-8 flex items-center justify-between">
            <div>
                <a href="{{ route('account.show') }}" class="mb-2 inline-flex items-center text-sm font-semibold text-brand-400 hover:text-brand-300">
                    <svg class="mr-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                    </svg>
                    Quay lại
                </a>
                <h1 class="text-3xl font-extrabold tracking-tight text-white">Cập nhật hồ sơ</h1>
            </div>
        </div>

        <div class="rounded-2xl border border-white/10 bg-night-soft p-5 shadow-2xl shadow-black/30 sm:p-8">
            <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- Avatar -->
                <div>
                    <label class="block text-sm font-bold text-gray-300">Ảnh đại diện</label>
                    <div class="mt-4 flex items-center gap-5">
                        <div class="relative size-24 shrink-0 overflow-hidden rounded-full border-2 border-brand-500/30 bg-night">
                            @if($user->avatar)
                                <img src="{{ str_starts_with($user->avatar, 'http') ? $user->avatar : (str_starts_with($user->avatar, 'images/') ? asset($user->avatar) : asset('storage/' . $user->avatar)) }}" alt="{{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}" class="h-full w-full object-cover text-center font-bold text-gray-500" onerror="this.onerror=null; this.outerHTML='<div class=\'flex h-full w-full items-center justify-center bg-brand-600 text-3xl font-extrabold text-white\'>{{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}</div>';">
                            @else
                                <div class="flex h-full w-full items-center justify-center bg-brand-600 text-3xl font-extrabold text-white">
                                    {{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}
                                </div>
                            @endif
                        </div>
                        <div>
                            <input type="file" name="avatar" id="avatar" accept="image/*" class="block w-full text-sm text-gray-400 file:mr-4 file:rounded-full file:border-0 file:bg-brand-500/10 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-brand-400 hover:file:bg-brand-500/20">
                            <p class="mt-2 text-xs text-gray-500">Định dạng JPEG, PNG, JPG. Tối đa 2MB.</p>
                            @error('avatar')
                                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-bold text-gray-300">Họ tên <span class="text-red-400">*</span></label>
                    <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required
                           class="mt-2 block w-full rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-white placeholder-gray-500 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 sm:text-sm">
                    @error('name')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email (Readonly) -->
                <div>
                    <label for="email" class="block text-sm font-bold text-gray-300">Email</label>
                    <input type="email" id="email" value="{{ $user->email }}" disabled
                           class="mt-2 block w-full rounded-xl border border-white/5 bg-white/5 px-4 py-3 text-gray-400 opacity-70 sm:text-sm cursor-not-allowed">
                    <p class="mt-1 text-xs text-gray-500">Email không thể thay đổi để đảm bảo bảo mật.</p>
                </div>

                <!-- Phone -->
                <div>
                    <label for="phone" class="block text-sm font-bold text-gray-300">Số điện thoại</label>
                    <input type="text" name="phone" id="phone" value="{{ old('phone', $user->phone) }}"
                           class="mt-2 block w-full rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-white placeholder-gray-500 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 sm:text-sm"
                           placeholder="Nhập số điện thoại của bạn">
                    @error('phone')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="pt-4">
                    <button type="submit" class="w-full rounded-xl bg-brand-600 px-6 py-3.5 text-sm font-bold text-white shadow-lg shadow-brand-600/30 transition-all duration-200 hover:bg-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2 focus:ring-offset-night sm:w-auto">
                        Lưu thay đổi
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>
@endsection
