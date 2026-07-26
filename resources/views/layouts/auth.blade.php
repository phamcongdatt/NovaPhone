<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'NovaPhone')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#f5f4f1] text-[#161616] antialiased">
    <div class="mx-auto max-w-[760px] px-4 py-8">
        <div class="mb-4 flex items-center justify-center">
            <a href="{{ route('home') }}" class="inline-flex items-center gap-2 rounded-full border border-[#e7e3dd] bg-white px-4 py-2 shadow-sm" aria-label="NovaPhone">
                <img src="{{ asset('images/brand/novaphone-mark-v2.png') }}" alt="" aria-hidden="true" width="24" height="24" class="size-6 object-contain">
                <span class="text-sm font-semibold tracking-[-0.04em] text-[#111827]">Nova<span class="text-[#0a84ff]">Phone</span></span>
            </a>
        </div>
        @yield('content')
    </div>
    @stack('scripts')
</body>
</html>
