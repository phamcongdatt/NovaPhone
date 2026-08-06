<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'NovaPhone')</title>
    @stack('head')

    <script>
        window.__novaChartQueue = [];
        window.novaChart = (cb) => window.__novaChartQueue.push({ lib: 'apex', cb });
        window.novaChartJs = (cb) => window.__novaChartQueue.push({ lib: 'chartjs', cb });
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="overflow-x-hidden bg-[#f5f4f1] text-[#161616] antialiased">
    @php
        $navLinks = [
            ['label' => 'Shop', 'href' => route('home')],
            ['label' => 'iPhone', 'href' => route('products.index', ['search' => 'iPhone'])],
            ['label' => 'Samsung', 'href' => route('products.index', ['search' => 'Samsung'])],
            ['label' => 'Xiaomi', 'href' => route('products.index', ['search' => 'Xiaomi'])],
            ['label' => 'Mã khuyến mại', 'href' => route('coupons.index')],
            ['label' => 'Tin công nghệ', 'href' => route('posts.index')],
        ];
    @endphp

    <div class="w-full">
        <header data-site-header class="relative z-50 w-full border-b border-[#e5e5e7] bg-[#fbfbfd]/95 backdrop-blur">
            <div class="mx-auto flex h-16 max-w-[1440px] items-center gap-4 px-4 sm:px-6 lg:px-8">
                <a href="{{ route('home') }}" class="inline-flex shrink-0 items-center gap-2.5" aria-label="NovaPhone">
                    <img src="{{ asset('images/brand/novaphone-mark-v2.png') }}" alt="" aria-hidden="true" width="28" height="28" fetchpriority="high" decoding="async" class="size-7 object-contain">
                    <span class="whitespace-nowrap text-[15px] font-semibold tracking-[-0.045em] text-[#111827]">Nova<span class="text-[#0a84ff]">Phone</span></span>
                </a>

                <nav class="hidden flex-1 items-center justify-center gap-8 lg:flex">
                    @foreach ($navLinks as $link)
                        <a href="{{ $link['href'] }}" class="text-[13px] font-medium text-[#303030] transition hover:text-black">
                            {{ $link['label'] }}
                        </a>
                    @endforeach
                </nav>

                <div class="ml-auto flex items-center gap-1.5">
                    <button type="button" data-search-open aria-controls="nova-search-overlay" aria-expanded="false" class="inline-flex size-9 items-center justify-center rounded-full border border-transparent text-[#222] transition hover:border-[#e6e2dc] hover:bg-white focus:outline-none focus-visible:ring-2 focus-visible:ring-[#0066cc]" aria-label="Tìm kiếm">
                        <svg class="size-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.3-4.3m1.8-5.2a6.5 6.5 0 1 1-13 0 6.5 6.5 0 0 1 13 0Z"/></svg>
                    </button>
                    <a href="{{ route('compare.index') }}" class="inline-flex size-9 items-center justify-center rounded-full border border-transparent text-[#222] transition hover:border-[#e6e2dc] hover:bg-[#faf9f7]" aria-label="So sánh sản phẩm">
                        <svg class="size-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 3.75H5.5A1.75 1.75 0 0 0 3.75 5.5v13A1.75 1.75 0 0 0 5.5 20.25h2.75m7.5-16.5h2.75A1.75 1.75 0 0 1 20.25 5.5v13a1.75 1.75 0 0 1-1.75 1.75h-2.75M8.25 8.25h7.5m-7.5 7.5h7.5M12 5.25v13.5"/></svg>
                    </a>
                    <a href="{{ route('wishlist.index') }}" class="relative inline-flex size-9 items-center justify-center rounded-full border border-transparent text-[#222] transition hover:border-[#e6e2dc] hover:bg-[#faf9f7]" aria-label="Yêu thích">
                        <svg class="size-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.49-2.1-4.5-4.69-4.5-1.94 0-3.6 1.13-4.31 2.73a4.72 4.72 0 0 0-4.31-2.73C5.1 3.75 3 5.76 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z"/></svg>
                    </a>
                    <a href="{{ route('account.show') }}" class="inline-flex size-9 items-center justify-center rounded-full border border-transparent text-[#222] transition hover:border-[#e6e2dc] hover:bg-[#faf9f7]" aria-label="Tài khoản">
                        <svg class="size-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6.375a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Zm-10.5 15.75a7.5 7.5 0 0 1 15 0"/></svg>
                    </a>
                    <a href="{{ route('cart.index') }}" class="relative inline-flex size-9 items-center justify-center rounded-full border border-transparent text-[#222] transition hover:border-[#e6e2dc] hover:bg-[#faf9f7]" aria-label="Giỏ hàng">
                        <svg class="size-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.51c.41 0 .78.26.92.65l1.67 4.75m0 0L7.2 13.5a2.25 2.25 0 0 0 2.15 1.6h7.9a2.25 2.25 0 0 0 2.16-1.63l1.6-5.52H6.35Zm4.65 14.25a1.125 1.125 0 1 1 0-2.25 1.125 1.125 0 0 1 0 2.25Zm9 0a1.125 1.125 0 1 1 0-2.25 1.125 1.125 0 0 1 0 2.25Z"/></svg>
                        @if (isset($cartCount) && $cartCount > 0)
                            <span data-cart-badge class="absolute -right-1 -top-1 inline-flex size-4 items-center justify-center rounded-full bg-black text-[9px] font-bold text-white">{{ $cartCount }}</span>
                        @endif
                    </a>
                </div>
            </div>
            <div class="border-t border-[#f0eeea] px-4 py-2 sm:px-6 lg:hidden">
                <div class="flex flex-wrap items-center gap-2 text-[11px] font-semibold text-[#777]">
                    @foreach ($navLinks as $link)
                        <a href="{{ $link['href'] }}" class="rounded-full px-2.5 py-1 transition hover:bg-[#f6f4f1] hover:text-black">{{ $link['label'] }}</a>
                    @endforeach
                </div>
            </div>
        </header>

        <div id="nova-search-overlay" data-search-overlay class="fixed inset-0 z-40 bg-[rgba(245,245,247,.46)] backdrop-blur-md" aria-hidden="true" inert>
            <section data-search-panel class="nova-search-panel absolute inset-x-0 overflow-y-auto border-b border-[#e5e5e7] bg-[#fbfbfd]/[.98] shadow-[0_24px_55px_rgba(0,0,0,.08)]" role="dialog" aria-modal="true" aria-labelledby="nova-search-title">
                <div class="mx-auto max-w-[1120px] px-5 py-10 sm:px-8 sm:py-14">
                    <div class="flex items-start gap-3">
                        <form action="{{ route('products.index') }}" method="GET" class="min-w-0 flex-1" role="search">
                            <label id="nova-search-title" class="sr-only" for="quick-search-input">Tìm kiếm sản phẩm NovaPhone</label>
                            <div class="flex items-center gap-3 border-b border-[#d2d2d7] pb-3 sm:gap-4">
                                <svg class="size-6 shrink-0 text-[#6e6e73] sm:size-7" fill="none" stroke="currentColor" stroke-width="1.65" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.3-4.3m1.8-5.2a6.5 6.5 0 1 1-13 0 6.5 6.5 0 0 1 13 0Z"/></svg>
                                <input
                                    id="quick-search-input"
                                    name="search"
                                    data-quick-search-url="{{ route('search.quick') }}"
                                    data-search-results-url="{{ route('products.index') }}"
                                    type="search"
                                    autocomplete="off"
                                    placeholder="Tìm kiếm trên NovaPhone"
                                    class="min-w-0 flex-1 bg-transparent text-2xl font-semibold tracking-[-0.035em] text-[#1d1d1f] outline-none placeholder:text-[#86868b] sm:text-4xl"
                                >
                            </div>
                            <div id="quick-search-results" class="mt-6 hidden max-h-[44vh] overflow-y-auto border-y border-[#e5e5e7] bg-white"></div>
                        </form>
                        <button type="button" data-search-close class="mt-0.5 inline-flex size-9 shrink-0 items-center justify-center rounded-full text-[#424245] transition hover:bg-[#e8e8ed] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#0066cc]" aria-label="Đóng tìm kiếm">
                            <svg class="size-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" d="m6 6 12 12M18 6 6 18"/></svg>
                        </button>
                    </div>

                    <div data-search-quick-links class="mt-10 max-w-xl sm:mt-12">
                        <p class="text-sm font-medium text-[#6e6e73]">Liên kết nhanh</p>
                        <ul class="mt-3 space-y-1">
                            @foreach ($navLinks as $link)
                                <li>
                                    <a href="{{ $link['href'] }}" class="group inline-flex items-center gap-3 py-2 text-base font-semibold text-[#424245] transition hover:text-[#0066cc]">
                                        <svg class="size-4 text-[#6e6e73] transition group-hover:translate-x-0.5 group-hover:text-[#0066cc]" viewBox="0 0 16 16" fill="none" aria-hidden="true"><path d="M3 8h9M8.5 3.5 13 8l-4.5 4.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                        {{ $link['label'] }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </section>
        </div>

        <main data-page-enter class="mx-auto w-full px-3 pt-5 sm:px-4 lg:w-[90%] lg:px-0">
            @if (session('success'))
                <div data-toast="{{ session('success') }}" data-toast-type="success"></div>
            @endif
            @if (session('error'))
                <div data-toast="{{ session('error') }}" data-toast-type="error"></div>
            @endif
            @yield('content')
        </main>
    </div>

    @stack('modals')
    @stack('scripts')
<footer class="defer-render mt-12 rounded-[28px] border border-[#e9e7e2] bg-white shadow-[0_12px_40px_rgba(0,0,0,.04)]">
    <div class="px-6 py-12">

        {{-- Top --}}
        <div class="grid gap-10 md:grid-cols-2 lg:grid-cols-5">

            {{-- Brand --}}
            <div class="lg:col-span-2">

                <div class="inline-flex items-center gap-2.5" aria-label="NovaPhone">
                    <img src="{{ asset('images/brand/novaphone-mark-v2.png') }}" alt="" aria-hidden="true" width="32" height="32" loading="lazy" decoding="async" class="size-8 object-contain">
                    <span class="text-lg font-semibold tracking-[-0.045em] text-[#111827]">Nova<span class="text-[#0a84ff]">Phone</span></span>
                </div>

                <p class="mt-5 max-w-sm text-sm leading-7 text-[#666]">
                    NovaPhone là hệ thống bán lẻ điện thoại, máy tính bảng,
                    phụ kiện và thiết bị công nghệ chính hãng với trải nghiệm
                    mua sắm hiện đại, minh bạch và nhanh chóng.
                </p>

                <div class="mt-6 flex gap-3">

                    <span class="flex h-10 w-10 items-center justify-center rounded-full border text-xs font-semibold text-[#777]">
                        FB
                    </span>

                    <span class="flex h-10 w-10 items-center justify-center rounded-full border text-xs font-semibold text-[#777]">
                        IG
                    </span>

                    <span class="flex h-10 w-10 items-center justify-center rounded-full border text-xs font-semibold text-[#777]">
                        YT
                    </span>

                </div>

            </div>

            {{-- Shop --}}
            <div>

                <h3 class="font-semibold">
                    Mua sắm
                </h3>

                <ul class="mt-5 space-y-3 text-sm text-[#666]">

                    <li><a href="{{ route('products.index') }}" class="hover:text-black">Tất cả sản phẩm</a></li>

                    <li><a href="{{ route('products.index',['search'=>'iPhone']) }}" class="hover:text-black">iPhone</a></li>

                    <li><a href="{{ route('products.index',['search'=>'Samsung']) }}" class="hover:text-black">Samsung</a></li>

                    <li><a href="{{ route('products.index',['search'=>'Xiaomi']) }}" class="hover:text-black">Xiaomi</a></li>

                    <li><a href="{{ route('coupons.index') }}" class="hover:text-black">Khuyến mãi</a></li>

                </ul>

            </div>

            {{-- Customer --}}
            <div>

                <h3 class="font-semibold">
                    Khách hàng
                </h3>

                <ul class="mt-5 space-y-3 text-sm text-[#666]">

                    <li><a href="{{ route('orders.index') }}" class="hover:text-black">Đơn hàng</a></li>

                    <li><a href="{{ route('wishlist.index') }}" class="hover:text-black">Yêu thích</a></li>

                    <li><a href="{{ route('cart.index') }}" class="hover:text-black">Giỏ hàng</a></li>

                    <li><a href="{{ route('account.show') }}" class="hover:text-black">Tài khoản</a></li>

                </ul>

            </div>

            {{-- Contact --}}
            <div>

                <h3 class="font-semibold">
                    Liên hệ
                </h3>

                <div class="mt-5 space-y-3 text-sm text-[#666]">
                    <p>Thông tin hỗ trợ và hướng dẫn mua sắm được cập nhật trong khu vực tin tức.</p>
                    <a href="{{ route('posts.index') }}" class="font-semibold text-black hover:text-[#666]">Xem hướng dẫn</a>
                </div>

            </div>

        </div>

        {{-- Newsletter --}}

        <div class="mt-12 rounded-3xl bg-[#f7f6f3] p-8">

            <div class="flex flex-col items-start justify-between gap-5 lg:flex-row lg:items-center">

                <div>

                    <h3 class="text-xl font-semibold">
                        Đăng ký nhận ưu đãi
                    </h3>

                    <p class="mt-2 text-sm text-[#666]">
                        Nhận thông tin về sản phẩm mới và chương trình khuyến mãi.
                    </p>

                </div>

                <p class="w-full max-w-xl text-sm leading-6 text-[#666] lg:text-right">
                    Theo dõi các kênh chính thức của NovaPhone để cập nhật sản phẩm và chương trình mới.
                </p>

            </div>

        </div>

        {{-- Bottom --}}

        <div class="mt-10 flex flex-col gap-3 border-t border-[#ececec] pt-6 text-sm text-[#777] md:flex-row md:items-center md:justify-between">

            <p>
                © {{ date('Y') }} NovaPhone. All rights reserved.
            </p>

            <div class="flex flex-wrap gap-5">

                <span>Chính sách bảo mật</span>

                <span>Điều khoản sử dụng</span>

                <span>Chính sách đổi trả</span>

                <span>Vận chuyển</span>

            </div>

        </div>

    </div>
</footer>

<div class="fixed bottom-5 right-5 z-50">
    <button type="button" data-nova-chat-toggle aria-controls="nova-ai-chat" aria-expanded="false" class="group inline-flex items-center gap-2 rounded-full bg-[#111827] px-4 py-3 text-sm font-semibold text-white shadow-[0_14px_35px_rgba(17,24,39,.28)] transition-transform duration-300 hover:-translate-y-0.5 hover:bg-black focus:outline-none focus:ring-4 focus:ring-[#0a84ff]/20">
        <span class="grid size-5 place-items-center rounded-full bg-[#0a84ff]">
            <svg class="size-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M8 15h8m2 4-3.2-2.4a3 3 0 0 1-1.8.6H7a4 4 0 0 1-4-4V7a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4v6a4 4 0 0 1-3 3.87V19Z"/></svg>
        </span>
        <span>Nova AI</span>
    </button>

    <section id="nova-ai-chat" data-nova-chat-panel class="absolute bottom-0 right-0 hidden w-[min(420px,calc(100vw-2.5rem))] overflow-hidden rounded-[26px] border border-[#e5e3df] bg-white shadow-[0_24px_70px_rgba(0,0,0,.18)]" role="dialog" aria-labelledby="nova-ai-chat-title">
        <header class="flex items-start justify-between gap-4 border-b border-[#eeece8] px-5 py-4">
            <div class="flex min-w-0 items-center gap-3">
                <span class="grid size-10 shrink-0 place-items-center rounded-2xl bg-[#eef6ff] text-[#0a84ff]">
                    <svg class="size-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2m0 14v2M4.9 4.9l1.4 1.4m11.4 11.4 1.4 1.4M3 12h2m14 0h2M4.9 19.1l1.4-1.4M17.7 6.3l1.4-1.4M12 7a5 5 0 1 0 0 10 5 5 0 0 0 0-10Z"/></svg>
                </span>
                <div class="min-w-0">
                    <h2 id="nova-ai-chat-title" class="text-sm font-bold text-[#171717]">Nova AI</h2>
                    <p class="mt-0.5 text-xs text-[#777]">Tư vấn sản phẩm từ NovaPhone</p>
                </div>
            </div>
            <button type="button" data-nova-chat-close class="grid size-8 place-items-center rounded-full text-[#777] transition-colors duration-300 hover:bg-[#f5f4f1] hover:text-black" aria-label="Đóng chat">
                <svg class="size-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" d="m6 6 12 12M18 6 6 18"/></svg>
            </button>
        </header>

        <div data-nova-chat-messages class="flex max-h-[min(500px,calc(100vh-15rem))] min-h-64 flex-col gap-3 overflow-y-auto bg-[#fbfaf8] px-4 py-5" aria-live="polite" aria-relevant="additions">
            <div data-nova-chat-empty class="rounded-2xl border border-dashed border-[#e3dfd9] bg-white px-4 py-5 text-center">
                <p class="text-sm font-semibold text-[#222]">Bạn cần hỗ trợ gì?</p>
                <p class="mt-1 text-xs leading-5 text-[#777]">Hỏi Nova AI về sản phẩm, thông số hoặc cách mua hàng.</p>
            </div>
        </div>

        <form data-nova-chat-form data-chat-api-url="{{ route('api.chatbot') }}" data-product-api-template="{{ route('api.products.show', ['product' => '__nova_product_slug__']) }}" data-cart-add-url="{{ route('cart.store') }}" data-buy-now-url="{{ route('cart.buy-now') }}" class="border-t border-[#eeece8] bg-white p-3">
            <p data-nova-chat-error class="mb-2 hidden text-xs text-red-600" role="alert"></p>
            <div class="flex items-end gap-2 rounded-2xl border border-[#e5e1db] bg-[#fbfaf8] p-1.5 focus-within:border-[#0a84ff]">
                <label class="sr-only" for="nova-chat-input">Nhập câu hỏi cho Nova AI</label>
                <input id="nova-chat-input" data-nova-chat-input name="message" type="text" maxlength="1000" autocomplete="off" required placeholder="Nhập câu hỏi của bạn..." class="min-w-0 flex-1 bg-transparent px-2.5 py-2 text-sm text-[#222] outline-none placeholder:text-[#9a9a9a]">
                <button type="submit" data-nova-chat-send class="grid size-9 shrink-0 place-items-center rounded-xl bg-black text-white transition-colors duration-300 hover:bg-[#222] disabled:cursor-not-allowed disabled:opacity-50" aria-label="Gửi câu hỏi">
                    <svg class="size-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m22 2-7 20-4-9-9-4 20-7Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M22 2 11 13"/></svg>
                </button>
            </div>
        </form>
    </section>
</div>
</body>
</html>
