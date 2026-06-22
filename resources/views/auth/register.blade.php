<!DOCTYPE html>
<html lang="vi" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Đăng ký - NovaPhone</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-night font-sans text-gray-100 antialiased">
<main class="relative flex min-h-screen items-center justify-center overflow-hidden px-4 py-10 sm:px-6">
    <div aria-hidden="true" class="pointer-events-none absolute inset-0">
        <div class="absolute left-1/2 top-[-18rem] size-[40rem] -translate-x-1/2 rounded-full bg-brand-600/15 blur-3xl"></div>
        <div class="absolute bottom-[-16rem] right-[-10rem] size-[32rem] rounded-full bg-blue-400/10 blur-3xl"></div>
    </div>

    <div class="relative z-10 w-full max-w-md">
        <a href="{{ route('home') }}" class="mx-auto mb-7 flex w-fit items-center justify-center" aria-label="NovaPhone - Trang chủ">
            <img src="{{ asset('images/brand/nova-phone-logo.png') }}" alt="NovaPhone" class="h-16 w-auto max-w-[240px] object-contain sm:h-[72px]">
        </a>

        <div class="w-full max-w-md overflow-hidden rounded-3xl border border-white/10 bg-night-soft/60 p-8 shadow-2xl shadow-black/50 backdrop-blur-2xl">
            <div class="text-center">
                <h1 class="text-3xl font-black tracking-tight text-white">Tạo tài khoản mới</h1>
                <p class="mt-2 text-sm text-gray-400">Gia nhập NovaPhone để trải nghiệm dịch vụ mua sắm đẳng cấp</p>
            </div>

            <form class="mt-8 space-y-5" action="{{ route('register') }}" method="POST">
                @csrf

                <div class="space-y-1.5">
                    <label for="name" class="text-xs font-bold uppercase tracking-wider text-gray-400">Họ và tên</label>
                    <input
                        id="name"
                        name="name"
                        type="text"
                        required
                        value="{{ old('name') }}"
                        placeholder="Nguyễn Văn A"
                        class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-white outline-none transition focus:border-brand-500 focus:ring-2 focus:ring-brand-500/25"
                    >
                    @error('name')
                        <p class="text-xs text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-1.5">
                    <label for="email" class="text-xs font-bold uppercase tracking-wider text-gray-400">Địa chỉ Email</label>
                    <input
                        id="email"
                        name="email"
                        type="email"
                        autocomplete="email"
                        required
                        value="{{ old('email') }}"
                        placeholder="ten@novaphone.vn"
                        class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-white outline-none transition focus:border-brand-500 focus:ring-2 focus:ring-brand-500/25"
                    >
                    @error('email')
                        <p class="text-xs text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-1.5">
                    <label for="phone" class="text-xs font-bold uppercase tracking-wider text-gray-400">Số điện thoại (tùy chọn)</label>
                    <input
                        id="phone"
                        name="phone"
                        type="tel"
                        value="{{ old('phone') }}"
                        placeholder="09xxxxxxxx"
                        class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-white outline-none transition focus:border-brand-500 focus:ring-2 focus:ring-brand-500/25"
                    >
                    @error('phone')
                        <p class="text-xs text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-1.5">
                    <label for="password" class="text-xs font-bold uppercase tracking-wider text-gray-400">Mật khẩu</label>
                    <div class="relative">
                        <input
                            id="password"
                            name="password"
                            type="password"
                            autocomplete="new-password"
                            required
                            placeholder="••••••••"
                            class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-3 pr-12 text-sm text-white outline-none transition focus:border-brand-500 focus:ring-2 focus:ring-brand-500/25"
                        >
                        <button type="button" data-toggle-password aria-label="Hiện hoặc ẩn mật khẩu" class="absolute inset-y-0 right-0 flex items-center px-4 text-gray-500 transition hover:text-white">
                            <svg class="icon-eye size-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.04 12.32a1 1 0 0 1 0-.64C3.42 7.51 7.36 4.5 12 4.5s8.57 3.01 9.96 7.18c.07.21.07.43 0 .64C20.58 16.49 16.64 19.5 12 19.5S3.43 16.49 2.04 12.32Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/></svg>
                            <svg class="icon-eye-slash hidden size-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m3 3 18 18M10.58 10.59a2 2 0 0 0 2.83 2.83M9.88 4.51A10.5 10.5 0 0 1 12 4.5c4.64 0 8.57 3.01 9.96 7.18.07.21.07.43 0 .64a10.53 10.53 0 0 1-2.1 3.66M6.23 6.23a10.48 10.48 0 0 0-4.19 5.45 1 1 0 0 0 0 .64C3.42 16.49 7.36 19.5 12 19.5a10.5 10.5 0 0 0 3.77-.7"/></svg>
                        </button>
                    </div>
                    @error('password')
                        <p class="text-xs text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-1.5">
                    <label for="password_confirmation" class="text-xs font-bold uppercase tracking-wider text-gray-400">Xác nhận mật khẩu</label>
                    <div class="relative">
                        <input
                            id="password_confirmation"
                            name="password_confirmation"
                            type="password"
                            autocomplete="new-password"
                            required
                            placeholder="••••••••"
                            class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-3 pr-12 text-sm text-white outline-none transition focus:border-brand-500 focus:ring-2 focus:ring-brand-500/25"
                        >
                        <button type="button" data-toggle-password aria-label="Hiện hoặc ẩn mật khẩu" class="absolute inset-y-0 right-0 flex items-center px-4 text-gray-500 transition hover:text-white">
                            <svg class="icon-eye size-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.04 12.32a1 1 0 0 1 0-.64C3.42 7.51 7.36 4.5 12 4.5s8.57 3.01 9.96 7.18c.07.21.07.43 0 .64C20.58 16.49 16.64 19.5 12 19.5S3.43 16.49 2.04 12.32Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/></svg>
                            <svg class="icon-eye-slash hidden size-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m3 3 18 18M10.58 10.59a2 2 0 0 0 2.83 2.83M9.88 4.51A10.5 10.5 0 0 1 12 4.5c4.64 0 8.57 3.01 9.96 7.18.07.21.07.43 0 .64a10.53 10.53 0 0 1-2.1 3.66M6.23 6.23a10.48 10.48 0 0 0-4.19 5.45 1 1 0 0 0 0 .64C3.42 16.49 7.36 19.5 12 19.5a10.5 10.5 0 0 0 3.77-.7"/></svg>
                        </button>
                    </div>
                </div>

                <div class="flex flex-col gap-1">
                    <label class="flex items-start gap-2 text-sm text-gray-400">
                        <input
                            id="terms"
                            name="terms"
                            type="checkbox"
                            required
                            class="mt-0.5 size-4 rounded border-white/10 bg-white/5 text-brand-600 focus:ring-brand-500/30"
                        >
                        <span>
                            Tôi đồng ý với <a href="#" class="font-bold text-brand-400 transition hover:text-brand-300">Điều khoản dịch vụ</a> và <a href="#" class="font-bold text-brand-400 transition hover:text-brand-300">Chính sách bảo mật</a>
                        </span>
                    </label>
                    @error('terms')
                        <p class="text-xs text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" class="w-full rounded-xl bg-gradient-to-r from-brand-600 to-brand-500 py-3 text-sm font-bold text-white shadow-lg shadow-brand-600/25 transition hover:-translate-y-0.5">
                    Đăng ký
                </button>
            </form>

            <div class="relative my-6 flex items-center justify-center">
                <div class="absolute inset-x-0 h-px bg-white/10"></div>
                <span class="relative bg-night-soft px-3 text-xs uppercase tracking-wider text-gray-500">Hoặc đăng ký bằng</span>
            </div>

            <div class="flex flex-col gap-3">
                <a href="{{ route('google.login') }}" class="flex w-full items-center justify-center gap-3 rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-sm font-semibold text-white transition hover:border-white/20 hover:bg-white/10">
                    <svg class="size-5" viewBox="0 0 24 24"><path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 0 1-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09Z"/><path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84A11 11 0 0 0 12 23Z"/><path fill="#FBBC05" d="M5.84 14.09A6.6 6.6 0 0 1 5.49 12c0-.73.13-1.43.35-2.09V7.07H2.18A11 11 0 0 0 1 12c0 1.78.43 3.45 1.18 4.93l3.66-2.84Z"/><path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15A10.6 10.6 0 0 0 12 1 11 11 0 0 0 2.18 7.07l3.66 2.84C6.71 7.31 9.14 5.38 12 5.38Z"/></svg>
                    Google
                </a>
                <a href="{{ route('auth.social.redirect', ['provider' => 'facebook']) }}" class="flex w-full items-center justify-center gap-3 rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-sm font-semibold text-white transition hover:border-white/20 hover:bg-white/10">
                    <svg class="size-5" viewBox="0 0 24 24" fill="#1877F2"><path d="M24 12.073C24 5.405 18.627 0 12 0S0 5.405 0 12.073C0 18.1 4.388 23.094 10.125 24v-8.437H7.078v-3.49h3.047v-2.66c0-3.025 1.792-4.697 4.533-4.697 1.312 0 2.686.236 2.686.236v2.971H15.83c-1.491 0-1.956.93-1.956 1.886v2.264h3.328l-.532 3.49h-2.796V24C19.612 23.094 24 18.1 24 12.073Z"/></svg>
                    Facebook
                </a>
            </div>

            <p class="mt-7 text-center text-sm text-gray-500">
                Đã có tài khoản?
                <a href="{{ route('login') }}" class="font-bold text-brand-400 transition hover:text-brand-300">Đăng nhập</a>
            </p>
        </div>
    </div>
</main>
</body>
</html>
