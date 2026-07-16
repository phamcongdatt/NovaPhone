@extends('admin.layout')
@section('page-title', 'Quản lý Thương hiệu')

@section('content')
<div class="flex items-center justify-between mb-6">
    <form method="GET" action="{{ route('admin.brands.index') }}" class="relative w-full max-w-sm">
        <svg class="absolute left-3 top-1/2 -translate-y-1/2 size-4 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="m20 20-4-4m2-5a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z"/></svg>
        <input name="search" value="{{ request('search') }}" type="search" placeholder="Tìm kiếm thương hiệu..." class="w-full rounded-xl border border-white/10 bg-[#0b1523] py-2.5 pl-10 pr-4 text-sm text-white outline-none placeholder:text-slate-600 focus:border-blue-500/50 focus:ring-1 focus:ring-blue-500/50">
    </form>
    
    <a href="{{ route('admin.brands.create') }}" class="flex items-center gap-2 rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-blue-500">
        <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
        Thêm thương hiệu
    </a>
</div>

<div class="overflow-hidden rounded-2xl border border-white/10 bg-[#0b1523] shadow-xl">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-slate-300">
            <thead class="border-b border-white/10 bg-white/[0.02] text-xs font-semibold uppercase tracking-wider text-slate-400">
                <tr>
                    <th class="px-5 py-4">Thương hiệu</th>
                    <th class="px-5 py-4 text-center">Số sản phẩm</th>
                    <th class="px-5 py-4 text-center">Trạng thái</th>
                    <th class="px-5 py-4 text-right">Thao tác</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
                @forelse ($brands as $brand)
                    <tr class="transition-colors hover:bg-white/[0.02]">
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-4">
                                <div class="flex size-12 shrink-0 items-center justify-center rounded-lg border border-white/10 bg-white p-1">
                                    <img src="{{ $brand->logo ? (str_starts_with($brand->logo, 'images/') ? asset($brand->logo) : asset('storage/' . $brand->logo)) : asset('images/placeholder.svg') }}" alt="{{ $brand->name }}" class="h-full w-full rounded-md object-contain">
                                </div>
                                <div>
                                    <h4 class="font-semibold text-white">{{ $brand->name }}</h4>
                                    <p class="text-[11px] text-slate-500">Slug: {{ $brand->slug }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-4 text-center">
                            <span class="inline-flex size-6 items-center justify-center rounded-full bg-white/10 text-xs font-semibold text-white">{{ $brand->products_count }}</span>
                        </td>
                        <td class="px-5 py-4 text-center">
                            @if ($brand->is_active)
                                <span class="inline-flex rounded-full border border-emerald-500/20 bg-emerald-500/10 px-2.5 py-0.5 text-[10px] font-bold uppercase tracking-wider text-emerald-400">Hoạt động</span>
                            @else
                                <span class="inline-flex rounded-full border border-slate-500/20 bg-slate-500/10 px-2.5 py-0.5 text-[10px] font-bold uppercase tracking-wider text-slate-400">Đã ẩn</span>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.brands.edit', $brand) }}" class="rounded-lg p-2 text-slate-400 transition-colors hover:bg-white/5 hover:text-blue-400" title="Sửa">
                                    <svg class="size-4.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.89.11l-3.152-1.05a.125.125 0 0 1-.082-.082l-1.05-3.152a4.5 4.5 0 0 1 1.11-1.89l12.63-12.63Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 7.125 16.875 4.5"/></svg>
                                </a>
                                <form action="{{ route('admin.brands.destroy', $brand) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xoá thương hiệu này không?');" class="inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="rounded-lg p-2 text-slate-400 transition-colors hover:bg-white/5 hover:text-red-400" title="Xoá">
                                        <svg class="size-4.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-5 py-8 text-center text-slate-500">
                            Chưa có thương hiệu nào.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if ($brands->hasPages())
        <div class="border-t border-white/10 px-5 py-4">
            {{ $brands->appends(request()->query())->links('pagination::tailwind') }}
        </div>
    @endif
</div>
@endsection
