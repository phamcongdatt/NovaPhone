@extends('admin.layout')
@section('title', 'Hoàn hàng')
@section('page-title', 'Hoàn hàng / hoàn tiền')
@section('content')
<form method="GET" class="mb-5 flex flex-wrap gap-3">
    <input name="search" value="{{ request('search') }}" placeholder="Mã phiếu hoặc mã đơn..." class="min-w-64 flex-1 rounded-xl border border-white/10 bg-white/5 px-4 py-2.5 text-sm text-white outline-none">
    <select name="status" class="rounded-xl border border-white/10 bg-night-soft px-4 py-2.5 text-sm text-white"><option value="">Tất cả trạng thái</option>@foreach($statusLabels as $value => $label)<option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>@endforeach</select>
    <button class="rounded-xl bg-white/10 px-5 py-2.5 text-sm font-semibold text-white">Lọc</button>
</form>
<div class="overflow-x-auto rounded-2xl border border-white/5 bg-night-soft">
<table class="w-full text-left text-sm"><thead><tr class="border-b border-white/5 text-xs uppercase text-gray-500"><th class="px-4 py-3">Mã phiếu</th><th class="px-4 py-3">Đơn hàng</th><th class="px-4 py-3">Khách hàng</th><th class="px-4 py-3">Trạng thái</th><th class="px-4 py-3">Ngày tạo</th><th class="px-4 py-3"></th></tr></thead>
<tbody class="divide-y divide-white/5">@forelse($returnRequests as $returnRequest)<tr class="hover:bg-white/[.03]"><td class="px-4 py-3 font-semibold text-white">{{ $returnRequest->return_code }}</td><td class="px-4 py-3 text-gray-300">{{ $returnRequest->order->order_code }}</td><td class="px-4 py-3 text-gray-300">{{ $returnRequest->user->name }}</td><td class="px-4 py-3"><span class="rounded-full bg-brand-500/15 px-3 py-1 text-xs font-semibold text-brand-300">{{ $statusLabels[$returnRequest->status] }}</span></td><td class="px-4 py-3 text-gray-400">{{ $returnRequest->created_at->format('d/m/Y H:i') }}</td><td class="px-4 py-3 text-right"><a href="{{ route('admin.returns.show', $returnRequest) }}" class="font-semibold text-brand-400">Xử lý →</a></td></tr>@empty<tr><td colspan="6" class="px-4 py-12 text-center text-gray-500">Chưa có yêu cầu hoàn hàng.</td></tr>@endforelse</tbody></table>
</div><div class="mt-5">{{ $returnRequests->links() }}</div>
@endsection
