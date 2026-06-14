
@extends('layouts.app')

@section('title', 'Đăng nhập tài khoản | NovaPhone')

@section('content')
<div class="hero-glow flex min-h-[calc(100vh-68px-340px)] items-center justify-center px-4 py-16 sm:px-6">
    <div class="w-full max-w-md overflow-hidden rounded-3xl border border-white/10 bg-night-soft/60 p-8 shadow-2xl shadow-black/50 backdrop-blur-2xl">
        
        {{-- Header Form --}}
        <div class="text-center">
            <span class="inline-flex size-12 items-center justify-center rounded-2xl bg-brand-600 text-xl font-black text-white shadow-lg shadow-brand-600/30">N</span>
            <h2 class="mt-4 text-2xl font-black tracking-tight text-white sm:text-3xl">Đăng nhập tài khoản</h2>
            <p class="mt-2 text-sm text-gray-400">Trải nghiệm mua sắm đẳng cấp tại NovaPhone</p>
        </div>

        @if (session('error'))
            <div class="mt-6 rounded-xl border border-red-500/20 bg-red-500/10 p-4 text-sm text-red-400">
                {{ session('error') }}
            </div>
        @endif

        {{-- Form đăng nhập chuẩn --}}
        <form method="POST" action="{{ route('login') }}" class="mt-8 space-y-5">
            @csrf
            
            {{-- Email --}}
            <div class="space-y-1.5">
                <label for="email" class="text-xs font-bold uppercase tracking-wider text-gray-400">Địa chỉ email</label>
                <div class="relative">
                    <input 
                        type="email" 
                        name="email" 
                        id="email" 
                        value="{{ old('email') }}" 
                        required 
                        placeholder="ten@novaphone.vn"
                        class="w-full rounded-xl border border-white/10 bg-white/5 py-3 pl-11 pr-4 text-sm text-white outline-none transition duration-200 focus:border-brand-500 focus:bg-white/[0.08] focus:ring-2 focus:ring-brand-500/25"
                    >
                    <svg class="absolute left-4 top-1/2 size-4 -translate-y-1/2 text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25H4.5A2.25 2.25 0 0 1 2.25 17.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5H4.5a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                    </svg>
                </div>
                @error('email')
                    <span class="text-xs text-red-500">{{ $message }}</span>
                @enderror
            </div>

            {{-- Mật khẩu --}}
            <div class="space-y-1.5">
                <div class="flex items-center justify-between">
                    <label for="password" class="text-xs font-bold uppercase tracking-wider text-gray-400">Mật khẩu</label>
                    <a href="#" class="text-xs font-bold text-brand-400 hover:text-brand-300 transition-colors">Quên mật khẩu?</a>
                </div>
                <div class="relative">
                    <input 
                        type="password" 
                        name="password" 
                        id="password" 
                        required 
                        placeholder="••••••••"
                        class="w-full rounded-xl border border-white/10 bg-white/5 py-3 pl-11 pr-4 text-sm text-white outline-none transition duration-200 focus:border-brand-500 focus:bg-white/[0.08] focus:ring-2 focus:ring-brand-500/25"
                    >
                    <svg class="absolute left-4 top-1/2 size-4 -translate-y-1/2 text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                    </svg>
                </div>
            </div>

            {{-- Remember me --}}
            <div class="flex items-center">
                <input 
                    type="checkbox" 
                    name="remember" 
                    id="remember" 
                    class="size-4 rounded border-white/10 bg-white/5 text-brand-600 focus:ring-brand-500/30"
                >
                <label for="remember" class="ml-2 text-sm text-gray-400">Ghi nhớ đăng nhập</label>
            </div>

            {{-- Nút Đăng nhập --}}
            <button 
                type="submit" 
                class="w-full rounded-xl bg-gradient-to-r from-brand-600 to-brand-500 py-3 text-sm font-bold text-white shadow-lg shadow-brand-600/25 transition-all duration-200 hover:-translate-y-0.5 hover:shadow-brand-600/35"
            >
                Đăng nhập
            </button>
        </form>

        {{-- Divider --}}
        <div class="relative my-6 flex items-center justify-center">
            <div class="absolute inset-x-0 h-px bg-white/10"></div>
            <span class="relative bg-night-soft px-3 text-xs uppercase tracking-wider text-gray-500">Hoặc thử nghiệm nhanh</span>
        </div>

        {{-- Cụm đăng nhập nhanh (Quick Login) --}}
        <div class="grid gap-3">
            <a 
                href="{{ route('quick-login', ['email' => 'user@novaphone.vn']) }}"
                class="flex items-center justify-between rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-sm transition hover:border-brand-500/50 hover:bg-brand-600/10"
            >
                <div class="flex items-center gap-3">
                    <span class="flex size-7 items-center justify-center rounded-lg bg-emerald-500/15 text-[11px] font-bold text-emerald-400">A</span>
                    <div class="text-left">
                        <span class="block font-semibold text-white">Khách hàng Test</span>
                        <span class="block text-xs text-gray-500">Nguyễn Văn A (Mặc định)</span>
                    </div>
                </div>
                <svg class="size-4 text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                </svg>
            </a>

            <a 
                href="{{ route('quick-login', ['email' => 'admin@novaphone.vn']) }}"
                class="flex items-center justify-between rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-sm transition hover:border-red-500/50 hover:bg-red-600/10"
            >
                <div class="flex items-center gap-3">
                    <span class="flex size-7 items-center justify-center rounded-lg bg-red-500/15 text-[11px] font-bold text-red-400">AD</span>
                    <div class="text-left">
                        <span class="block font-semibold text-white">Quản trị viên Test</span>
                        <span class="block text-xs text-gray-500">admin@novaphone.vn</span>
                    </div>
                </div>
                <svg class="size-4 text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                </svg>
            </a>
        </div>

        {{-- Đăng ký --}}
        <p class="mt-8 text-center text-sm text-gray-500">
            Chưa có tài khoản? 
            <a href="#" class="font-bold text-brand-400 hover:text-brand-300 transition-colors">Đăng ký ngay</a>
        </p>

    </div>
</div>
@endsection
=======

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


