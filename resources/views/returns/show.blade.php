@extends('layouts.app')

@section('title', 'Theo dõi hoàn hàng - NovaPhone')

@section('content')
@php
    $labels = \App\Models\ReturnRequest::STATUS_LABELS;
    $steps = ['requested' => 1, 'return_shipping' => 2, 'store_received' => 3, 'approved' => 4, 'completed' => 5];
    $current = $steps[$returnRequest->status] ?? 0;
@endphp
<div class="mx-auto max-w-5xl space-y-5">
    <a href="{{ route('orders.show', $returnRequest->order) }}" class="text-sm font-semibold text-[#1677d2]">← Đơn {{ $returnRequest->order->order_code }}</a>
    <section class="rounded-[28px] border border-[#ece8e2] bg-white p-6 shadow-sm">
        <div class="flex flex-wrap items-start justify-between gap-4"><div><p class="text-xs font-semibold uppercase tracking-wider text-[#888]">Phiếu hoàn hàng</p><h1 class="mt-2 text-2xl font-bold">{{ $returnRequest->return_code }}</h1></div><span class="rounded-full bg-blue-50 px-4 py-2 text-sm font-semibold text-blue-700">{{ $labels[$returnRequest->status] }}</span></div>
        @if ($returnRequest->status === 'rejected')<div class="mt-5 rounded-2xl border border-red-200 bg-red-50 p-4 text-sm text-red-700"><strong>Cửa hàng từ chối:</strong> {{ $returnRequest->admin_note }}</div>@endif
        <ol class="mt-7 grid gap-3 sm:grid-cols-5">
            @foreach (['Tạo yêu cầu', 'Gửi trả hàng', 'Cửa hàng nhận', 'Cửa hàng duyệt', 'Hoàn tiền'] as $index => $label)
                <li class="rounded-2xl border p-3 text-center text-xs font-semibold {{ $current >= $index + 1 ? 'border-emerald-200 bg-emerald-50 text-emerald-700' : 'border-[#eeeae4] text-[#999]' }}"><span class="mx-auto mb-2 grid size-7 place-items-center rounded-full bg-white">{{ $index + 1 }}</span>{{ $label }}</li>
            @endforeach
        </ol>
    </section>

    @if ($returnRequest->status === 'requested')
        <section class="rounded-[28px] border border-amber-200 bg-amber-50 p-6">
            <h2 class="font-bold text-amber-900">Đã gửi hàng về cửa hàng?</h2><p class="mt-1 text-sm text-amber-800">Nhập đơn vị vận chuyển và mã vận đơn để cửa hàng theo dõi kiện hoàn.</p>
            <form method="POST" action="{{ route('returns.shipped', $returnRequest) }}" class="mt-4 grid gap-3 sm:grid-cols-[1fr_1fr_auto]">@csrf
                <input name="shipping_carrier" required placeholder="Đơn vị vận chuyển" class="rounded-xl border border-amber-200 px-4 py-3">
                <input name="tracking_code" required placeholder="Mã vận đơn" class="rounded-xl border border-amber-200 px-4 py-3">
                <button class="rounded-xl bg-amber-900 px-5 py-3 font-semibold text-white">Xác nhận đã gửi</button>
            </form>
        </section>
    @endif

    @if($returnRequest->order->payment_method === 'cod' && !$returnRequest->refund_bank_account && !in_array($returnRequest->status, ['completed', 'rejected']))
        <section class="rounded-[28px] border border-red-200 bg-red-50 p-6"><h2 class="font-bold text-red-900">Bổ sung tài khoản nhận tiền hoàn</h2><p class="mt-1 text-sm text-red-700">Phiếu cũ chưa có thông tin nhận tiền. Cửa hàng không thể hoàn tiền cho đến khi bạn cập nhật.</p><form method="POST" action="{{ route('returns.refund-account', $returnRequest) }}" class="mt-4 grid gap-3 sm:grid-cols-3">@csrf<select name="refund_bank_name" required class="rounded-xl border border-red-200 px-3 py-3"><option value="">Chọn ngân hàng</option>@foreach(['Vietcombank','VietinBank','BIDV','Agribank','Techcombank','MB Bank','ACB','VPBank','TPBank','Sacombank','HDBank','VIB','SHB','OCB','MSB'] as $bank)<option value="{{ $bank }}">{{ $bank }}</option>@endforeach</select><input name="refund_bank_account" required inputmode="numeric" pattern="[0-9]{6,20}" placeholder="Số tài khoản" class="rounded-xl border border-red-200 px-3 py-3"><input name="refund_account_name" required placeholder="Tên chủ tài khoản" class="rounded-xl border border-red-200 px-3 py-3 uppercase"><button class="rounded-xl bg-red-700 px-4 py-3 font-semibold text-white sm:col-span-3">Lưu tài khoản nhận tiền</button></form></section>
    @endif

    <div class="grid gap-5 lg:grid-cols-2">
        <section class="rounded-[28px] border border-[#ece8e2] bg-white p-6 shadow-sm"><h2 class="font-bold">Chi tiết tiền hoàn</h2><div class="mt-4 divide-y">@foreach ($returnRequest->items as $item)<div class="flex justify-between gap-4 py-3 text-sm"><span>{{ $item->orderItem->product_name }} × {{ $item->quantity }} <span class="block text-xs text-[#888]">Sau giảm giá + VAT tương ứng</span></span><strong>{{ number_format($item->refund_amount, 0, ',', '.') }}₫</strong></div>@endforeach</div><dl class="space-y-2 border-t pt-4 text-sm"><div class="flex justify-between"><dt>Phí giao hàng ban đầu được hoàn</dt><dd>{{ number_format($returnRequest->original_shipping_refund, 0, ',', '.') }}₫</dd></div><div class="flex justify-between"><dt>Phí gửi trả NovaPhone chịu</dt><dd>{{ number_format($returnRequest->return_shipping_fee, 0, ',', '.') }}₫</dd></div></dl>@if($returnRequest->refund_amount)<div class="mt-4 flex justify-between border-t pt-4"><strong>Tổng đã hoàn</strong><strong class="text-emerald-600">{{ number_format($returnRequest->refund_amount, 0, ',', '.') }}₫</strong></div>@else<div class="mt-4 flex justify-between border-t pt-4"><strong>Tạm tính</strong><strong>{{ number_format($returnRequest->items->sum('refund_amount') + $returnRequest->original_shipping_refund + $returnRequest->return_shipping_fee, 0, ',', '.') }}₫</strong></div>@endif</section>
        <section class="rounded-[28px] border border-[#ece8e2] bg-white p-6 shadow-sm"><h2 class="font-bold">Nội dung yêu cầu</h2><p class="mt-4 text-sm"><strong>Lý do:</strong> {{ $returnRequest->reason }}</p><p class="mt-2 whitespace-pre-line text-sm text-[#666]">{{ $returnRequest->note }}</p>@if($returnRequest->tracking_code)<p class="mt-4 text-sm"><strong>Vận đơn:</strong> {{ $returnRequest->shipping_carrier }} · {{ $returnRequest->tracking_code }}</p>@endif</section>
    </div>
    <section class="rounded-[28px] border border-[#ece8e2] bg-white p-6 shadow-sm"><h2 class="font-bold">Ảnh/video bằng chứng</h2><div class="mt-4 grid grid-cols-2 gap-3 sm:grid-cols-4">@foreach($returnRequest->media as $media)<a href="{{ asset('storage/' . $media->path) }}" target="_blank" class="overflow-hidden rounded-2xl border bg-black">@if($media->media_type === 'video')<video src="{{ asset('storage/' . $media->path) }}" controls class="aspect-square w-full object-cover"></video>@else<img src="{{ asset('storage/' . $media->path) }}" class="aspect-square w-full object-cover" alt="Bằng chứng hoàn hàng">@endif</a>@endforeach</div></section>
    @if($returnRequest->status === 'completed')<div class="flex justify-end"><a href="{{ route('returns.receipt', $returnRequest) }}" target="_blank" class="rounded-full bg-black px-5 py-3 text-sm font-semibold text-white">Xem / in hóa đơn hoàn tiền</a></div>@endif
</div>
@endsection
