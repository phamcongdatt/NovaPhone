@extends('layouts.app')

@section('title', 'Tài khoản của tôi - NovaPhone')

@section('content')
<div class="space-y-4">
    <div class="flex items-center gap-2 text-sm text-[#8b8b8b]">
        <a href="{{ route('home') }}" class="hover:text-black">Trang chủ</a>
        <span>/</span>
        <span class="text-black">Tài khoản</span>
    </div>

    <div class="grid gap-4 lg:grid-cols-[280px_1fr]">
        <aside class="rounded-[28px] border border-[#ece8e2] bg-white p-5 shadow-[0_10px_35px_rgba(0,0,0,.04)]">
            <h2 class="text-sm font-bold text-[#171717]">Tài khoản của tôi</h2>
            <nav class="mt-4 space-y-1">
                <a href="{{ route('account.show') }}" class="block rounded-[14px] bg-black px-3 py-2.5 text-sm font-medium text-white">Thông tin tài khoản</a>
                <a href="{{ route('orders.index') }}" class="block rounded-[14px] px-3 py-2.5 text-sm font-medium text-[#111] transition hover:bg-[#f7f5f2]">Đơn hàng của tôi</a>
                <a href="{{ route('wishlist.index') }}" class="block rounded-[14px] px-3 py-2.5 text-sm font-medium text-[#111] transition hover:bg-[#f7f5f2]">Sản phẩm yêu thích</a>
                <a href="{{ route('account.show') }}#ma-giam-gia-cua-toi" class="block rounded-[14px] px-3 py-2.5 text-sm font-medium text-[#111] transition hover:bg-[#f7f5f2]">Mã giảm giá của tôi</a>
                <a href="{{ route('account.addresses') }}" class="block rounded-[14px] px-3 py-2.5 text-sm font-medium text-[#111] transition hover:bg-[#f7f5f2]">Địa chỉ của tôi</a>
                <a href="{{ route('password.change') }}" class="block rounded-[14px] px-3 py-2.5 text-sm font-medium text-[#111] transition hover:bg-[#f7f5f2]">Đổi mật khẩu</a>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full rounded-[14px] px-3 py-2.5 text-left text-sm font-medium text-red-600 transition hover:bg-red-50">Đăng xuất</button>
                </form>
            </nav>
        </aside>

        <section class="space-y-4">
            <div class="rounded-[28px] border border-[#ece8e2] bg-white p-5 shadow-[0_10px_35px_rgba(0,0,0,.04)] sm:p-6">
                @if (session('status'))
                    <div class="mb-4 rounded-[16px] border border-green-200 bg-green-50 p-4 text-sm text-green-800">{{ session('status') }}</div>
                @endif

                <div class="flex flex-col items-start justify-between gap-4 sm:flex-row sm:items-center">
                    <div>
                        <h1 class="text-2xl font-bold text-[#171717]">Thông tin tài khoản</h1>
                        <p class="mt-1 text-sm text-[#8b8b8b]">Quản lý thông tin cá nhân và các thiết lập bảo mật.</p>
                    </div>
                    <a href="{{ route('profile.edit') }}" class="rounded-full border-2 border-[#e8e4de] bg-white px-6 py-2 text-sm font-semibold text-[#111] transition hover:border-black hover:bg-[#fbfaf8]">Sửa thông tin</a>
                </div>

                <div class="mt-6 grid gap-6 md:grid-cols-[180px_1fr]">
                    <div class="flex justify-center">
                        @if ($user->avatar)
                            <img src="{{ str_starts_with($user->avatar, 'http') ? $user->avatar : asset($user->avatar) }}" alt="{{ $user->name }}" class="size-40 rounded-[24px] object-cover">
                        @else
                            <div class="flex size-40 items-center justify-center rounded-[24px] bg-gradient-to-br from-[#f0eeea] to-[#e5ddd0] text-6xl font-bold text-[#555]">{{ strtoupper(mb_substr($user->name, 0, 1)) }}</div>
                        @endif
                    </div>
                    <dl class="space-y-3">
                        <div class="rounded-[16px] border border-[#ece8e2] bg-[#fbfaf8] px-4 py-3">
                            <dt class="text-xs text-[#8b8b8b]">Họ và tên</dt>
                            <dd class="font-semibold text-[#111]">{{ $user->name }}</dd>
                        </div>
                        <div class="rounded-[16px] border border-[#ece8e2] bg-[#fbfaf8] px-4 py-3">
                            <dt class="text-xs text-[#8b8b8b]">Email</dt>
                            <dd class="font-semibold text-[#111]">{{ $user->email }}</dd>
                        </div>
                        <div class="rounded-[16px] border border-[#ece8e2] bg-[#fbfaf8] px-4 py-3">
                            <dt class="text-xs text-[#8b8b8b]">Số điện thoại</dt>
                            <dd class="font-semibold text-[#111]">{{ $user->phone ?: 'Chưa cập nhật' }}</dd>
                        </div>
                        <div class="rounded-[16px] border border-[#ece8e2] bg-[#fbfaf8] px-4 py-3">
                            <dt class="text-xs text-[#8b8b8b]">Thành viên từ</dt>
                            <dd class="font-semibold text-[#111]">{{ $user->created_at?->format('d/m/Y') }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <div class="grid gap-3 sm:grid-cols-2">
                <a href="{{ route('orders.index') }}" class="rounded-[20px] border border-[#ece8e2] bg-white p-5 shadow-[0_8px_30px_rgba(0,0,0,.03)] transition hover:-translate-y-0.5 hover:shadow-md">
                    <h2 class="font-semibold text-[#171717]">Theo dõi đơn hàng</h2>
                    <p class="mt-1 text-sm text-[#8b8b8b]">Xem trạng thái và chi tiết các đơn đã đặt.</p>
                </a>
                <a href="{{ route('account.addresses') }}" class="rounded-[20px] border border-[#ece8e2] bg-white p-5 shadow-[0_8px_30px_rgba(0,0,0,.03)] transition hover:-translate-y-0.5 hover:shadow-md">
                    <h2 class="font-semibold text-[#171717]">Địa chỉ giao hàng</h2>
                    <p class="mt-1 text-sm text-[#8b8b8b]">Quản lý địa chỉ để thanh toán nhanh hơn.</p>
                </a>
            </div>

            <section id="ma-giam-gia-cua-toi" class="scroll-mt-24 rounded-[28px] border border-[#ece8e2] bg-white p-5 shadow-[0_10px_35px_rgba(0,0,0,.04)] sm:p-6">
                <div class="flex flex-col items-start justify-between gap-4 sm:flex-row sm:items-center">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.16em] text-[#0a84ff]">Ví ưu đãi</p>
                        <h2 class="mt-1 text-xl font-bold text-[#171717]">Mã giảm giá của tôi</h2>
                        <p class="mt-1 text-sm text-[#8b8b8b]">Các mã bạn đã lưu được tách riêng khỏi ưu đãi chung.</p>
                    </div>
                    <a href="{{ route('coupons.index') }}" class="rounded-full border border-[#e8e4de] bg-white px-4 py-2 text-sm font-semibold text-[#171717] transition hover:border-black hover:bg-[#fbfaf8]">Khám phá ưu đãi chung</a>
                </div>

                <div class="mt-5 grid gap-3 lg:grid-cols-2">
                    @forelse ($savedCoupons as $coupon)
                        <article class="overflow-hidden rounded-[20px] border border-[#ece8e2] bg-[#fbfaf8]">
                            <div class="flex flex-col gap-4 p-4 sm:flex-row sm:items-center">
                                <div class="rounded-[16px] bg-[#171717] px-4 py-3 text-white sm:w-36">
                                    <p class="text-[10px] font-semibold uppercase tracking-[0.14em] text-white/65">Đã lưu</p>
                                    <p class="mt-1 text-lg font-bold leading-tight">
                                        {{ $coupon->type === 'percent' ? 'Giảm ' . rtrim(rtrim((string) $coupon->value, '0'), '.') . '%' : 'Giảm ' . number_format((float) $coupon->value, 0, ',', '.') . 'đ' }}
                                    </p>
                                    @if ($coupon->max_discount)
                                        <p class="mt-1 text-[10px] text-white/70">Tối đa {{ number_format((float) $coupon->max_discount, 0, ',', '.') }}đ</p>
                                    @endif
                                </div>

                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center justify-between gap-3">
                                        <h3 class="truncate text-base font-bold text-[#171717]">{{ $coupon->code }}</h3>
                                        <span class="shrink-0 rounded-full border border-[#d9e7ff] bg-[#eef6ff] px-2.5 py-1 text-[10px] font-semibold text-[#0a5ec2]">Trong ví</span>
                                    </div>
                                    @if ($coupon->description)
                                        <p class="mt-1 line-clamp-2 text-sm leading-5 text-[#6f6f6f]">{{ $coupon->description }}</p>
                                    @endif
                                    <div class="mt-3 flex flex-wrap gap-x-3 gap-y-1 text-[11px] text-[#777]">
                                        @if ($coupon->min_order_amount > 0)
                                            <span>Đơn từ {{ number_format((float) $coupon->min_order_amount, 0, ',', '.') }}đ</span>
                                        @endif
                                        <span>{{ $coupon->expires_at ? 'HSD ' . $coupon->expires_at->format('d/m/Y') : 'Không giới hạn thời gian' }}</span>
                                    </div>
                                </div>
                            </div>
                        </article>
                    @empty
                        <div class="lg:col-span-2 rounded-[20px] border-2 border-dashed border-[#e5e1db] bg-[#fbfaf8] px-5 py-10 text-center">
                            <p class="text-sm font-semibold text-[#242424]">Bạn chưa lưu mã giảm giá nào.</p>
                            <p class="mt-1 text-sm text-[#8b8b8b]">Khám phá ưu đãi chung và lưu mã phù hợp để dùng khi thanh toán.</p>
                            <a href="{{ route('coupons.index') }}" class="mt-4 inline-flex rounded-full bg-black px-4 py-2 text-sm font-semibold text-white transition hover:bg-[#222]">Xem ưu đãi chung</a>
                        </div>
                    @endforelse
                </div>
            </section>
        </section>
    </div>
</div>
@endsection
