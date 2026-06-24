@extends('admin.layout')

@section('title', 'Chi tiết người dùng')
@section('page-title', 'Chi tiết người dùng')

@section('content')

<div class="mb-5 flex items-center justify-between gap-3">
    <a href="{{ route('admin.users.index') }}" class="inline-flex items-center gap-2 text-sm text-gray-400 transition-colors hover:text-white">
        <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
        Quay lại danh sách
    </a>

    @if ($user->id !== auth()->id())
        <form method="POST" action="{{ route('admin.users.toggle-status', $user) }}"
              onsubmit="return confirm('{{ $user->isBlocked() ? 'Mở khóa' : 'Khóa' }} tài khoản &quot;{{ $user->name }}&quot;?')">
            @csrf
            @method('PATCH')
            <button type="submit"
                    class="inline-flex items-center gap-2 rounded-xl px-4 py-2.5 text-sm font-bold text-white shadow-lg transition-all duration-200 hover:-translate-y-0.5
                           {{ $user->isBlocked() ? 'bg-green-600 shadow-green-600/25 hover:bg-green-500' : 'bg-red-600 shadow-red-600/25 hover:bg-red-500' }}">
                @if ($user->isBlocked())
                    <svg class="size-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 10.5V6.75a4.5 4.5 0 1 1 9 0v3.75M3.75 21.75h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H3.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z"/></svg>
                    Mở khóa tài khoản
                @else
                    <svg class="size-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z"/></svg>
                    Khóa tài khoản
                @endif
            </button>
        </form>
    @endif
</div>

<div class="grid grid-cols-1 gap-5 lg:grid-cols-3">
    {{-- Hồ sơ --}}
    <div class="rounded-2xl border border-white/5 bg-night-soft p-6">
        <div class="flex flex-col items-center text-center">
            <span class="flex size-20 items-center justify-center overflow-hidden rounded-full border border-blue-400/20 bg-gradient-to-br from-blue-500/30 to-cyan-500/10 text-2xl font-bold text-blue-200">
                @if ($user->avatar)
                    <img src="{{ $user->avatar }}" alt="{{ $user->name }}" class="size-full object-cover">
                @else
                    {{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}
                @endif
            </span>
            <h2 class="mt-4 text-lg font-bold text-white">{{ $user->name }}</h2>
            <p class="text-sm text-gray-500">{{ $user->email }}</p>

            <div class="mt-4 flex items-center gap-2">
                <span class="rounded-full px-3 py-1 text-xs font-bold
                    {{ $user->isAdmin() ? 'bg-violet-500/15 text-violet-300' : 'bg-white/5 text-gray-400' }}">
                    {{ $user->isAdmin() ? 'Quản trị viên' : 'Khách hàng' }}
                </span>
                <span class="rounded-full px-3 py-1 text-xs font-bold
                    {{ $user->isBlocked() ? 'bg-red-500/15 text-red-400' : 'bg-green-500/15 text-green-400' }}">
                    {{ $user->isBlocked() ? 'Đã khóa' : 'Đang hoạt động' }}
                </span>
            </div>
        </div>
    </div>

    {{-- Thông tin chi tiết --}}
    <div class="rounded-2xl border border-white/5 bg-night-soft p-6 lg:col-span-2">
        <h3 class="mb-4 text-sm font-bold uppercase tracking-wider text-gray-500">Thông tin tài khoản</h3>
        <dl class="grid grid-cols-1 gap-x-6 gap-y-4 sm:grid-cols-2">
            <div>
                <dt class="text-xs text-gray-500">Mã người dùng</dt>
                <dd class="mt-1 font-semibold text-white">#{{ $user->id }}</dd>
            </div>
            <div>
                <dt class="text-xs text-gray-500">Số điện thoại</dt>
                <dd class="mt-1 font-semibold text-white">{{ $user->phone ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-xs text-gray-500">Email đã xác thực</dt>
                <dd class="mt-1 font-semibold text-white">
                    {{ $user->email_verified_at ? $user->email_verified_at->format('d/m/Y H:i') : 'Chưa xác thực' }}
                </dd>
            </div>
            <div>
                <dt class="text-xs text-gray-500">Phương thức đăng nhập</dt>
                <dd class="mt-1 font-semibold text-white">{{ $user->provider ? ucfirst($user->provider) : 'Email / Mật khẩu' }}</dd>
            </div>
            <div>
                <dt class="text-xs text-gray-500">Số đơn hàng</dt>
                <dd class="mt-1 font-semibold text-white">{{ $user->orders_count }}</dd>
            </div>
            <div>
                <dt class="text-xs text-gray-500">Số đánh giá</dt>
                <dd class="mt-1 font-semibold text-white">{{ $user->reviews_count }}</dd>
            </div>
            <div>
                <dt class="text-xs text-gray-500">Ngày tham gia</dt>
                <dd class="mt-1 font-semibold text-white">{{ $user->created_at?->format('d/m/Y H:i') ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-xs text-gray-500">Cập nhật gần nhất</dt>
                <dd class="mt-1 font-semibold text-white">{{ $user->updated_at?->format('d/m/Y H:i') ?? '—' }}</dd>
            </div>
        </dl>
    </div>
</div>

@endsection
