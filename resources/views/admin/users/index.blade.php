@extends('admin.layout')

@section('title', 'Người dùng')
@section('page-title', 'Quản lý người dùng')

@section('content')

{{-- Thanh công cụ: tìm kiếm + lọc --}}
<div class="mb-5 flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
    <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-1 flex-wrap items-center gap-2">
        <div class="relative min-w-[220px] flex-1">
            <input
                type="text" name="search" value="{{ $filters['search'] ?? '' }}"
                placeholder="Tìm theo tên, email hoặc số điện thoại..."
                class="w-full rounded-xl border border-white/10 bg-white/5 py-2.5 pl-10 pr-4 text-sm text-white outline-none transition-all duration-200 placeholder:text-gray-500 focus:border-brand-500 focus:ring-2 focus:ring-brand-500/25"
            >
            <svg class="pointer-events-none absolute left-3.5 top-1/2 size-4 -translate-y-1/2 text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35M17 10.5a6.5 6.5 0 1 1-13 0 6.5 6.5 0 0 1 13 0Z"/></svg>
        </div>

        <select name="role" class="rounded-xl border border-white/10 bg-white/5 px-3 py-2.5 text-sm text-white outline-none focus:border-brand-500">
            <option value="">Tất cả vai trò</option>
            <option value="user" @selected(($filters['role'] ?? null) === 'user')>Khách hàng</option>
            <option value="admin" @selected(($filters['role'] ?? null) === 'admin')>Quản trị viên</option>
        </select>

        <select name="status" class="rounded-xl border border-white/10 bg-white/5 px-3 py-2.5 text-sm text-white outline-none focus:border-brand-500">
            <option value="">Tất cả trạng thái</option>
            <option value="active" @selected(($filters['status'] ?? null) === 'active')>Đang hoạt động</option>
            <option value="blocked" @selected(($filters['status'] ?? null) === 'blocked')>Đã khóa</option>
        </select>

        <button type="submit" class="rounded-xl bg-white/5 px-4 py-2.5 text-sm font-semibold text-white transition-all duration-200 hover:bg-white/10">
            Lọc
        </button>

        @if (collect($filters)->filter()->isNotEmpty())
            <a href="{{ route('admin.users.index') }}" class="text-sm text-gray-500 transition-colors hover:text-gray-300">Xoá lọc</a>
        @endif
    </form>
</div>

{{-- Bảng người dùng --}}
<div class="overflow-x-auto rounded-2xl border border-white/5 bg-night-soft">
    <table class="w-full text-left text-sm">
        <thead>
            <tr class="border-b border-white/5 text-xs uppercase tracking-wider text-gray-500">
                <th class="px-4 py-3">Người dùng</th>
                <th class="px-4 py-3">Liên hệ</th>
                <th class="px-4 py-3 text-center">Vai trò</th>
                <th class="px-4 py-3 text-center">Trạng thái</th>
                <th class="px-4 py-3">Ngày tham gia</th>
                <th class="px-4 py-3 text-right">Hành động</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-white/5">
            @forelse ($users as $user)
                <tr class="transition-colors duration-150 hover:bg-white/[0.03]">
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-3">
                            <span class="flex size-10 shrink-0 items-center justify-center overflow-hidden rounded-full border border-blue-400/20 bg-gradient-to-br from-blue-500/30 to-cyan-500/10 text-sm font-bold text-blue-200">
                                @if ($user->avatar)
                                    <img src="{{ $user->avatar }}" alt="{{ $user->name }}" class="size-full object-cover">
                                @else
                                    {{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}
                                @endif
                            </span>
                            <div class="min-w-0">
                                <p class="truncate font-semibold text-white">{{ $user->name }}</p>
                                <p class="mt-0.5 text-xs text-gray-500">#{{ $user->id }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-3">
                        <p class="text-gray-300">{{ $user->email }}</p>
                        <p class="mt-0.5 text-xs text-gray-500">{{ $user->phone ?? '—' }}</p>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <span class="rounded-full px-3 py-1 text-xs font-bold
                            {{ $user->isAdmin() ? 'bg-violet-500/15 text-violet-300' : 'bg-white/5 text-gray-400' }}">
                            {{ $user->isAdmin() ? 'Quản trị' : 'Khách hàng' }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <span class="rounded-full px-3 py-1 text-xs font-bold
                            {{ $user->isBlocked() ? 'bg-red-500/15 text-red-400' : 'bg-green-500/15 text-green-400' }}">
                            {{ $user->isBlocked() ? 'Đã khóa' : 'Hoạt động' }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-gray-400">{{ $user->created_at?->format('d/m/Y') ?? '—' }}</td>
                    <td class="px-4 py-3 text-right">
                        <div class="inline-flex items-center gap-1.5">
                            <a href="{{ route('admin.users.show', $user) }}"
                               class="flex size-8 items-center justify-center rounded-lg bg-white/5 text-gray-400 transition-all duration-200 hover:bg-brand-600/20 hover:text-brand-400"
                               title="Xem chi tiết">
                                <svg class="size-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/></svg>
                            </a>

                            @if ($user->id !== auth()->id())
                                <form method="POST" action="{{ route('admin.users.toggle-status', $user) }}"
                                      onsubmit="return confirm('{{ $user->isBlocked() ? 'Mở khóa' : 'Khóa' }} tài khoản &quot;{{ $user->name }}&quot;?')">
                                    @csrf
                                    @method('PATCH')
                                    @if ($user->isBlocked())
                                        <button type="submit"
                                                class="flex size-8 items-center justify-center rounded-lg bg-white/5 text-gray-400 transition-all duration-200 hover:bg-green-500/20 hover:text-green-400"
                                                title="Mở khóa">
                                            <svg class="size-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 10.5V6.75a4.5 4.5 0 1 1 9 0v3.75M3.75 21.75h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H3.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z"/></svg>
                                        </button>
                                    @else
                                        <button type="submit"
                                                class="flex size-8 items-center justify-center rounded-lg bg-white/5 text-gray-400 transition-all duration-200 hover:bg-red-500/20 hover:text-red-400"
                                                title="Khóa">
                                            <svg class="size-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z"/></svg>
                                        </button>
                                    @endif
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-4 py-12 text-center text-gray-500">
                        Không tìm thấy người dùng nào.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Pagination --}}
<div class="mt-5">
    {{ $users->links() }}
</div>

@endsection
