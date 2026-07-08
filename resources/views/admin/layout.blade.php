<!DOCTYPE html>
<html lang="vi" class="dark">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>@yield('title', 'Quản trị') — NovaPhone Admin</title>
@vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen overflow-x-hidden bg-[#050b14] font-sans text-gray-100 antialiased selection:bg-brand-500/30">
@php
$navItem = 'group flex items-center gap-3 rounded-xl px-3 py-2.5 text-[13px] font-medium transition-all duration-200';
$navIdle = 'text-slate-400 hover:bg-white/[0.045] hover:text-white';
$navActive = 'border border-blue-400/25 bg-gradient-to-r from-blue-600/30 to-blue-500/10 text-white shadow-[0_0_24px_rgba(37,99,235,.16)]';
@endphp

<div id="admin-sidebar-overlay" class="fixed inset-0 z-40 hidden bg-black/70 backdrop-blur-sm lg:hidden"></div>

<aside id="admin-sidebar" class="fixed inset-y-0 left-0 z-50 flex w-64 -translate-x-full flex-col border-r border-blue-400/10 bg-[#07111e]/95 shadow-2xl shadow-black/40 backdrop-blur-xl transition-transform duration-300 lg:translate-x-0">
<div class="flex h-[76px] shrink-0 items-center justify-between border-b border-white/[0.06] px-5">
<a href="{{ route('admin.dashboard') }}" class="flex min-w-0 items-center" aria-label="NovaPhone Admin">
<img src="{{ asset('images/brand/nova-phone-logo.png') }}" alt="NovaPhone" class="h-12 w-auto max-w-[178px] object-contain">
</a>
<button id="admin-sidebar-close" type="button" class="rounded-lg p-2 text-slate-500 hover:bg-white/5 hover:text-white lg:hidden" aria-label="Đóng menu">
<svg class="size-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M6 6l12 12M18 6 6 18"/></svg>
</button>
</div>

<nav class="flex-1 space-y-1 overflow-y-auto px-3 py-5 [scrollbar-width:none]">
<a href="{{ route('admin.dashboard') }}" class="{{ $navItem }} {{ request()->routeIs('admin.dashboard') ? $navActive : $navIdle }}">
<svg class="size-[18px] shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
<span class="flex-1">Dashboard</span>
</a>
<a href="{{ route('admin.products.index') }}" class="{{ $navItem }} {{ request()->routeIs('admin.products.*') ? $navActive : $navIdle }}">
<svg class="size-[18px] shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linejoin="round" d="m3.5 7 8.5-4 8.5 4-8.5 4-8.5-4Z"/><path stroke-linejoin="round" d="m3.5 7v10l8.5 4 8.5-4V7M12 11v10"/></svg>
<span class="flex-1">Sản phẩm</span><span class="text-slate-600">›</span>
</a>
<a href="{{ route('admin.categories.index') }}" class="{{ $navItem }} {{ request()->routeIs('admin.categories.*') ? $navActive : $navIdle }}">
<svg class="size-[18px] shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 7.5h7l2 2h9v9.5a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V7.5Zm0 0V5a2 2 0 0 1 2-2h5l2 2h7a2 2 0 0 1 2 2v2.5"/></svg>
<span class="flex-1">Danh mục</span><span class="text-slate-600">›</span>
</a>
<a href="{{ route('admin.users.index') }}" class="{{ $navItem }} {{ request()->routeIs('admin.users.*') ? $navActive : $navIdle }}">
<svg class="size-[18px] shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm13 10v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>
<span class="flex-1">Khách hàng</span><span class="text-slate-600">›</span>
</a>
<a href="{{ route('admin.orders.index') }}" class="{{ $navItem }} {{ request()->routeIs('admin.orders.*') ? $navActive : $navIdle }}">
<svg class="size-[18px] shrink-0" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5h18l-2 16H5L3 5Zm4 0a5 5 0 0 1 10 0"/></svg>
<span class="flex-1">Đơn hàng</span><span class="text-slate-600">›</span>
</a>
@foreach ([
['Banner', 'M3 5h18v14H3V5Zm0 10 5-5 4 4 3-3 6 6M16 9h.01'],
['Khuyến mãi', 'm20 12-8 8-9-9V3h8l9 9ZM7.5 7.5h.01'],
['Tin công nghệ', 'M4 4h16v16H4V4Zm4 4h8M8 12h8M8 16h5'],
['Cài đặt', 'M12 15.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7Zm7.4-3.5a7.5 7.5 0 0 0-.1-1l2-1.5-2-3.4-2.4 1a8 8 0 0 0-1.8-1L14.8 3h-4l-.4 2.6a8 8 0 0 0-1.8 1l-2.4-1-2 3.4 2 1.5a7.5 7.5 0 0 0 0 2L4.2 14l2 3.4 2.4-1a8 8 0 0 0 1.8 1l.4 2.6h4l.4-2.6a8 8 0 0 0 1.8-1l2.4 1 2-3.4-2-1.5a7.5 7.5 0 0 0 .1-1Z'],
] as [$label, $path])
<a href="#" class="{{ $navItem }} {{ $navIdle }}">
<svg class="size-[18px] shrink-0" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $path }}"/></svg>
<span class="flex-1">{{ $label }}</span><span class="text-slate-600">›</span>
</a>
@endforeach
<a href="{{ route('admin.orders.statistics') }}" class="{{ $navItem }} {{ request()->routeIs('admin.orders.statistics') ? $navActive : $navIdle }}">
<svg class="size-[18px] shrink-0" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 20V10m5 10V4m6 16v-7m5 7V7"/></svg>
<span class="flex-1">Thống kê đơn hàng</span><span class="text-slate-600">›</span>
</a>
<a href="{{ route('admin.inventory.index') }}" class="{{ $navItem }} {{ request()->routeIs('admin.inventory.*') ? $navActive : $navIdle }}">
<svg class="size-[18px] shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m20.25 7.5-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z"/></svg>
<span class="flex-1">Quản lý kho</span><span class="text-slate-600">›</span>
</a>
<a href="{{ route('admin.revenue.index') }}" class="{{ $navItem }} {{ request()->routeIs('admin.revenue.*') ? $navActive : $navIdle }}">
<svg class="size-[18px] shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
<span class="flex-1">Thống kê doanh thu</span><span class="text-slate-600">›</span>
</a>
</nav>

<div class="shrink-0 px-4 pb-4">
<div class="rounded-2xl border border-blue-400/15 bg-gradient-to-br from-blue-500/[0.08] to-cyan-400/[0.03] p-4 shadow-[0_0_30px_rgba(37,99,235,.08)]">
<div class="mb-3 flex items-center gap-2.5">
<span class="flex size-8 items-center justify-center rounded-lg bg-blue-500/15 text-blue-400"><svg class="size-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linejoin="round" d="m12 3 3 6 6 .8-4.5 4.4 1 6.3L12 17.6l-5.5 2.9 1-6.3L3 9.8 9 9l3-6Z"/></svg></span>
<span class="text-[11px] font-bold uppercase tracking-wider text-slate-200">Nâng cấp gói Pro</span>
</div>
<p class="text-[10px] leading-relaxed text-slate-500">Mở khóa nhiều tính năng quản trị nâng cao.</p>
<button type="button" class="mt-3 w-full rounded-lg bg-gradient-to-r from-blue-600 to-blue-500 py-2 text-[11px] font-bold text-white shadow-lg shadow-blue-600/20">Nâng cấp ngay</button>
</div>
<p class="mt-4 text-[9px] uppercase tracking-wider text-slate-600">NovaPhone © {{ date('Y') }}</p>
</div>
</aside>

<div class="min-h-screen transition-all duration-300 lg:pl-64">
<header class="sticky top-0 z-30 flex h-[76px] items-center gap-3 border-b border-white/[0.06] bg-[#07111e]/90 px-4 backdrop-blur-xl sm:px-6">
<button id="admin-sidebar-open" type="button" class="rounded-xl p-2.5 text-slate-400 hover:bg-white/5 hover:text-white" aria-label="Mở menu">
<svg class="size-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M4 7h16M4 12h16M4 17h16"/></svg>
</button>
<div class="hidden min-w-[190px] sm:block">
<h1 class="text-base font-bold text-white">@yield('page-title', 'Quản trị')</h1>
<p class="mt-0.5 text-[10px] text-slate-500">@yield('page-subtitle', 'Hệ thống quản trị NovaPhone')</p>
</div>

<form method="GET" action="{{ route('admin.products.index') }}" class="relative mx-auto hidden w-full max-w-lg md:block">
<svg class="pointer-events-none absolute left-4 top-1/2 size-4 -translate-y-1/2 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="m20 20-4-4m2-5a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z"/></svg>
<input name="search" type="search" placeholder="Tìm kiếm đơn hàng, sản phẩm, khách hàng..." class="w-full rounded-xl border border-white/[0.07] bg-black/10 py-2.5 pl-11 pr-16 text-xs text-white outline-none placeholder:text-slate-600 focus:border-blue-500/40 focus:ring-2 focus:ring-blue-500/10">
<span class="absolute right-3 top-1/2 -translate-y-1/2 text-[9px] text-slate-600">Ctrl + K</span>
</form>

<div class="ml-auto flex items-center gap-1.5">
<button type="button" class="relative rounded-xl p-2.5 text-slate-400 hover:bg-white/5 hover:text-white" aria-label="Thông báo">
<svg class="size-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.9 18a3 3 0 0 1-5.8 0M18 9a6 6 0 1 0-12 0c0 7-3 7-3 7h18s-3 0-3-7Z"/></svg>
<span class="absolute right-1.5 top-1.5 size-2 rounded-full bg-blue-500 ring-2 ring-[#07111e]"></span>
</button>
<details class="group relative ml-1">
<summary class="flex cursor-pointer list-none items-center gap-2 rounded-xl px-2 py-1.5 hover:bg-white/5">
<span class="flex size-9 items-center justify-center overflow-hidden rounded-full border border-blue-400/20 bg-gradient-to-br from-blue-500/30 to-cyan-500/10 text-xs font-bold text-blue-200">{{ mb_strtoupper(mb_substr(Auth::user()->name, 0, 1)) }}</span>
<span class="hidden text-left xl:block"><span class="block max-w-28 truncate text-xs font-semibold text-white">{{ Auth::user()->name }}</span><span class="block text-[9px] text-slate-500">Quản trị viên</span></span>
<svg class="hidden size-3 text-slate-500 transition group-open:rotate-180 xl:block" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="m6 9 6 6 6-6"/></svg>
</summary>
<div class="absolute right-0 top-full mt-2 w-44 rounded-xl border border-white/10 bg-[#0b1523] p-2 shadow-2xl">
<a href="{{ route('home') }}" class="block rounded-lg px-3 py-2 text-xs text-slate-400 hover:bg-white/5 hover:text-white">Về trang chủ</a>
<form method="POST" action="{{ route('logout') }}">@csrf<button class="mt-1 w-full rounded-lg px-3 py-2 text-left text-xs text-red-400 hover:bg-red-500/10">Đăng xuất</button></form>
</div>
</details>
</div>
</header>

<main class="min-h-[calc(100vh-76px)] bg-[radial-gradient(circle_at_top_right,rgba(37,99,235,.06),transparent_28rem)] p-4 sm:p-5 lg:p-6">
@if (session('success'))<div class="mb-4 rounded-xl border border-emerald-500/20 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-400">{{ session('success') }}</div>@endif
@if ($errors->any())<div class="mb-4 rounded-xl border border-red-500/20 bg-red-500/10 px-4 py-3 text-sm text-red-400"><ul class="list-inside list-disc">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
@yield('content')
</main>
</div>

<script>
(() => {
const sidebar = document.getElementById('admin-sidebar');
const overlay = document.getElementById('admin-sidebar-overlay');
const open = () => { sidebar?.classList.remove('-translate-x-full'); overlay?.classList.remove('hidden'); };
const close = () => { if (window.innerWidth < 1024) sidebar?.classList.add('-translate-x-full'); overlay?.classList.add('hidden'); };
document.getElementById('admin-sidebar-open')?.addEventListener('click', open);
document.getElementById('admin-sidebar-close')?.addEventListener('click', close);
overlay?.addEventListener('click', close);
document.addEventListener('keydown', event => {
if ((event.ctrlKey || event.metaKey) && event.key.toLowerCase() === 'k') {
event.preventDefault(); document.querySelector('header input[type="search"]')?.focus();
}
});
})();
</script>
@stack('scripts')
</body>
</html>
