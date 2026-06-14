
@extends('auth.layout')
@section('title', 'Đăng nhập')

@section('content')
<h2 class="text-xl font-semibold text-gray-900 mb-1">Chào mừng trở lại</h2>
<p class="text-sm text-gray-500 mb-6">Đăng nhập vào tài khoản của bạn</p>

<form method="POST" action="{{ route('login') }}" novalidate>
    @csrf

    {{-- Email --}}
    <div class="mb-4">
        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
        <input
            id="email" name="email" type="email"
            value="{{ old('email') }}"
            autocomplete="email" autofocus
            class="input-field @error('email') border-red-400 bg-red-50 @enderror"
            placeholder="ban@example.com"
        >
        @error('email')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>

    {{-- Mật khẩu --}}
    <div class="mb-4">
        <div class="flex items-center justify-between mb-1">
            <label for="password" class="text-sm font-medium text-gray-700">Mật khẩu</label>
            <a href="{{ route('password.request') }}" class="text-xs text-brand-600 hover:text-brand-700">
                Quên mật khẩu?
            </a>
        </div>
        <div class="relative">
            <input
                id="password" name="password" type="password"
                autocomplete="current-password"
                class="input-field pr-10 @error('password') border-red-400 bg-red-50 @enderror"
                placeholder="••••••••"
            >
            {{-- Toggle hiện/ẩn mật khẩu --}}
            <button type="button" onclick="togglePassword('password')"
                    class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                <svg id="eye-password" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7
                             -1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
            </button>
        </div>
        @error('password')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>

    {{-- Nhớ tài khoản --}}
    <div class="flex items-center mb-6">
        <input
            id="remember" name="remember" type="checkbox"
            value="1"
            {{ old('remember') ? 'checked' : '' }}
            class="w-4 h-4 text-brand-600 border-gray-300 rounded focus:ring-brand-500 cursor-pointer"
        >
        <label for="remember" class="ml-2 text-sm text-gray-600 cursor-pointer">
            Nhớ tài khoản
        </label>
    </div>

    <button type="submit" class="btn-primary">Đăng nhập</button>
</form>

<p class="text-center text-sm text-gray-500 mt-6">
    Chưa có tài khoản?
    <a href="{{ route('register') }}" class="text-brand-600 hover:text-brand-700 font-medium">Đăng ký ngay</a>
</p>

<script>
function togglePassword(id) {
    const input = document.getElementById(id);
    input.type = input.type === 'password' ? 'text' : 'password';
}
</script>
@endsection
=======
<!DOCTYPE html>
<html lang="vi" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Đăng nhập tài khoản - NovaPhone</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-night font-sans text-gray-100 antialiased">

<div class="flex min-h-screen items-center justify-center p-4 sm:p-6 lg:p-10">
    <div class="relative w-full max-w-md">
        <div class="pointer-events-none absolute -inset-[2px] rounded-[2.1rem] bg-gradient-to-r from-brand-500/60 via-indigo-400/50 to-brand-500/60 opacity-80 blur-[10px]"></div>
        <div class="pointer-events-none absolute -inset-6 rounded-[2.5rem] bg-gradient-to-r from-brand-600/25 via-indigo-500/20 to-brand-600/25 opacity-70 blur-3xl"></div>

        <div class="relative overflow-hidden rounded-[2rem] border border-white/15 bg-night-soft px-6 py-10 shadow-2xl shadow-black/70 sm:px-10">
            <a href="{{ route('home') }}" class="mb-6 flex items-center justify-center">
                <img src="{{ asset('images/brand/nova-phone-logo.png') }}"
                     alt="NovaPhone"
                     class="h-14 w-auto max-w-[220px] object-contain">
            </a>

            <h1 class="text-center text-2xl font-extrabold tracking-tight text-white sm:text-[1.7rem]">Đăng nhập tài khoản</h1>

            <form method="POST" action="{{ route('login') }}" class="mt-8 space-y-4" novalidate>
                @csrf

                <div>
                    <div class="relative">
                        <span class="pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-500">
                            <svg class="size-5" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25H4.5a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5H4.5A2.25 2.25 0 0 0 2.25 6.75m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75"/></svg>
                        </span>
                        <input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" autofocus placeholder="Email"
                               class="w-full rounded-xl border bg-white/[0.04] py-3 pl-11 pr-4 text-sm text-white outline-none transition-all duration-200 ease-in-out placeholder:text-gray-500 focus:bg-white/[0.07] focus:ring-2 @error('email') border-red-500/70 focus:ring-red-500/25 @else border-white/10 focus:border-amber-400 focus:ring-amber-400/20 @enderror">
                    </div>
                    @error('email') <p class="mt-1.5 pl-1 text-xs font-medium text-red-400">{{ $message }}</p> @enderror
                </div>

                <div>
                    <div class="relative">
                        <span class="pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-500">
                            <svg class="size-5" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 0h10.5a1.5 1.5 0 0 1 1.5 1.5v6a1.5 1.5 0 0 1-1.5 1.5H6.75a1.5 1.5 0 0 1-1.5-1.5v-6a1.5 1.5 0 0 1 1.5-1.5Z"/></svg>
                        </span>
                        <input id="password" name="password" type="password" autocomplete="current-password" placeholder="Mật khẩu"
                               class="w-full rounded-xl border bg-white/[0.04] py-3 pl-11 pr-4 text-sm text-white outline-none transition-all duration-200 ease-in-out placeholder:text-gray-500 focus:bg-white/[0.07] focus:ring-2 @error('password') border-red-500/70 focus:ring-red-500/25 @else border-white/10 focus:border-amber-400 focus:ring-amber-400/20 @enderror">
                    </div>
                    @error('password') <p class="mt-1.5 pl-1 text-xs font-medium text-red-400">{{ $message }}</p> @enderror
                </div>

                <label class="flex items-center gap-2.5 text-xs text-gray-400">
                    <input type="checkbox" name="remember" value="1"
                           class="size-4 rounded border-white/20 bg-white/5 text-amber-500 focus:ring-amber-400/40">
                    <span>Ghi nhớ đăng nhập</span>
                </label>

                <button type="submit"
                        class="w-full rounded-xl bg-gradient-to-r from-amber-400 to-amber-500 py-3.5 text-sm font-extrabold uppercase tracking-wide text-night shadow-lg shadow-amber-500/25 transition-all duration-200 ease-in-out hover:from-amber-300 hover:to-amber-400 hover:shadow-amber-400/40 active:scale-[0.99]">
                    Đăng nhập
                </button>
            </form>

            <p class="mt-7 text-center text-sm text-gray-400">
                Chưa có tài khoản?
                <a href="{{ route('register') }}" class="font-semibold text-amber-400 transition-colors duration-200 hover:text-amber-300">Đăng ký ngay</a>
            </p>
        </div>
    </div>
</div>

</body>

