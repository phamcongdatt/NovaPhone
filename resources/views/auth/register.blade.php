<!DOCTYPE html>
<html lang="vi" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Đăng ký tài khoản — NovaPhone</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-night font-sans text-gray-100 antialiased">

<div class="flex min-h-screen items-center justify-center p-4 sm:p-6 lg:p-10">
    <div class="relative w-full max-w-6xl">

        {{-- ===== Viền phát sáng quanh khung ===== --}}
        <div class="pointer-events-none absolute -inset-[2px] rounded-[2.1rem] bg-gradient-to-r from-brand-500/60 via-indigo-400/50 to-brand-500/60 opacity-80 blur-[10px]"></div>
        <div class="pointer-events-none absolute -inset-6 rounded-[2.5rem] bg-gradient-to-r from-brand-600/25 via-indigo-500/20 to-brand-600/25 opacity-70 blur-3xl"></div>

        {{-- ===== Khung chính ===== --}}
        <div class="relative grid overflow-hidden rounded-[2rem] border border-white/15 bg-night-soft shadow-2xl shadow-black/70 lg:grid-cols-2">

            {{-- ========== CỘT TRÁI: FORM (có đường chia dọc bên phải) ========== --}}
            <div class="flex items-center justify-center px-6 py-10 sm:px-10 lg:border-r lg:border-white/10">
                <div class="w-full max-w-md">

                    {{-- Logo --}}
                    <a href="{{ route('home') }}" class="mb-7 flex items-center justify-center gap-2.5">
                        <span class="flex size-9 items-center justify-center rounded-xl bg-brand-600 text-base font-extrabold text-white shadow-lg shadow-brand-600/30">N</span>
                        <span class="text-lg font-extrabold tracking-tight">Nova<span class="text-brand-500">Phone</span></span>
                    </a>

                    <h1 class="text-center text-3xl font-extrabold tracking-tight text-white">Đăng ký tài khoản</h1>

                    <form method="POST" action="{{ route('register') }}" class="mt-7 space-y-4" novalidate>
                        @csrf

                        {{-- Họ và tên --}}
                        <div>
                            <div class="relative">
                                <span class="pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-500">
                                    <svg class="size-5" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.5 19.5a7.5 7.5 0 0 1 15 0v.25H4.5v-.25Z"/></svg>
                                </span>
                                <input id="name" name="name" type="text" value="{{ old('name') }}" autocomplete="name" autofocus placeholder="Họ và tên"
                                       class="w-full rounded-xl border bg-white/[0.04] py-3 pl-11 pr-4 text-sm text-white outline-none transition-all duration-200 ease-in-out placeholder:text-gray-500 focus:bg-white/[0.07] focus:ring-2 @error('name') border-red-500/70 focus:ring-red-500/25 @else border-white/10 focus:border-amber-400 focus:ring-amber-400/20 @enderror">
                            </div>
                            @error('name') <p class="mt-1.5 pl-1 text-xs font-medium text-red-400">{{ $message }}</p> @enderror
                        </div>

                        {{-- Email --}}
                        <div>
                            <div class="relative">
                                <span class="pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-500">
                                    <svg class="size-5" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25H4.5a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5H4.5A2.25 2.25 0 0 0 2.25 6.75m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75"/></svg>
                                </span>
                                <input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" placeholder="Email"
                                       class="w-full rounded-xl border bg-white/[0.04] py-3 pl-11 pr-4 text-sm text-white outline-none transition-all duration-200 ease-in-out placeholder:text-gray-500 focus:bg-white/[0.07] focus:ring-2 @error('email') border-red-500/70 focus:ring-red-500/25 @else border-white/10 focus:border-amber-400 focus:ring-amber-400/20 @enderror">
                            </div>
                            @error('email') <p class="mt-1.5 pl-1 text-xs font-medium text-red-400">{{ $message }}</p> @enderror
                        </div>

                        {{-- Số điện thoại --}}
                        <div>
                            <div class="relative">
                                <span class="pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-500">
                                    <svg class="size-5" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.28 6.72 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.37c0-.52-.35-.97-.85-1.09l-4.42-1.11c-.44-.11-.9.06-1.18.42l-.97 1.29c-.28.38-.77.54-1.21.38a12.04 12.04 0 0 1-7.14-7.14c-.16-.44 0-.93.38-1.21l1.29-.97c.36-.27.53-.73.42-1.17L6.96 3.1a1.13 1.13 0 0 0-1.09-.85H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z"/></svg>
                                </span>
                                <input id="phone" name="phone" type="tel" value="{{ old('phone') }}" autocomplete="tel" placeholder="Số điện thoại"
                                       class="w-full rounded-xl border bg-white/[0.04] py-3 pl-11 pr-4 text-sm text-white outline-none transition-all duration-200 ease-in-out placeholder:text-gray-500 focus:bg-white/[0.07] focus:ring-2 @error('phone') border-red-500/70 focus:ring-red-500/25 @else border-white/10 focus:border-amber-400 focus:ring-amber-400/20 @enderror">
                            </div>
                            @error('phone') <p class="mt-1.5 pl-1 text-xs font-medium text-red-400">{{ $message }}</p> @enderror
                        </div>

                        {{-- Mật khẩu --}}
                        <div>
                            <div class="relative">
                                <span class="pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-500">
                                    <svg class="size-5" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 0h10.5a1.5 1.5 0 0 1 1.5 1.5v6a1.5 1.5 0 0 1-1.5 1.5H6.75a1.5 1.5 0 0 1-1.5-1.5v-6a1.5 1.5 0 0 1 1.5-1.5Z"/></svg>
                                </span>
                                <input id="password" name="password" type="password" autocomplete="new-password" placeholder="Mật khẩu"
                                       class="w-full rounded-xl border bg-white/[0.04] py-3 pl-11 pr-4 text-sm text-white outline-none transition-all duration-200 ease-in-out placeholder:text-gray-500 focus:bg-white/[0.07] focus:ring-2 @error('password') border-red-500/70 focus:ring-red-500/25 @else border-white/10 focus:border-amber-400 focus:ring-amber-400/20 @enderror">
                            </div>
                            @error('password') <p class="mt-1.5 pl-1 text-xs font-medium text-red-400">{{ $message }}</p> @enderror
                        </div>

                        {{-- Xác nhận mật khẩu --}}
                        <div>
                            <div class="relative">
                                <span class="pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-500">
                                    <svg class="size-5" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.96 11.96 0 0 1 3.6 6.082a11.99 11.99 0 0 0-.58 3.668c0 5.42 3.71 9.98 8.74 11.27.16.04.32.04.48 0 5.03-1.29 8.74-5.85 8.74-11.27 0-1.27-.2-2.49-.58-3.67A11.96 11.96 0 0 1 12 2.713Z"/></svg>
                                </span>
                                <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" placeholder="Xác nhận mật khẩu"
                                       class="w-full rounded-xl border border-white/10 bg-white/[0.04] py-3 pl-11 pr-4 text-sm text-white outline-none transition-all duration-200 ease-in-out placeholder:text-gray-500 focus:border-amber-400 focus:bg-white/[0.07] focus:ring-2 focus:ring-amber-400/20">
                            </div>
                        </div>

                        {{-- Điều khoản --}}
                        <div>
                            <label class="flex items-start gap-2.5 text-xs text-gray-400">
                                <input type="checkbox" name="terms" value="1" {{ old('terms') ? 'checked' : '' }}
                                       class="mt-0.5 size-4 shrink-0 rounded border-white/20 bg-white/5 text-amber-500 focus:ring-amber-400/40">
                                <span>Tôi đồng ý với <a href="#" class="font-semibold text-amber-400 hover:text-amber-300">Điều khoản sử dụng</a> và <a href="#" class="font-semibold text-amber-400 hover:text-amber-300">Chính sách bảo mật</a> của NovaPhone.</span>
                            </label>
                            @error('terms') <p class="mt-1.5 pl-1 text-xs font-medium text-red-400">{{ $message }}</p> @enderror
                        </div>

                        {{-- Nút đăng ký --}}
                        <button type="submit"
                                class="w-full rounded-xl bg-gradient-to-r from-amber-400 to-amber-500 py-3.5 text-sm font-extrabold uppercase tracking-wide text-night shadow-lg shadow-amber-500/25 transition-all duration-200 ease-in-out hover:from-amber-300 hover:to-amber-400 hover:shadow-amber-400/40 active:scale-[0.99]">
                            Đăng ký ngay
                        </button>
                    </form>

                    {{-- Divider --}}
                    <div class="my-5 flex items-center gap-3 text-xs text-gray-500">
                        <span class="h-px flex-1 bg-white/10"></span>
                        Hoặc đăng ký bằng
                        <span class="h-px flex-1 bg-white/10"></span>
                    </div>

                    {{-- Social --}}
                    <div class="grid grid-cols-2 gap-3">
                        <a href="#" class="flex items-center justify-center gap-2 rounded-xl border border-white/10 bg-white/[0.04] py-2.5 text-sm font-semibold text-gray-200 transition-all duration-200 ease-in-out hover:bg-white/[0.08]">
                            <svg class="size-5" viewBox="0 0 24 24"><path fill="#FFC107" d="M43.6 20.5H42V20H24v8h11.3C33.7 32.4 29.3 35 24 35c-6.6 0-12-5.4-12-12s5.4-12 12-12c3.1 0 5.9 1.2 8 3.1l5.7-5.7C34.5 3.5 29.5 1.5 24 1.5 11.6 1.5 1.5 11.6 1.5 24S11.6 46.5 24 46.5 46.5 36.4 46.5 24c0-1.2-.1-2.3-.4-3.5z"/><path fill="#FF3D00" d="m4.3 14.7 6.6 4.8C12.7 15.1 17.9 11.5 24 11.5c3.1 0 5.9 1.2 8 3.1l5.7-5.7C34.5 5.5 29.5 3.5 24 3.5 16 3.5 9 8 4.3 14.7z" transform="scale(.96) translate(.5 .5)"/><path fill="#4CAF50" d="M24 46.5c5.2 0 10-1.9 13.6-5.2l-6.3-5.3C29.3 37.4 26.8 38.5 24 38.5c-5.3 0-9.7-2.6-11.3-7l-6.5 5C9 42 16 46.5 24 46.5z"/><path fill="#1976D2" d="M43.6 20.5H42V20H24v8h11.3c-.8 2.2-2.2 4.1-4 5.5l6.3 5.3C40.9 36 46.5 30.8 46.5 24c0-1.2-.1-2.3-.9-3.5z"/></svg>
                            Google
                        </a>
                        <a href="#" class="flex items-center justify-center gap-2 rounded-xl border border-white/10 bg-white/[0.04] py-2.5 text-sm font-semibold text-gray-200 transition-all duration-200 ease-in-out hover:bg-white/[0.08]">
                            <svg class="size-5" fill="#1877F2" viewBox="0 0 24 24"><path d="M24 12.07C24 5.41 18.63 0 12 0S0 5.4 0 12.07C0 18.1 4.39 23.1 10.13 24v-8.44H7.08v-3.49h3.04V9.41c0-3.02 1.8-4.7 4.54-4.7 1.31 0 2.68.24 2.68.24v2.97h-1.5c-1.5 0-1.96.93-1.96 1.89v2.26h3.32l-.53 3.5h-2.8V24C19.62 23.1 24 18.1 24 12.07Z"/></svg>
                            Facebook
                        </a>
                    </div>

                    <p class="mt-7 text-center text-sm text-gray-400">
                        Đã có tài khoản?
                        <a href="#" class="font-semibold text-amber-400 transition-colors duration-200 hover:text-amber-300">Đăng nhập ngay</a>
                    </p>
                </div>
            </div>

            {{-- ========== CỘT PHẢI: MARKETING ========== --}}
            <div class="relative hidden overflow-hidden px-12 py-12 lg:flex lg:flex-col lg:justify-center">

                {{-- Quầng sáng sau điện thoại --}}
                <div class="pointer-events-none absolute right-0 top-1/2 size-[460px] -translate-y-1/2 translate-x-1/4 rounded-full bg-brand-500/20 blur-[120px]"></div>
                <div class="pointer-events-none absolute right-16 top-20 size-72 rounded-full bg-indigo-400/10 blur-[100px]"></div>

                <div class="relative z-10">
                    <h2 class="text-3xl font-extrabold leading-tight tracking-tight text-white">Gia nhập NovaPhone</h2>
                    <p class="mt-3 max-w-md text-sm leading-relaxed text-gray-400">
                        Khám phá thế giới điện thoại flagship chính hãng cùng hàng nghìn ưu đãi độc quyền dành riêng cho thành viên. Mua sắm dễ dàng — trải nghiệm đẳng cấp.
                    </p>

                    {{-- Ảnh sản phẩm + hiệu ứng phát sáng --}}
                    <div class="relative my-10 flex items-center justify-center">
                        <div class="pointer-events-none absolute inset-0 -z-0 mx-auto size-72 rounded-full bg-brand-500/25 blur-[90px]"></div>
                        <img src="{{ asset('storage/phones.png') }}" alt="Điện thoại flagship NovaPhone"
                             class="relative z-10 w-full max-w-md drop-shadow-2xl">
                    </div>

                    {{-- Tính năng --}}
                    <div class="grid grid-cols-3 gap-4">
                        @foreach ([
                            ['M11.48 3.5a.56.56 0 0 1 1.04 0l2.13 4.31 4.76.69c.5.07.7.69.34 1.04l-3.44 3.36.81 4.74c.09.5-.44.88-.88.65L12 16.7l-4.25 2.24c-.45.23-.97-.15-.88-.65l.81-4.74-3.44-3.36c-.36-.35-.16-.97.34-1.04l4.76-.69 2.14-4.31Z', 'Tích điểm', 'Mỗi đơn hàng'],
                            ['M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Z', 'Ưu đãi', 'Độc quyền'],
                            ['M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z', 'Bảo hành', 'Ưu tiên 1-1'],
                        ] as $f)
                            <div class="rounded-2xl border border-white/5 bg-white/[0.03] p-4 text-center">
                                <span class="mx-auto mb-2 flex size-10 items-center justify-center rounded-xl bg-amber-400/15 text-amber-400">
                                    <svg class="size-5" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $f[0] }}"/></svg>
                                </span>
                                <p class="text-sm font-bold text-white">{{ $f[1] }}</p>
                                <p class="mt-0.5 text-[11px] text-gray-500">{{ $f[2] }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>