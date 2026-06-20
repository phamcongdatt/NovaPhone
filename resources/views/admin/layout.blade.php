<!DOCTYPE html>
<html lang="vi" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — NovaPhone Admin</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-night font-sans text-gray-100 antialiased">
@php
    $navItems = [
        ['label' => 'Dashboard', 'route' => 'admin.dashboard', 'active' => 'admin.dashboard', 'chevron' => false,
         'icon' => 'M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25Z'],
        ['label' => 'Sản phẩm', 'route' => 'admin.products.index', 'active' => 'admin.products.*', 'chevron' => true,
         'icon' => 'M21 7.5l-9-5.25L3 7.5m18 0-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9.75v9.75'],
        ['label' => 'Danh mục', 'route' => null, 'active' => null, 'chevron' => true,
         'icon' => 'M2.25 12.76c0 1.6 1.123 2.994 2.707 3.227 1.087.16 2.185.283 3.293.369V21l4.184-4.183a1.14 1.14 0 0 1 .778-.332 48.294 48.294 0 0 0 5.83-.498c1.585-.233 2.708-1.626 2.708-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0 0 12 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018Z'],
        ['label' => 'Đơn hàng', 'route' => null, 'active' => null, 'chevron' => true,
         'icon' => 'M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z'],
        ['label' => 'Khách hàng', 'route' => null, 'active' => null, 'chevron' => true,
         'icon' => 'M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z'],
        ['label' => 'Bình luận', 'route' => 'admin.reviews.index', 'active' => 'admin.reviews.*', 'chevron' => true,
         'icon' => 'M2.25 12.76c0 1.6 1.123 2.994 2.707 3.227 1.068.157 2.148.279 3.238.364.466.037.893.281 1.153.671L12 21l2.652-3.978c.26-.39.687-.634 1.153-.67 1.09-.086 2.17-.208 3.238-.365 1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0 0 12 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018Z'],
        ['label' => 'Báo cáo', 'route' => null, 'active' => null, 'chevron' => false,
         'icon' => 'M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z'],
        ['label' => 'Cài đặt', 'route' => null, 'active' => null, 'chevron' => false,
         'icon' => 'M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 0 1 0 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 0 1 0-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 0 1-.26-1.43l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28Z M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z'],
    ];
@endphp

    {{-- Lớp phủ khi mở sidebar trên mobile --}}
    <div data-sidebar-backdrop class="fixed inset-0 z-30 hidden bg-black/60 backdrop-blur-sm lg:hidden"></div>

    <div class="flex min-h-screen">

        {{-- ============== Sidebar ============== --}}
        <aside data-sidebar
               class="fixed inset-y-0 left-0 z-40 flex w-64 -translate-x-full flex-col border-r border-white/5 bg-night-soft transition-transform duration-300 ease-in-out lg:static lg:translate-x-0">

            {{-- Logo --}}
            <div class="flex h-16 shrink-0 items-center gap-2.5 border-b border-white/5 px-5">
                <span class="flex size-9 items-center justify-center rounded-xl bg-gradient-to-br from-brand-500 to-brand-700 shadow-lg shadow-brand-600/30">
                    <svg class="size-5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 0 0 6 3.75v16.5a2.25 2.25 0 0 0 2.25 2.25h7.5A2.25 2.25 0 0 0 18 20.25V3.75a2.25 2.25 0 0 0-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3"/></svg>
                </span>
                <span class="text-lg font-extrabold leading-none tracking-tight">
                    NOVA<span class="text-brand-500">PHONE</span>
                </span>
            </div>

            {{-- Navigation --}}
            <nav class="flex-1 space-y-1 overflow-y-auto p-3 no-scrollbar">
                @foreach ($navItems as $item)
                    @php
                        $isActive = $item['active'] && request()->routeIs($item['active']);
                        $href     = $item['route'] ? route($item['route']) : '#';
                    @endphp
                    <a href="{{ $href }}"
                       class="group flex items-center gap-3 rounded-xl px-3.5 py-2.5 text-sm font-semibold transition-all duration-200
                              {{ $isActive
                                  ? 'bg-brand-600 text-white shadow-md shadow-brand-600/25'
                                  : 'text-gray-400 hover:bg-white/5 hover:text-white' }}">
                        <svg class="size-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $item['icon'] }}"/>
                        </svg>
                        <span class="flex-1">{{ $item['label'] }}</span>
                        @if ($item['chevron'])
                            <svg class="size-4 shrink-0 opacity-50 transition group-hover:translate-x-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/></svg>
                        @endif
                    </a>
                @endforeach
            </nav>
        </aside>

        {{-- ============== Main ============== --}}
        <div class="flex min-w-0 flex-1 flex-col">

            {{-- Topbar --}}
            <header class="sticky top-0 z-20 flex h-16 items-center gap-3 border-b border-white/5 bg-night/85 px-4 backdrop-blur-xl sm:px-6">
                <button data-sidebar-toggle class="rounded-lg p-2 text-gray-400 transition hover:bg-white/5 hover:text-white">
                    <svg class="size-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/></svg>
                </button>

                <div class="min-w-0">
                    <h1 class="truncate text-lg font-bold leading-tight text-white">@yield('page-title', 'Dashboard')</h1>
                    <p class="truncate text-xs text-gray-500">@yield('page-subtitle', 'Tổng quan hệ thống')</p>
                </div>

                {{-- Search --}}
                <div class="relative ml-auto hidden max-w-md flex-1 md:block">
                    <svg class="pointer-events-none absolute left-3.5 top-1/2 size-4 -translate-y-1/2 text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/></svg>
                    <input type="text" placeholder="Tìm kiếm đơn hàng, sản phẩm, khách hàng..."
                           class="w-full rounded-xl border border-white/10 bg-white/5 py-2.5 pl-10 pr-16 text-sm text-gray-200 placeholder:text-gray-500 transition focus:border-brand-500/50 focus:bg-white/[0.07] focus:outline-none">
                    <kbd class="absolute right-3 top-1/2 -translate-y-1/2 rounded-md border border-white/10 bg-white/5 px-2 py-0.5 text-[10px] font-semibold text-gray-500">Ctrl + K</kbd>
                </div>

                <div class="ml-auto flex items-center gap-1.5 md:ml-3">
                    {{-- Thông báo --}}
                    <button class="relative rounded-xl p-2.5 text-gray-400 transition hover:bg-white/5 hover:text-white">
                        <svg class="size-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0"/></svg>
                        <span class="absolute right-1.5 top-1.5 flex size-4 items-center justify-center rounded-full bg-red-500 text-[9px] font-bold text-white">5</span>
                    </button>
                    {{-- Tin nhắn --}}
                    <button class="relative rounded-xl p-2.5 text-gray-400 transition hover:bg-white/5 hover:text-white">
                        <svg class="size-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.625 12a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H8.25m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H12m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 0 1-2.555-.337A5.972 5.972 0 0 1 5.41 20.97a5.969 5.969 0 0 1-.474-.065 4.48 4.48 0 0 0 .978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25Z"/></svg>
                        <span class="absolute right-1.5 top-1.5 flex size-4 items-center justify-center rounded-full bg-brand-500 text-[9px] font-bold text-white">3</span>
                    </button>

                    {{-- Avatar --}}
                    <div class="relative" data-dropdown>
                        <button data-dropdown-toggle class="flex items-center gap-2.5 rounded-xl py-1.5 pl-1.5 pr-2 transition hover:bg-white/5">
                            <span class="flex size-9 items-center justify-center rounded-xl bg-gradient-to-br from-brand-500 to-brand-700 text-sm font-bold text-white">
                                {{ mb_strtoupper(mb_substr(Auth::user()->name ?? 'A', 0, 1)) }}
                            </span>
                            <span class="hidden text-left leading-tight sm:block">
                                <span class="block text-sm font-bold text-white">{{ Auth::user()->name ?? 'Admin' }}</span>
                                <span class="block text-xs text-gray-500">Quản trị viên</span>
                            </span>
                            <svg class="hidden size-4 text-gray-500 sm:block" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5"/></svg>
                        </button>
                        <div data-dropdown-menu
                             class="absolute right-0 mt-2 hidden w-48 overflow-hidden rounded-xl border border-white/10 bg-night-card shadow-2xl shadow-black/50">
                            <a href="{{ route('home') }}" class="block px-4 py-2.5 text-sm text-gray-300 transition hover:bg-white/5 hover:text-white">Về trang chủ</a>
                            <a href="{{ route('account.show') }}" class="block px-4 py-2.5 text-sm text-gray-300 transition hover:bg-white/5 hover:text-white">Tài khoản</a>
                            <form method="POST" action="{{ route('logout') }}" class="border-t border-white/5">
                                @csrf
                                <button class="block w-full px-4 py-2.5 text-left text-sm text-red-400 transition hover:bg-red-500/10">Đăng xuất</button>
                            </form>
                        </div>
                    </div>
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

    {{-- Tương tác: sidebar mobile + dropdown --}}
    <script>
        (function () {
            const sidebar  = document.querySelector('[data-sidebar]');
            const backdrop = document.querySelector('[data-sidebar-backdrop]');
            const toggle   = document.querySelector('[data-sidebar-toggle]');

            function openSidebar()  { sidebar.classList.remove('-translate-x-full'); backdrop.classList.remove('hidden'); }
            function closeSidebar() { sidebar.classList.add('-translate-x-full');   backdrop.classList.add('hidden'); }

            toggle?.addEventListener('click', () => {
                sidebar.classList.contains('-translate-x-full') ? openSidebar() : closeSidebar();
            });
            backdrop?.addEventListener('click', closeSidebar);

            // Dropdown avatar
            const dd = document.querySelector('[data-dropdown]');
            const ddToggle = dd?.querySelector('[data-dropdown-toggle]');
            const ddMenu = dd?.querySelector('[data-dropdown-menu]');
            ddToggle?.addEventListener('click', (e) => { e.stopPropagation(); ddMenu.classList.toggle('hidden'); });
            document.addEventListener('click', (e) => {
                if (dd && !dd.contains(e.target)) ddMenu.classList.add('hidden');
            });
        })();
    </script>

    @stack('scripts')
</body>
</html>
