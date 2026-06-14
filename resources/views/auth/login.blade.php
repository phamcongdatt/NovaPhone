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
            <a href="{{ route('home') }}" class="mb-7 flex items-center justify-center gap-2.5">
                <span class="flex size-9 items-center justify-center rounded-xl bg-brand-600 text-base font-extrabold text-white shadow-lg shadow-brand-600/30">N</span>
                <span class="text-lg font-extrabold tracking-tight">Nova<span class="text-brand-500">Phone</span></span>
            </a>

            <h1 class="text-center text-3xl font-extrabold tracking-tight text-white">Đăng nhập tài khoản</h1>

            <form method="POST" action="{{ route('login') }}" class="mt-7 space-y-4" novalidate>
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
</html>
