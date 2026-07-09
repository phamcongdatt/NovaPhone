@extends('admin.layout')

@section('page-title', 'Quản lý Flash Sale')
@section('page-subtitle', 'Danh sách các chiến dịch khuyến mãi chớp nhoáng')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div>
        <h2 class="text-xl font-bold text-white">Chiến dịch Flash Sale</h2>
        <p class="mt-1 text-sm text-slate-400">Tạo và quản lý các sự kiện flash sale hiển thị trên trang chủ.</p>
    </div>
    <a href="{{ route('admin.flash-sales.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-lg shadow-blue-500/20 transition-all hover:bg-blue-500 hover:-translate-y-0.5">
        <svg class="size-4.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
        Tạo chiến dịch mới
    </a>
</div>

<div class="overflow-hidden rounded-2xl border border-white/5 bg-[#0b1523] shadow-xl">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-slate-300">
            <thead class="border-b border-white/5 bg-white/5 text-xs uppercase text-slate-400">
                <tr>
                    <th class="px-6 py-4 font-semibold">Tên chiến dịch</th>
                    <th class="px-6 py-4 font-semibold">Thời gian</th>
                    <th class="px-6 py-4 font-semibold">Sản phẩm</th>
                    <th class="px-6 py-4 font-semibold text-center">Trạng thái</th>
                    <th class="px-6 py-4 font-semibold text-right">Thao tác</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
                @forelse($flashSales as $campaign)
                    @php
                        $now = now();
                        $statusText = '';
                        $statusClass = '';
                        if (!$campaign->is_active) {
                            $statusText = 'Tạm dừng';
                            $statusClass = 'bg-slate-500/10 text-slate-400 border-slate-500/20';
                        } elseif ($campaign->start_time > $now) {
                            $statusText = 'Sắp diễn ra';
                            $statusClass = 'bg-amber-500/10 text-amber-400 border-amber-500/20';
                        } elseif ($campaign->end_time < $now) {
                            $statusText = 'Đã kết thúc';
                            $statusClass = 'bg-red-500/10 text-red-400 border-red-500/20';
                        } else {
                            $statusText = 'Đang diễn ra';
                            $statusClass = 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20 animate-pulse';
                        }
                    @endphp
                    <tr class="transition-colors hover:bg-white/[0.02]">
                        <td class="px-6 py-4 font-medium text-white">{{ $campaign->name }}</td>
                        <td class="px-6 py-4">
                            <div class="text-[13px] text-slate-300">{{ $campaign->start_time->format('H:i d/m/Y') }}</div>
                            <div class="text-[13px] text-slate-500">đến {{ $campaign->end_time->format('H:i d/m/Y') }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center gap-1.5 rounded-lg bg-blue-500/10 px-2.5 py-1 text-xs font-medium text-blue-400">
                                {{ $campaign->items_count }} sản phẩm
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-block rounded-full border px-2.5 py-1 text-xs font-semibold {{ $statusClass }}">
                                {{ $statusText }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.flash-sales.edit', $campaign) }}" class="rounded-lg bg-white/5 p-2 text-slate-400 transition-colors hover:bg-blue-500/10 hover:text-blue-400" title="Chỉnh sửa">
                                    <svg class="size-4.5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125"/></svg>
                                </a>
                                <form action="{{ route('admin.flash-sales.destroy', $campaign) }}" method="POST" class="inline-block" onsubmit="return confirm('Bạn có chắc chắn muốn xóa chiến dịch này?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="rounded-lg bg-white/5 p-2 text-slate-400 transition-colors hover:bg-red-500/10 hover:text-red-400" title="Xóa">
                                        <svg class="size-4.5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-slate-500">
                            Chưa có chiến dịch Flash Sale nào.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($flashSales->hasPages())
        <div class="border-t border-white/5 p-4">
            {{ $flashSales->links() }}
        </div>
    @endif
</div>
@endsection
