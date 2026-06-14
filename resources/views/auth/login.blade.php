
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
                    <a href="{{ route('password.request') }}" class="text-xs font-bold text-brand-400 hover:text-brand-300 transition-colors">Quên mật khẩu?</a>
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
                    name="remember" value="1" 
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
            <a href="{{ route('register') }}" class="font-bold text-brand-400 hover:text-brand-300 transition-colors">Đăng ký ngay</a>
        </p>

    </div>
</div>
@endsection
