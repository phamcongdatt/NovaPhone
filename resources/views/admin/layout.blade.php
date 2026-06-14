<!DOCTYPE html>
<html lang="vi" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Quản trị') — NovaPhone Admin</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-night font-sans text-gray-100 antialiased">

    <div class="flex min-h-screen">

        {{-- ═══════════════ Sidebar ═══════════════ --}}
        <aside class="hidden w-64 shrink-0 border-r border-white/5 bg-night-soft lg:block">
            <div class="flex h-16 items-center gap-2.5 border-b border-white/5 px-5">
                <span class="flex size-9 items-center justify-center rounded-xl bg-brand-600 text-base font-extrabold text-white">N</span>
                <span class="text-lg font-extrabold tracking-tight">Nova<span class="text-brand-500">Admin</span></span>
            </div>

            <nav class="space-y-1 p-3">
                <a href="{{ route('home') }}"
                   class="flex items-center gap-3 rounded-xl px-3.5 py-2.5 text-sm font-semibold text-gray-400 transition-all duration-200 hover:bg-white/5 hover:text-white">
                    <svg class="size-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12 11.204 3.045a1.125 1.125 0 0 1 1.59 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75"/></svg>
                    Về trang chủ
                </a>

                <a href="{{ route('admin.products.index') }}"
                   class="flex items-center gap-3 rounded-xl px-3.5 py-2.5 text-sm font-semibold transition-all duration-200
                          {{ request()->routeIs('admin.products.*') ? 'bg-brand-600 text-white shadow-md shadow-brand-600/25' : 'text-gray-400 hover:bg-white/5 hover:text-white' }}">
                    <svg class="size-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 7.5l-9-5.25L3 7.5m18 0-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9.75v9.75"/></svg>
                    Sản phẩm
                </a>

                <a href="#"
                   class="flex items-center gap-3 rounded-xl px-3.5 py-2.5 text-sm font-semibold text-gray-400 transition-all duration-200 hover:bg-white/5 hover:text-white">
                    <svg class="size-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z"/></svg>
                    Đơn hàng
                </a>

                <a href="#"
                   class="flex items-center gap-3 rounded-xl px-3.5 py-2.5 text-sm font-semibold text-gray-400 transition-all duration-200 hover:bg-white/5 hover:text-white">
                    <svg class="size-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z"/></svg>
                    Người dùng
                </a>
            </nav>
        </aside>

        {{-- ═══════════════ Main ═══════════════ --}}
        <div class="flex min-w-0 flex-1 flex-col">

            {{-- Topbar --}}
            <header class="flex h-16 items-center justify-between border-b border-white/5 bg-night/85 px-4 backdrop-blur-xl sm:px-6">
                <h1 class="text-lg font-bold text-white">@yield('page-title', 'Quản trị')</h1>
                <div class="flex items-center gap-3">
                    <span class="text-sm text-gray-400">
                        Xin chào, <strong class="text-white">{{ Auth::user()->name }}</strong>
                    </span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="rounded-lg border border-white/10 px-3 py-1.5 text-xs font-semibold text-gray-400 transition-all duration-200 hover:border-red-500/40 hover:text-red-400">
                            Đăng xuất
                        </button>
                    </form>
                </div>
            </header>

            {{-- Content --}}
            <main class="flex-1 p-4 sm:p-6">
                @if (session('success'))
                    <div class="mb-4 rounded-xl border border-green-500/20 bg-green-500/10 px-4 py-3 text-sm text-green-400">
                        {{ session('success') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-4 rounded-xl border border-red-500/20 bg-red-500/10 px-4 py-3 text-sm text-red-400">
                        <ul class="list-inside list-disc space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    @stack('scripts')
</body>
</html>