<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'NovaPhone')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-[#191919] text-[#161616] antialiased">
    <div class="relative min-h-screen overflow-hidden px-4 py-4 sm:px-6 sm:py-6 lg:px-8 lg:py-8">
        <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_15%_15%,rgba(10,132,255,.15),transparent_32%),radial-gradient(circle_at_85%_85%,rgba(255,255,255,.08),transparent_25%)]"></div>

        <div class="relative mx-auto flex min-h-[calc(100vh-2rem)] max-w-[1180px] flex-col sm:min-h-[calc(100vh-3rem)]">
            {{-- <header class="flex items-center justify-between py-2 sm:py-3">
                <a href="{{ route('home') }}" class="inline-flex items-center gap-2.5 text-white" aria-label="NovaPhone">
                    <img src="{{ asset('images/brand/novaphone-mark-v2.png') }}" alt="" aria-hidden="true" width="28" height="28" class="size-7 object-contain">
                    <span class="text-sm font-semibold tracking-[-0.04em]">Nova<span class="text-[#62adff]">Phone</span></span>
                </a>
            </header> --}}
x
            <div class="mt-4 grid flex-1 overflow-hidden rounded-[26px] border border-white/10 bg-white shadow-[0_28px_90px_rgba(0,0,0,.34)] lg:grid-cols-[0.88fr_1.12fr]">
                <aside class="relative hidden overflow-hidden bg-[#f4f4f6] p-10 lg:flex lg:flex-col">
                    <div class="absolute -left-20 -top-20 size-72 rounded-full bg-[#dcecff]"></div>
                    <div class="absolute -bottom-24 -right-20 size-80 rounded-full border-[38px] border-white/70"></div>

                    <div class="relative">
                        <div class="inline-flex size-12 items-center justify-center rounded-2xl bg-white shadow-[0_12px_30px_rgba(0,0,0,.08)]">
                            <img src="{{ asset('images/brand/novaphone-mark-v2.png') }}" alt="" aria-hidden="true" width="30" height="30" class="size-8 object-contain">
                        </div>
                        <p class="mt-6 text-xs font-semibold uppercase tracking-[0.2em] text-[#0a84ff]">NovaPhone</p>
                        <h2 class="mt-3 max-w-xs text-4xl font-semibold tracking-[-0.06em] text-[#171717]">Mua sắm công nghệ, đơn giản hơn.</h2>
                        <p class="mt-4 max-w-sm text-sm leading-6 text-[#707070]">Đăng nhập hoặc tạo tài khoản để theo dõi đơn hàng, lưu ưu đãi và mua sắm nhanh hơn.</p>
                    </div>

                    <div class="relative mt-auto rounded-2xl border border-white/80 bg-white/70 p-4 backdrop-blur">
                        <p class="text-sm font-semibold text-[#222]">An toàn và minh bạch</p>
                        <p class="mt-1 text-xs leading-5 text-[#777]">Thông tin tài khoản được xử lý theo các kiểm tra bảo mật của NovaPhone.</p>
                    </div>
                </aside>

                <main class="flex items-center bg-white px-5 py-10 sm:px-10 lg:px-14">
                    <div class="mx-auto w-full max-w-[510px]">
                        @if (session('success'))
                            <div data-auth-alert class="mb-5 rounded-2xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800" role="status">{{ session('success') }}</div>
                        @endif
                        @if (session('error'))
                            <div data-auth-alert class="mb-5 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800" role="alert">{{ session('error') }}</div>
                        @endif
                        @if ($errors->any())
                            <div data-auth-alert class="mb-5 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800" role="alert">Vui lòng kiểm tra lại các trường được đánh dấu.</div>
                        @endif

                        @yield('content')
                    </div>
                </main>
            </div>
        </div>
    </div>
</body>
</html>
