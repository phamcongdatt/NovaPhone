@extends('layouts.app')

@section('title', 'Tài khoản của tôi - NovaPhone')

@section('content')
@php
    $user = auth()->user();
@endphp

<section class="bg-night py-10 sm:py-14">
    <div class="mx-auto max-w-7xl px-4 sm:px-6">
        <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.22em] text-brand-400">NovaPhone ID</p>
                <h1 class="mt-2 text-3xl font-extrabold tracking-tight text-white">Tài khoản của tôi</h1>
                <p class="mt-2 max-w-2xl text-sm leading-relaxed text-gray-400">
                    Quản lý thông tin đăng nhập và trạng thái tài khoản NovaPhone.
                </p>
            </div>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                        class="inline-flex items-center justify-center rounded-xl border border-white/10 bg-white/5 px-4 py-2.5 text-sm font-bold text-gray-200 transition-all duration-200 ease-in-out hover:border-red-400/40 hover:bg-red-500/10 hover:text-red-200">
                    Đăng xuất
                </button>
            </form>
        </div>

        <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_360px]">
            <div class="rounded-2xl border border-white/10 bg-night-soft p-5 shadow-2xl shadow-black/30 sm:p-6">
                <div class="flex flex-col gap-5 sm:flex-row sm:items-center">
                    <div class="flex size-20 shrink-0 items-center justify-center rounded-2xl bg-brand-600 text-2xl font-extrabold text-white shadow-lg shadow-brand-600/30">
                        {{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}
                    </div>
                    <div class="min-w-0">
                        <h2 class="truncate text-2xl font-extrabold text-white">{{ $user->name }}</h2>
                        <p class="mt-1 text-sm text-gray-400">{{ $user->email }}</p>
                        <div class="mt-3 flex flex-wrap gap-2">
                            <span class="rounded-full border border-brand-400/25 bg-brand-500/10 px-3 py-1 text-xs font-bold text-brand-200">
                                {{ $user->role === 'admin' ? 'Quản trị viên' : 'Khách hàng' }}
                            </span>
                            <span class="rounded-full border {{ $user->status === 'active' ? 'border-green-400/25 bg-green-500/10 text-green-200' : 'border-red-400/25 bg-red-500/10 text-red-200' }} px-3 py-1 text-xs font-bold">
                                {{ $user->status === 'active' ? 'Đang hoạt động' : 'Đã khóa' }}
                            </span>
                            <span class="rounded-full border {{ $user->hasVerifiedEmail() ? 'border-green-400/25 bg-green-500/10 text-green-200' : 'border-amber-400/25 bg-amber-500/10 text-amber-200' }} px-3 py-1 text-xs font-bold">
                                {{ $user->hasVerifiedEmail() ? 'Email đã xác thực' : 'Chưa xác thực email' }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="mt-8 grid gap-4 sm:grid-cols-2">
                    <div class="rounded-xl border border-white/10 bg-white/[0.03] p-4">
                        <p class="text-xs font-bold uppercase tracking-wider text-gray-500">Họ tên</p>
                        <p class="mt-2 break-words text-sm font-semibold text-white">{{ $user->name }}</p>
                    </div>
                    <div class="rounded-xl border border-white/10 bg-white/[0.03] p-4">
                        <p class="text-xs font-bold uppercase tracking-wider text-gray-500">Email</p>
                        <p class="mt-2 break-words text-sm font-semibold text-white">{{ $user->email }}</p>
                    </div>
                    <div class="rounded-xl border border-white/10 bg-white/[0.03] p-4">
                        <p class="text-xs font-bold uppercase tracking-wider text-gray-500">Số điện thoại</p>
                        <p class="mt-2 break-words text-sm font-semibold text-white">{{ $user->phone ?: 'Chưa cập nhật' }}</p>
                    </div>
                    <div class="rounded-xl border border-white/10 bg-white/[0.03] p-4">
                        <p class="text-xs font-bold uppercase tracking-wider text-gray-500">Ngày tham gia</p>
                        <p class="mt-2 text-sm font-semibold text-white">{{ $user->created_at?->format('d/m/Y') }}</p>
                    </div>
                </div>
            </div>

            <aside class="space-y-4">
                <div class="rounded-2xl border border-white/10 bg-night-soft p-5 shadow-2xl shadow-black/30">
                    <h2 class="text-base font-extrabold text-white">Bảo mật tài khoản</h2>
                    <p class="mt-2 text-sm leading-relaxed text-gray-400">
                        Email xác thực giúp bảo vệ đơn hàng và thông tin thành viên của bạn.
                    </p>

                    @unless ($user->hasVerifiedEmail())
                        <form method="POST" action="{{ route('verification.send') }}" class="mt-4">
                            @csrf
                            <button type="submit"
                                    class="w-full rounded-xl bg-brand-600 px-4 py-2.5 text-sm font-bold text-white transition-all duration-200 ease-in-out hover:bg-brand-500">
                                Gửi lại email xác thực
                            </button>
                        </form>
                    @endunless
                </div>

                <div class="rounded-2xl border border-white/10 bg-night-soft p-5 shadow-2xl shadow-black/30">
                    <h2 class="text-base font-extrabold text-white">Lối tắt</h2>
                    <div class="mt-4 grid gap-2">
                        <a href="{{ route('home') }}#san-pham" class="rounded-xl bg-white/[0.04] px-4 py-3 text-sm font-semibold text-gray-200 transition-all duration-200 ease-in-out hover:bg-white/[0.08] hover:text-white">
                            Tiếp tục mua sắm
                        </a>
                        <a href="{{ route('home') }}#flash-sale" class="rounded-xl bg-white/[0.04] px-4 py-3 text-sm font-semibold text-gray-200 transition-all duration-200 ease-in-out hover:bg-white/[0.08] hover:text-white">
                            Xem khuyến mãi
                        </a>
                    </div>
                </div>
            </aside>
        </div>
    </div>
</section>
@endsection
