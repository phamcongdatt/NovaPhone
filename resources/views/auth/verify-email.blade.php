@extends('layouts.app')

@section('title', 'Xác thực email — NovaPhone')

@section('content')
<section class="relative flex min-h-[calc(100vh-68px)] items-center justify-center px-4 py-12">
    <div class="w-full max-w-md text-center">

        {{-- Icon email --}}
        <span class="mx-auto mb-5 flex size-16 items-center justify-center rounded-2xl bg-brand-600/15 text-brand-400 ring-1 ring-brand-500/30">
            <svg class="size-8" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75"/>
            </svg>
        </span>

        <h1 class="text-2xl font-extrabold tracking-tight text-white">Xác thực địa chỉ email</h1>
        <p class="mx-auto mt-3 max-w-sm text-sm leading-relaxed text-gray-400">
            Cảm ơn bạn đã đăng ký! Chúng tôi đã gửi một liên kết xác thực tới email
            <span class="font-semibold text-gray-200">{{ auth()->user()?->email }}</span>.
            Vui lòng kiểm tra hộp thư (kể cả mục Spam) và nhấn vào liên kết để kích hoạt tài khoản.
        </p>

        {{-- Thông báo khi gửi lại thành công --}}
        @if (session('status') === 'verification-link-sent')
            <div class="mt-6 rounded-xl border border-green-500/30 bg-green-500/10 px-4 py-3 text-sm font-medium text-green-300">
                Một liên kết xác thực mới đã được gửi tới email của bạn.
            </div>
        @endif

        {{-- Nút gửi lại email --}}
        <form method="POST" action="{{ route('verification.send') }}" class="mt-7">
            @csrf
            <button type="submit"
                    class="w-full rounded-xl bg-brand-600 px-4 py-3 text-sm font-bold text-white shadow-lg shadow-brand-600/30 transition-all duration-200 ease-in-out hover:bg-brand-500 hover:shadow-brand-500/40 active:scale-[0.99]">
                Gửi lại email xác thực
            </button>
        </form>

        {{-- Đăng xuất --}}
        <form method="POST" action="#" class="mt-3">
            @csrf
            <button type="submit"
                    class="text-sm font-semibold text-gray-400 transition-colors duration-200 hover:text-white">
                Đăng xuất
            </button>
        </form>
    </div>
</section>
@endsection