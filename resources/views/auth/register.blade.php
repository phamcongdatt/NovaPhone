@extends('layouts.app')

@section('title', 'Đăng ký tài khoản — NovaPhone')

@section('content')
<section class="relative flex min-h-[calc(100vh-68px)] items-center justify-center px-4 py-12">
    <div class="w-full max-w-md">

        {{-- Tiêu đề --}}
        <div class="mb-8 text-center">
            <span class="mx-auto mb-4 flex size-12 items-center justify-center rounded-2xl bg-brand-600 text-xl font-extrabold text-white shadow-lg shadow-brand-600/30">N</span>
            <h1 class="text-2xl font-extrabold tracking-tight text-white">Tạo tài khoản mới</h1>
            <p class="mt-1.5 text-sm text-gray-400">Đăng ký để mua sắm nhanh hơn tại NovaPhone</p>
        </div>

        {{-- Thẻ form --}}
        <div class="rounded-2xl border border-white/10 bg-white/[0.03] p-6 shadow-2xl shadow-black/30 sm:p-8">
            <form method="POST" action="{{ route('register') }}" class="space-y-5" novalidate>
                @csrf

                {{-- Họ tên --}}
                <div>
                    <label for="name" class="mb-1.5 block text-sm font-semibold text-gray-200">Họ và tên</label>
                    <input
                        id="name" name="name" type="text" value="{{ old('name') }}"
                        autocomplete="name" autofocus
                        placeholder="Nguyễn Văn A"
                        class="w-full rounded-xl border bg-white/5 px-4 py-2.5 text-sm text-white outline-none transition-all duration-200 ease-in-out placeholder:text-gray-500 focus:bg-white/[0.08] focus:ring-2 @error('name') border-red-500/70 focus:ring-red-500/30 @else border-white/10 focus:border-brand-500 focus:ring-brand-500/25 @enderror"
                    >
                    @error('name')
                        <p class="mt-1.5 text-xs font-medium text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Email --}}
                <div>
                    <label for="email" class="mb-1.5 block text-sm font-semibold text-gray-200">Email</label>
                    <input
                        id="email" name="email" type="email" value="{{ old('email') }}"
                        autocomplete="email"
                        placeholder="email@example.com"
                        class="w-full rounded-xl border bg-white/5 px-4 py-2.5 text-sm text-white outline-none transition-all duration-200 ease-in-out placeholder:text-gray-500 focus:bg-white/[0.08] focus:ring-2 @error('email') border-red-500/70 focus:ring-red-500/30 @else border-white/10 focus:border-brand-500 focus:ring-brand-500/25 @enderror"
                    >
                    @error('email')
                        <p class="mt-1.5 text-xs font-medium text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Mật khẩu --}}
                <div>
                    <label for="password" class="mb-1.5 block text-sm font-semibold text-gray-200">Mật khẩu</label>
                    <input
                        id="password" name="password" type="password"
                        autocomplete="new-password"
                        placeholder="Tối thiểu 8 ký tự"
                        class="w-full rounded-xl border bg-white/5 px-4 py-2.5 text-sm text-white outline-none transition-all duration-200 ease-in-out placeholder:text-gray-500 focus:bg-white/[0.08] focus:ring-2 @error('password') border-red-500/70 focus:ring-red-500/30 @else border-white/10 focus:border-brand-500 focus:ring-brand-500/25 @enderror"
                    >
                    @error('password')
                        <p class="mt-1.5 text-xs font-medium text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Xác nhận mật khẩu --}}
                <div>
                    <label for="password_confirmation" class="mb-1.5 block text-sm font-semibold text-gray-200">Xác nhận mật khẩu</label>
                    <input
                        id="password_confirmation" name="password_confirmation" type="password"
                        autocomplete="new-password"
                        placeholder="Nhập lại mật khẩu"
                        class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-2.5 text-sm text-white outline-none transition-all duration-200 ease-in-out placeholder:text-gray-500 focus:border-brand-500 focus:bg-white/[0.08] focus:ring-2 focus:ring-brand-500/25"
                    >
                </div>

                {{-- Nút submit --}}
                <button type="submit"
                        class="w-full rounded-xl bg-brand-600 px-4 py-3 text-sm font-bold text-white shadow-lg shadow-brand-600/30 transition-all duration-200 ease-in-out hover:bg-brand-500 hover:shadow-brand-500/40 active:scale-[0.99]">
                    Đăng ký
                </button>
            </form>
        </div>

        {{-- Link đăng nhập --}}
        <p class="mt-6 text-center text-sm text-gray-400">
            Đã có tài khoản?
            <a href="#" class="font-semibold text-brand-400 transition-colors duration-200 hover:text-brand-300">Đăng nhập ngay</a>
        </p>
    </div>
</section>
@endsection