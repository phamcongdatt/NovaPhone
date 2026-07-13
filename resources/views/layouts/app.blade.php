<!DOCTYPE html>
<html lang="vi" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'NovaPhone — Hệ thống bán lẻ điện thoại chính hãng')</title>

    {{-- Stub chạy trước module app.js: gom callback biểu đồ vào hàng đợi để charts.js rút ra sau. --}}
    <script>
        window.__novaChartQueue = [];
        window.novaChart = (cb) => window.__novaChartQueue.push({ lib: 'apex', cb });
        window.novaChartJs = (cb) => window.__novaChartQueue.push({ lib: 'chartjs', cb });
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-night font-sans text-gray-100 antialiased">

    {{-- ===================== Topbar dịch vụ ===================== --}}
    <div class="hidden border-b border-white/5 bg-night-soft text-[11px] text-gray-400 lg:block">
        <div class="mx-auto flex h-9 max-w-7xl items-center justify-between px-4 sm:px-6">
            <div class="flex items-center gap-5">
                @foreach ([
                    ['icon' => 'M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.9 17.9 0 0 0-3.213-9.193 2.06 2.06 0 0 0-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 0 0-10.026 0 1.106 1.106 0 0 0-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12', 'label' => 'Miễn phí giao hàng toàn quốc'],
                    ['icon' => 'M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99', 'label' => 'Thu cũ đổi mới — Trợ giá đến 5 triệu'],
                    ['icon' => 'M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Z', 'label' => 'Trả góp 0% lãi suất'],
                ] as $item)
                    <span class="flex items-center gap-1.5">
                        <svg class="size-3.5 text-brand-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $item['icon'] }}"/></svg>
                        {{ $item['label'] }}
                    </span>
                @endforeach
            </div>
            <div class="flex items-center gap-5">
                <span class="flex items-center gap-1.5">
                    <svg class="size-3.5 text-brand-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z M19.5 10.5c0 7.14-7.5 11.25-7.5 11.25S4.5 17.64 4.5 10.5a7.5 7.5 0 1 1 15 0Z"/></svg>
                    Hệ thống 100+ cửa hàng
                </span>
                <span class="flex items-center gap-1.5">
                    <svg class="size-3.5 text-brand-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.28 6.72 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.37c0-.52-.35-.97-.85-1.09l-4.42-1.11c-.44-.11-.9.06-1.18.42l-.97 1.29c-.28.38-.77.54-1.21.38a12.04 12.04 0 0 1-7.14-7.14c-.16-.44 0-.93.38-1.21l1.29-.97c.36-.27.53-.73.42-1.17L6.96 3.1a1.13 1.13 0 0 0-1.09-.85H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z"/></svg>
                    Hỗ trợ 24/7: <a href="tel:18001234" class="font-semibold text-white transition-colors duration-200 hover:text-brand-400">1800 1234</a>
                </span>
            </div>
        </div>
    </div>

    {{-- ===================== Header chính ===================== --}}
    <header class="sticky top-0 z-50 border-b border-white/5 bg-night/85 backdrop-blur-xl">
        <div class="mx-auto flex h-16 max-w-7xl items-center gap-4 px-4 sm:px-6 lg:h-[68px] lg:gap-8">

            {{-- Logo --}}
            <a href="{{ route('home') }}" class="flex shrink-0 items-center" aria-label="NovaPhone - Trang chủ">
                <img
                    src="{{ asset('images/brand/nova-phone-logo.webp') }}"
                    alt="NovaPhone"
                    class="h-11 w-auto max-w-[180px] object-contain sm:h-12"
                >
            </a>

            {{-- Ô tìm kiếm trung tâm --}}
            <div class="relative min-w-0 flex-1" id="quick-search-container">
                <form action="{{ route('products.index') }}" method="GET" class="relative">
                    <input
                        type="search"
                        name="search"
                        id="quick-search-input"
                        autocomplete="off"
                        value="{{ request('search') }}"
                        placeholder="Bạn cần tìm gì hôm nay?"
                        class="w-full rounded-full border border-white/10 bg-white/5 py-2.5 pl-11 pr-4 text-sm text-white outline-none transition-all duration-200 ease-in-out placeholder:text-gray-500 focus:border-brand-500 focus:bg-white/[0.08] focus:ring-2 focus:ring-brand-500/25"
                    >
                    <button type="submit" class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 hover:text-brand-500 transition-colors">
                        <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35M17 10.5a6.5 6.5 0 1 1-13 0 6.5 6.5 0 0 1 13 0Z"/></svg>
                    </button>
                </form>

                {{-- Dropdown kết quả --}}
                <div id="quick-search-results" class="absolute left-0 right-0 top-full mt-2 hidden max-h-96 overflow-y-auto rounded-xl border border-white/10 bg-night-soft shadow-2xl shadow-black/50 backdrop-blur-xl z-[60] no-scrollbar">
                    {{-- Dữ liệu AJAX sẽ render ở đây --}}
                </div>
            </div>

            {{-- Cụm hành động bên phải --}}
            <div class="flex shrink-0 items-center gap-1 sm:gap-2">
                {{-- Yêu thích --}}
                <a href="{{ route('wishlist.index') }}" class="group relative flex items-center gap-2 rounded-xl px-2.5 py-2 text-gray-400 transition-all duration-200 ease-in-out hover:bg-white/5 hover:text-white sm:px-3">
                    <span class="relative">
                        <svg class="size-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.49-2.1-4.5-4.69-4.5-1.94 0-3.6 1.13-4.31 2.73a4.72 4.72 0 0 0-4.31-2.73C5.1 3.75 3 5.76 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z"/></svg>
                        <span id="wishlist-count-badge"
                              class="absolute -right-2 -top-1.5 {{ ($wishlistCount ?? 0) > 0 ? 'flex' : 'hidden' }} size-[17px] items-center justify-center rounded-full bg-brand-600 text-[10px] font-bold text-white">{{ $wishlistCount ?? 0 }}</span>
                    </span>
                    <span class="hidden text-xs font-semibold xl:block">Yêu thích</span>
                </a>
                {{-- So sánh --}}
                <a href="{{ route('compare.index') }}" class="group relative flex items-center gap-2 rounded-xl px-2.5 py-2 text-gray-400 transition-all duration-200 ease-in-out hover:bg-white/5 hover:text-white sm:px-3">
                    <span class="relative">
                        <svg class="size-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 3.75H5.5A1.75 1.75 0 0 0 3.75 5.5v13A1.75 1.75 0 0 0 5.5 20.25h2.75m7.5-16.5h2.75A1.75 1.75 0 0 1 20.25 5.5v13a1.75 1.75 0 0 1-1.75 1.75h-2.75M8.25 8.25h7.5m-7.5 7.5h7.5M12 5.25v13.5"/></svg>
                        <span id="compare-count-badge"
                              class="absolute -right-2 -top-1.5 {{ ($compareCount ?? 0) > 0 ? 'flex' : 'hidden' }} size-[17px] items-center justify-center rounded-full bg-brand-600 text-[10px] font-bold text-white">{{ $compareCount ?? 0 }}</span>
                    </span>
                    <span class="hidden text-xs font-semibold xl:block">So sánh</span>
                </a>
                {{-- Giỏ hàng --}}
                <a href="{{ route('cart.index') }}" class="group relative flex items-center gap-2 rounded-xl px-2.5 py-2 text-gray-400 transition-all duration-200 ease-in-out hover:bg-white/5 hover:text-white sm:px-3">
                    <span class="relative">
                        <svg class="size-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.36-1.62 1.26 12a1.13 1.13 0 0 1-1.12 1.24H4.25a1.13 1.13 0 0 1-1.12-1.24l1.26-12A1.13 1.13 0 0 1 5.51 7.88h12.98c.58 0 1.06.43 1.12 1Z"/></svg>
                        <span id="cart-count-badge"
                              class="absolute -right-2 -top-1.5 {{ ($cartCount ?? 0) > 0 ? 'flex' : 'hidden' }} size-[17px] items-center justify-center rounded-full bg-brand-600 text-[10px] font-bold text-white">{{ $cartCount ?? 0 }}</span>
                    </span>
                    <span class="hidden text-xs font-semibold xl:block">Giỏ hàng</span>
                </a>
                {{-- Tài khoản --}}
                @guest
                    <a href="{{ route('login') }}" class="group flex items-center gap-2 rounded-xl px-2.5 py-2 text-gray-400 transition-all duration-200 ease-in-out hover:bg-white/5 hover:text-white sm:px-3">
                        <svg class="size-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.98 18.72a9.09 9.09 0 0 0 3.74-.48 3 3 0 0 0-4.68-2.72m.94 3.2.01.03c0 .22-.01.44-.04.65a11.94 11.94 0 0 1-11.9 0 8.97 8.97 0 0 1-.04-.68m11.97 0a8.97 8.97 0 0 0-.94-3.2M6.02 18.72a9.09 9.09 0 0 1-3.74-.48 3 3 0 0 1 4.68-2.72m-.94 3.2a8.97 8.97 0 0 0 .94-3.2M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z"/></svg>
                        <span class="hidden text-left text-xs leading-tight xl:block">
                            <span class="block text-gray-500">Đăng nhập</span>
                            <span class="block font-semibold text-white">Tài khoản</span>
                        </span>
                    </a>
                @endguest

                @auth
                    <div class="group relative">
                        <button class="flex items-center gap-2 rounded-xl px-2.5 py-2 text-gray-400 transition-all duration-200 ease-in-out hover:bg-white/5 hover:text-white sm:px-3 focus:outline-none">
                            <svg class="size-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.98 18.72a9.09 9.09 0 0 0 3.74-.48 3 3 0 0 0-4.68-2.72m.94 3.2.01.03c0 .22-.01.44-.04.65a11.94 11.94 0 0 1-11.9 0 8.97 8.97 0 0 1-.04-.68m11.97 0a8.97 8.97 0 0 0-.94-3.2M6.02 18.72a9.09 9.09 0 0 1-3.74-.48 3 3 0 0 1 4.68-2.72m-.94 3.2a8.97 8.97 0 0 0 .94-3.2M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z"/></svg>
                            <span class="hidden text-left text-xs leading-tight xl:block">
                                <span class="block text-gray-500">Xin chào,</span>
                                <span class="block font-semibold text-white truncate max-w-[100px]">{{ explode(' ', Auth::user()->name)[count(explode(' ', Auth::user()->name))-1] }}</span>
                            </span>
                        </button>

                        {{-- Dropdown menu --}}
                        <div class="invisible absolute right-0 top-full mt-1 w-48 opacity-0 transition-all duration-200 ease-in-out group-hover:visible group-hover:opacity-100 group-focus-within:visible group-focus-within:opacity-100 z-50">
                            <div class="rounded-xl border border-white/10 bg-night-soft py-2 shadow-2xl shadow-black/50">
                                <a href="{{ route('account.show') }}" class="block px-4 py-2 text-sm text-gray-300 hover:bg-white/5 hover:text-white transition-colors">Tài khoản của tôi</a>
                                <a href="{{ route('orders.index') }}" class="block px-4 py-2 text-sm text-gray-300 hover:bg-white/5 hover:text-white transition-colors">Quản lý đơn hàng</a>
                                <a href="{{ route('password.change') }}" class="block px-4 py-2 text-sm text-gray-300 hover:bg-white/5 hover:text-white transition-colors">Đổi mật khẩu</a>
                                <div class="my-1 border-t border-white/5"></div>
                                <form action="{{ route('logout') }}" method="POST" class="block w-full">
                                    @csrf
                                    <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-400 hover:bg-white/5 hover:text-red-300 transition-colors">
                                        Đăng xuất
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endauth
                {{-- Nút mở menu mobile --}}
                <button data-mobile-menu-toggle aria-expanded="false" aria-label="Mở menu"
                        class="flex size-10 items-center justify-center rounded-xl text-gray-400 transition-all duration-200 ease-in-out hover:bg-white/5 hover:text-white lg:hidden">
                    <svg class="size-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/></svg>
                </button>
            </div>
        </div>

        {{-- Thanh điều hướng danh mục --}}
        <nav class="hidden border-t border-white/5 lg:block">
            <div class="mx-auto flex h-11 max-w-7xl items-center gap-1 px-4 sm:px-6">
                <div class="group relative mr-2 flex h-full items-center">
                    @auth
                        @if(Auth::user()->isAdmin())
                            <a href="{{ route('admin.categories.index') }}" class="flex items-center gap-2 rounded-lg bg-brand-600 px-4 py-1.5 text-xs font-bold text-white shadow-md shadow-brand-600/25 transition-all duration-200 ease-in-out hover:-translate-y-0.5 hover:bg-brand-500 hover:shadow-lg hover:shadow-brand-600/30">
                        @else
                            <a href="{{ route('home') }}#san-pham" class="flex items-center gap-2 rounded-lg bg-brand-600 px-4 py-1.5 text-xs font-bold text-white shadow-md shadow-brand-600/25 transition-all duration-200 ease-in-out hover:-translate-y-0.5 hover:bg-brand-500 hover:shadow-lg hover:shadow-brand-600/30">
                        @endif
                    @else
                        <a href="{{ route('home') }}#san-pham" class="flex items-center gap-2 rounded-lg bg-brand-600 px-4 py-1.5 text-xs font-bold text-white shadow-md shadow-brand-600/25 transition-all duration-200 ease-in-out hover:-translate-y-0.5 hover:bg-brand-500 hover:shadow-lg hover:shadow-brand-600/30">
                    @endauth
                        <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/></svg>
                        Danh mục
                        <svg class="size-3.5 transition-transform duration-200 group-hover:rotate-180" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6"/></svg>
                    </a>
                    <div class="invisible absolute left-0 top-full z-50 w-64 translate-y-2 pt-2 opacity-0 transition-all duration-200 ease-in-out group-hover:visible group-hover:translate-y-0 group-hover:opacity-100">
                        <div class="overflow-hidden rounded-2xl border border-white/10 bg-night-card p-2 shadow-2xl shadow-black/50 backdrop-blur-xl">
                            <a href="{{ route('home') }}#san-pham" class="flex items-center justify-between rounded-xl px-3 py-2.5 text-sm font-bold text-white transition-colors duration-200 hover:bg-brand-600/15 hover:text-brand-300">
                                Tất cả sản phẩm
                                <span class="text-xs text-gray-500">→</span>
                            </a>
                            @foreach (($categoryLinks ?? []) as $cat)
                                <a href="{{ $cat['href'] }}" class="flex items-center justify-between rounded-xl px-3 py-2.5 text-sm font-semibold text-gray-300 transition-colors duration-200 hover:bg-white/5 hover:text-white">
                                    {{ $cat['label'] }}
                                    <span class="text-xs text-gray-600">→</span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
                @foreach (($categoryLinks ?? []) as $cat)
                    <a href="{{ $cat['href'] }}" class="rounded-lg px-3.5 py-1.5 text-xs font-semibold text-gray-400 transition-all duration-200 ease-in-out hover:bg-white/5 hover:text-white">{{ $cat['label'] }}</a>
                @endforeach
                <a href="#tech-journal" class="rounded-lg px-3.5 py-1.5 text-xs font-semibold text-gray-400 transition-all duration-200 ease-in-out hover:bg-white/5 hover:text-white">Tin công nghệ</a>
            </div>
        </nav>

        {{-- Menu mobile --}}
        <div data-mobile-menu class="hidden border-t border-white/5 bg-night px-4 pb-4 pt-2 lg:hidden">
            @foreach (($categoryLinks ?? []) as $cat)
                <a href="{{ $cat['href'] }}" class="block rounded-xl px-4 py-2.5 text-sm font-semibold text-gray-300 transition-all duration-200 ease-in-out hover:bg-white/5 hover:text-white">{{ $cat['label'] }}</a>
            @endforeach
            <a href="#flash-sale" class="block rounded-xl px-4 py-2.5 text-sm font-semibold text-amber-400 transition-all duration-200 ease-in-out hover:bg-amber-400/10">Khuyến mãi</a>
            <a href="#tech-journal" class="block rounded-xl px-4 py-2.5 text-sm font-semibold text-gray-300 transition-all duration-200 ease-in-out hover:bg-white/5 hover:text-white">Tin công nghệ</a>
        </div>
    </header>

    {{-- ===================== Nội dung trang ===================== --}}
    <main>
        @yield('content')
    </main>

    {{-- ===================== Footer ===================== --}}
    <footer id="lien-he" class="border-t border-white/5 bg-night-soft">
        <div class="mx-auto grid max-w-7xl gap-10 px-4 py-14 sm:grid-cols-2 sm:px-6 lg:grid-cols-5">

            {{-- Cột 1: Giới thiệu --}}
            <div class="lg:col-span-2 lg:pr-10">
                <a href="{{ route('home') }}" class="mb-4 inline-flex" aria-label="NovaPhone - Trang chủ">
                    <img
                        src="{{ asset('images/brand/nova-phone-logo.webp') }}"
                        alt="NovaPhone"
                        class="h-14 w-auto max-w-[210px] object-contain"
                    >
                </a>
                <p class="text-sm leading-relaxed text-gray-400">
                    NovaPhone — Hệ thống bán lẻ điện thoại, máy tính bảng và phụ kiện chính hãng. Cam kết sản phẩm chất lượng, giá tốt nhất thị trường và dịch vụ hậu mãi tận tâm.
                </p>
                <div class="mt-5 flex gap-2.5">
                    <a href="#" aria-label="Facebook" class="flex size-9 items-center justify-center rounded-xl bg-white/5 text-gray-400 transition-all duration-200 ease-in-out hover:-translate-y-0.5 hover:bg-brand-600 hover:text-white">
                        <svg class="size-4" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.07C24 5.41 18.63 0 12 0S0 5.4 0 12.07C0 18.1 4.39 23.1 10.13 24v-8.44H7.08v-3.49h3.04V9.41c0-3.02 1.8-4.7 4.54-4.7 1.31 0 2.68.24 2.68.24v2.97h-1.5c-1.5 0-1.96.93-1.96 1.89v2.26h3.32l-.53 3.5h-2.8V24C19.62 23.1 24 18.1 24 12.07Z"/></svg>
                    </a>
                    <a href="#" aria-label="YouTube" class="flex size-9 items-center justify-center rounded-xl bg-white/5 text-gray-400 transition-all duration-200 ease-in-out hover:-translate-y-0.5 hover:bg-brand-600 hover:text-white">
                        <svg class="size-4" fill="currentColor" viewBox="0 0 24 24"><path d="M23.5 6.19a3.02 3.02 0 0 0-2.12-2.14C19.5 3.55 12 3.55 12 3.55s-7.5 0-9.38.5A3.02 3.02 0 0 0 .5 6.19C0 8.07 0 12 0 12s0 3.93.5 5.81a3.02 3.02 0 0 0 2.12 2.14c1.88.5 9.38.5 9.38.5s7.5 0 9.38-.5a3.02 3.02 0 0 0 2.12-2.14C24 15.93 24 12 24 12s0-3.93-.5-5.81ZM9.55 15.57V8.43L15.82 12l-6.27 3.57Z"/></svg>
                    </a>
                    <a href="#" aria-label="TikTok" class="flex size-9 items-center justify-center rounded-xl bg-white/5 text-gray-400 transition-all duration-200 ease-in-out hover:-translate-y-0.5 hover:bg-brand-600 hover:text-white">
                        <svg class="size-4" fill="currentColor" viewBox="0 0 24 24"><path d="M19.59 6.69a4.83 4.83 0 0 1-3.77-4.25V2h-3.45v13.67a2.9 2.9 0 0 1-5.2 1.74 2.89 2.89 0 0 1 2.31-4.64c.3 0 .58.04.86.13V9.4a6.33 6.33 0 0 0-5.39 10.69 6.33 6.33 0 0 0 10.86-4.43v-7a8.16 8.16 0 0 0 4.77 1.52v-3.4a4.85 4.85 0 0 1-1-.1Z"/></svg>
                    </a>
                    <a href="#" aria-label="Zalo" class="flex size-9 items-center justify-center rounded-xl bg-white/5 text-xs font-extrabold text-gray-400 transition-all duration-200 ease-in-out hover:-translate-y-0.5 hover:bg-brand-600 hover:text-white">Z</a>
                </div>
            </div>

            {{-- Cột 2: Về chúng tôi --}}
            <div>
                <h4 class="mb-4 text-sm font-bold uppercase tracking-wider text-white">Về chúng tôi</h4>
                <ul class="space-y-3 text-sm text-gray-400">
                    <li><a href="#" class="transition-colors duration-200 hover:text-brand-400">Giới thiệu</a></li>
                    <li><a href="#" class="transition-colors duration-200 hover:text-brand-400">Tuyển dụng</a></li>
                    <li><a href="#" class="transition-colors duration-200 hover:text-brand-400">Tin tức</a></li>
                    <li><a href="#" class="transition-colors duration-200 hover:text-brand-400">Hệ thống cửa hàng</a></li>
                </ul>
            </div>

            {{-- Cột 3: Chính sách --}}
            <div>
                <h4 class="mb-4 text-sm font-bold uppercase tracking-wider text-white">Chính sách</h4>
                <ul class="space-y-3 text-sm text-gray-400">
                    <li><a href="#" class="transition-colors duration-200 hover:text-brand-400">Chính sách bảo hành</a></li>
                    <li><a href="#" class="transition-colors duration-200 hover:text-brand-400">Chính sách đổi trả</a></li>
                    <li><a href="#" class="transition-colors duration-200 hover:text-brand-400">Chính sách bảo mật</a></li>
                    <li><a href="#" class="transition-colors duration-200 hover:text-brand-400">Chính sách trả góp</a></li>
                </ul>
            </div>

            {{-- Cột 4: Hỗ trợ + Tải ứng dụng --}}
            <div>
                <h4 class="mb-4 text-sm font-bold uppercase tracking-wider text-white">Hỗ trợ</h4>
                <ul class="space-y-3 text-sm text-gray-400">
                    <li><a href="#" class="transition-colors duration-200 hover:text-brand-400">Trung tâm hỗ trợ</a></li>
                    <li><a href="#" class="transition-colors duration-200 hover:text-brand-400">Hướng dẫn mua hàng</a></li>
                    <li><a href="#" class="transition-colors duration-200 hover:text-brand-400">Tra cứu đơn hàng</a></li>
                    <li><a href="tel:18001234" class="font-semibold text-white transition-colors duration-200 hover:text-brand-400">Hotline: 1800 1234</a></li>
                </ul>

                <h4 class="mb-3 mt-6 text-sm font-bold uppercase tracking-wider text-white">Tải ứng dụng</h4>
                <div class="flex flex-col gap-2">
                    <a href="#" class="flex w-36 items-center gap-2 rounded-lg border border-white/10 bg-white/5 px-3 py-1.5 transition-all duration-200 ease-in-out hover:border-white/25 hover:bg-white/10">
                        <svg class="size-5 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M18.71 19.5c-.83 1.24-1.71 2.45-3.05 2.47-1.34.03-1.77-.79-3.29-.79-1.53 0-2 .77-3.27.82-1.31.05-2.3-1.32-3.14-2.53C4.25 17 2.94 12.45 4.7 9.39c.87-1.52 2.43-2.48 4.12-2.51 1.28-.02 2.5.87 3.29.87.78 0 2.26-1.07 3.81-.91.65.03 2.47.26 3.64 1.98-.09.06-2.17 1.28-2.15 3.81.03 3.02 2.65 4.03 2.68 4.04-.03.07-.42 1.44-1.38 2.83M13 3.5c.73-.83 1.94-1.46 2.94-1.5.13 1.17-.34 2.35-1.04 3.19-.69.85-1.83 1.51-2.95 1.42-.15-1.15.41-2.35 1.05-3.11z"/></svg>
                        <span class="text-left text-[10px] leading-tight text-gray-300"><span class="block text-[8px] text-gray-500">Tải về trên</span><span class="font-bold text-white">App Store</span></span>
                    </a>
                    <a href="#" class="flex w-36 items-center gap-2 rounded-lg border border-white/10 bg-white/5 px-3 py-1.5 transition-all duration-200 ease-in-out hover:border-white/25 hover:bg-white/10">
                        <svg class="size-5 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M3.61 1.81 13.79 12 3.61 22.19c-.36-.19-.61-.57-.61-1.04V2.85c0-.47.25-.85.61-1.04ZM14.85 13.06l2.44 2.44-9.91 5.7 7.47-8.14Zm3.78-2.16 2.59 1.49c.78.45.78 1.58 0 2.03l-2.59 1.49-2.72-2.5 2.72-2.51ZM7.38 2.8l9.91 5.7-2.44 2.44L7.38 2.8Z"/></svg>
                        <span class="text-left text-[10px] leading-tight text-gray-300"><span class="block text-[8px] text-gray-500">Tải về trên</span><span class="font-bold text-white">Google Play</span></span>
                    </a>
                </div>
            </div>
        </div>

        {{-- Dòng cuối: bản quyền + thanh toán --}}
        <div class="border-t border-white/5 py-5">
            <div class="mx-auto flex max-w-7xl flex-col items-center justify-between gap-4 px-4 sm:flex-row sm:px-6">
                <p class="text-xs text-gray-500">© {{ date('Y') }} NovaPhone. Bảo lưu mọi quyền.</p>
                <div class="flex items-center gap-2">
                    <span class="text-[10px] uppercase tracking-wider text-gray-600">Phương thức thanh toán</span>
                    @foreach (['VISA', 'MC', 'Momo', 'ZaloPay', 'COD'] as $pay)
                        <span class="rounded-md border border-white/10 bg-white/5 px-2 py-1 text-[9px] font-bold text-gray-400">{{ $pay }}</span>
                    @endforeach
                </div>
            </div>
        </div>
    </footer>

    @stack('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('quick-search-input');
            const searchResults = document.getElementById('quick-search-results');
            const searchContainer = document.getElementById('quick-search-container');
            let debounceTimer;

            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    clearTimeout(debounceTimer);
                    const query = this.value.trim();

                    if (query.length < 2) {
                        searchResults.classList.add('hidden');
                        searchResults.innerHTML = '';
                        return;
                    }

                    debounceTimer = setTimeout(() => {
                        fetch(`{{ route('search.quick') }}?q=${encodeURIComponent(query)}`)
                            .then(res => res.json())
                            .then(data => {
                                if (data.length > 0) {
                                    let html = '<div class="p-2 space-y-1">';
                                    data.forEach(item => {
                                        let oldPriceHtml = item.old_price ? `<span class="text-[10px] text-gray-500 line-through">${item.old_price}</span>` : '';
                                        html += `
                                            <a href="${item.url}" class="flex items-center gap-3 rounded-lg p-2 transition-colors hover:bg-white/5 group">
                                                <div class="flex size-12 shrink-0 items-center justify-center rounded-md bg-white/5 p-1 transition-transform group-hover:scale-105">
                                                    <img src="${item.thumbnail}" alt="${item.name}" class="h-full w-full object-contain">
                                                </div>
                                                <div class="flex flex-1 flex-col justify-center overflow-hidden">
                                                    <h4 class="truncate text-sm font-semibold text-gray-200 group-hover:text-brand-300 transition-colors">${item.name}</h4>
                                                    <div class="flex items-baseline gap-2 mt-0.5">
                                                        <span class="text-xs font-bold text-brand-400">${item.price}</span>
                                                        ${oldPriceHtml}
                                                    </div>
                                                </div>
                                            </a>
                                        `;
                                    });
                                    html += '</div>';
                                    searchResults.innerHTML = html;
                                    searchResults.classList.remove('hidden');
                                } else {
                                    searchResults.innerHTML = '<div class="p-4 text-center text-sm text-gray-500">Không tìm thấy sản phẩm nào phù hợp.</div>';
                                    searchResults.classList.remove('hidden');
                                }
                            })
                            .catch(err => {
                                console.error('Search error:', err);
                            });
                    }, 300); // delay 300ms
                });

                // Ẩn khi click ra ngoài
                document.addEventListener('click', function(e) {
                    if (!searchContainer.contains(e.target)) {
                        searchResults.classList.add('hidden');
                    }
                });

                // Hiện lại khi focus vào input nếu đã có giá trị
                searchInput.addEventListener('focus', function() {
                    if (this.value.trim().length >= 2 && searchResults.innerHTML !== '') {
                        searchResults.classList.remove('hidden');
                    }
                });
            }

            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            const updateCompareBadge = (count) => {
                const badge = document.getElementById('compare-count-badge');
                if (!badge) return;

                badge.textContent = count;
                badge.classList.toggle('hidden', count === 0);
                badge.classList.toggle('flex', count > 0);
            };

            const showCompareToast = (message, type = 'success') => {
                const toast = document.createElement('div');
                toast.setAttribute('role', 'status');
                toast.setAttribute('aria-live', 'polite');
                toast.className = `fixed bottom-5 right-5 z-[100] rounded-xl border px-4 py-3 text-sm font-semibold shadow-2xl transition ${type === 'success' ? 'border-emerald-400/30 bg-emerald-950 text-emerald-200' : 'border-red-400/30 bg-red-950 text-red-200'}`;
                toast.textContent = message;
                document.body.appendChild(toast);
                window.setTimeout(() => toast.remove(), 3200);
            };

            document.addEventListener('click', async (event) => {
                const button = event.target.closest('[data-compare-toggle]');
                if (!button) return;

                event.preventDefault();
                event.stopPropagation();

                const productId = button.dataset.compareToggle;
                const isCompared = button.dataset.compared === 'true';
                const url = isCompared
                    ? `{{ url('/compare') }}/${productId}`
                    : '{{ route('compare.add') }}';

                button.disabled = true;

                try {
                    const response = await fetch(url, {
                        method: isCompared ? 'DELETE' : 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                        },
                        body: isCompared ? null : JSON.stringify({ product_id: Number(productId) }),
                    });
                    const data = await response.json();

                    if (!response.ok || !data.success) {
                        throw new Error(data.message || 'Không thể cập nhật danh sách so sánh.');
                    }

                    const compared = data.action === 'added';
                    document.querySelectorAll(`[data-compare-toggle="${productId}"]`).forEach((control) => {
                        control.dataset.compared = compared ? 'true' : 'false';
                        control.setAttribute('aria-label', compared ? 'Xóa khỏi so sánh' : 'Thêm vào so sánh');
                        control.querySelector('svg')?.classList.toggle('text-brand-300', compared);

                        const label = control.querySelector('[data-compare-label]');
                        if (label) label.textContent = compared ? 'Xóa khỏi so sánh' : 'So sánh sản phẩm';
                    });
                    updateCompareBadge(data.count);
                    showCompareToast(data.message);
                } catch (error) {
                    showCompareToast(error.message || 'Không thể cập nhật danh sách so sánh.', 'error');
                } finally {
                    button.disabled = false;
                }
            });

            // Wishlist Toggle
            document.querySelectorAll('[data-wishlist-toggle]').forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();

                    const productId = this.getAttribute('data-wishlist-toggle');
                    const svg = this.querySelector('svg');
                    const originalHTML = this.innerHTML;

                    svg.style.opacity = '0.5';

                    fetch('{{ route("wishlist.toggle") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ product_id: productId })
                    })
                    .then(response => {
                        if (response.status === 401) {
                            window.location.href = '{{ route("login") }}';
                            throw new Error('Unauthorized');
                        }
                        return response.json();
                    })
                    .then(data => {
                        svg.style.opacity = '1';
                        if (data.status === 'added') {
                            svg.classList.remove('fill-transparent', 'text-white', 'hover:text-red-400');
                            svg.classList.add('fill-red-500', 'text-red-500');
                            this.setAttribute('aria-label', 'Xóa khỏi yêu thích');
                        } else if (data.status === 'removed') {
                            svg.classList.remove('fill-red-500', 'text-red-500');
                            svg.classList.add('fill-transparent', 'text-white', 'hover:text-red-400');
                            this.setAttribute('aria-label', 'Thêm vào yêu thích');
                        }

                        // Cập nhật badge
                        const badge = document.getElementById('wishlist-count-badge');
                        if (badge) {
                            badge.textContent = data.count;
                            if (data.count > 0) {
                                badge.classList.remove('hidden');
                                badge.classList.add('flex');
                            } else {
                                badge.classList.add('hidden');
                                badge.classList.remove('flex');
                            }
                        }
                        
                        // Nếu đang ở trang wishlist, ẩn thẻ sản phẩm và cập nhật tổng số lượng
                        if (data.status === 'removed' && window.location.pathname.includes('wishlist')) {
                            const card = this.closest('.wishlist-item');
                            if (card) {
                                card.remove();
                            }
                            const totalCountEl = document.getElementById('wishlist-total-count');
                            if (totalCountEl) {
                                totalCountEl.textContent = data.count;
                            }
                            
                            // Nếu hết sản phẩm, có thể reload để hiện màn hình trống
                            if (data.count === 0) {
                                window.location.reload();
                            }
                        }
                    })
                    .catch(error => {
                        if (error.message !== 'Unauthorized') {
                            console.error('Error toggling wishlist:', error);
                            svg.style.opacity = '1';
                        }
                    });
                });
            });
        });
    </script>

    <x-chatbot-widget />
</body>
</html>
