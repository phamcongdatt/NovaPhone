<!DOCTYPE html>
<html lang="vi" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Xác thực') — NovaPhone</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen overflow-x-hidden bg-[#02030a] font-sans text-white antialiased">
    <main class="relative flex min-h-screen items-center justify-center overflow-hidden px-4 py-10">
        <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_36%_38%,rgba(79,70,229,.15),transparent_34%),radial-gradient(circle_at_70%_76%,rgba(6,182,212,.10),transparent_28%)]"></div>
        <div class="pointer-events-none absolute left-1/2 top-1/2 h-[540px] w-[540px] -translate-x-1/2 -translate-y-1/2 rounded-full bg-blue-600/[0.06] blur-3xl"></div>

        <div class="relative z-10 w-full max-w-md">
            <a href="{{ route('home') }}" class="mx-auto mb-7 flex w-fit items-center" aria-label="Về trang chủ NovaPhone">
                <img src="{{ asset('images/brand/nova-phone-logo.webp') }}" alt="NovaPhone" class="h-16 w-auto object-contain">
            </a>

            <section class="rounded-2xl border border-blue-400/70 bg-[#090a16]/95 p-7 shadow-[0_0_0_1px_rgba(34,211,238,.18),0_0_35px_rgba(37,99,235,.28)] sm:p-9">
                @yield('content')
            </section>

            <p class="mt-6 text-center text-xs text-slate-600">© {{ date('Y') }} NovaPhone. Mọi quyền được bảo lưu.</p>
        </div>
    </main>
</body>
</html>
