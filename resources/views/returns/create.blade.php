@extends('layouts.app')

@section('title', 'Yêu cầu hoàn hàng - NovaPhone')

@section('content')
<div class="mx-auto max-w-4xl space-y-5">
    <div>
        <a href="{{ route('orders.show', $order) }}" class="text-sm font-semibold text-[#1677d2]">← Đơn {{ $order->order_code }}</a>
        <h1 class="mt-3 text-2xl font-bold text-[#171717]">Yêu cầu hoàn hàng</h1>
        <p class="mt-1 text-sm text-[#777]">Chọn sản phẩm, mô tả rõ tình trạng và cung cấp ảnh hoặc video thực tế.</p>
    </div>

    <div class="rounded-2xl border border-blue-200 bg-blue-50 p-4 text-sm leading-6 text-blue-900">
        <p class="font-semibold">Cách tính tiền hoàn</p>
        <p>Hoàn tiền sản phẩm thực trả sau khi phân bổ mã giảm giá, cộng VAT tương ứng. Nếu hoàn toàn bộ đơn do lỗi cửa hàng, phí giao hàng ban đầu cũng được hoàn. Phí gửi hàng trở lại do NovaPhone chịu theo số tiền cửa hàng xác nhận.</p>
    </div>

    @if ($errors->any())
        <div class="rounded-2xl border border-red-200 bg-red-50 p-4 text-sm text-red-700"><ul class="list-disc space-y-1 pl-5">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
    @endif

    <form method="POST" action="{{ route('returns.store', $order) }}" enctype="multipart/form-data" class="space-y-5">
        @csrf
        <section class="rounded-[28px] border border-[#ece8e2] bg-white p-5 shadow-sm sm:p-6">
            <h2 class="font-bold text-[#171717]">1. Sản phẩm cần hoàn</h2>
            <div class="mt-4 divide-y divide-[#eeeae4]">
                @foreach ($order->items as $item)
                    <div class="flex items-center justify-between gap-4 py-4">
                        <div><p class="font-semibold text-[#222]">{{ $item->product_name }}</p><p class="text-sm text-[#777]">{{ $item->variant_name ?: 'Mặc định' }} · Đã mua {{ $item->quantity }}</p></div>
                        <label class="text-sm text-[#555]">Số lượng
                            <select name="items[{{ $item->id }}]" class="ml-2 rounded-xl border border-[#ddd8d1] px-3 py-2">
                                @for ($quantity = 0; $quantity <= $item->quantity; $quantity++)<option value="{{ $quantity }}" @selected((int) old('items.' . $item->id) === $quantity)>{{ $quantity }}</option>@endfor
                            </select>
                        </label>
                    </div>
                @endforeach
            </div>
        </section>

        <section class="rounded-[28px] border border-[#ece8e2] bg-white p-5 shadow-sm sm:p-6">
            <h2 class="font-bold text-[#171717]">2. Lý do và bằng chứng</h2>
            <div class="mt-4 grid gap-4">
                <label class="text-sm font-semibold text-[#444]">Lý do
                    <select name="reason" required class="mt-2 w-full rounded-xl border border-[#ddd8d1] px-4 py-3 font-normal">
                        @foreach (['Sản phẩm lỗi/không hoạt động', 'Sản phẩm bị hư hỏng khi giao', 'Sai sản phẩm hoặc sai phiên bản', 'Thiếu phụ kiện/sản phẩm', 'Sản phẩm không đúng mô tả'] as $reason)<option value="{{ $reason }}" @selected(old('reason') === $reason)>{{ $reason }}</option>@endforeach
                    </select>
                </label>
                <label class="text-sm font-semibold text-[#444]">Ghi chú chi tiết <span class="text-red-500">*</span>
                    <textarea name="note" rows="5" required minlength="10" maxlength="2000" class="mt-2 w-full rounded-xl border border-[#ddd8d1] px-4 py-3 font-normal" placeholder="Mô tả lỗi, ngoại hình, phụ kiện đi kèm...">{{ old('note') }}</textarea>
                </label>
                <label class="text-sm font-semibold text-[#444]">Ảnh hoặc video bằng chứng <span class="text-red-500">*</span>
                    <input type="file" name="evidence[]" required multiple accept="image/jpeg,image/png,image/webp,video/mp4,video/quicktime,video/webm" class="mt-2 block w-full rounded-xl border border-dashed border-[#cfc8be] p-4 font-normal">
                    <span class="mt-2 block text-xs font-normal text-[#888]">Tối đa 5 tệp, mỗi tệp 20 MB.</span>
                </label>
            </div>
        </section>

        <section class="rounded-[28px] border border-[#ece8e2] bg-white p-5 shadow-sm sm:p-6">
            <h2 class="font-bold text-[#171717]">3. Nơi nhận tiền hoàn</h2>
            @if ($order->payment_method === 'vnpay')
                <div class="mt-4 rounded-2xl border border-blue-200 bg-blue-50 p-4 text-sm leading-6 text-blue-900">
                    Tiền sẽ được hoàn qua API VNPay về đúng phương thức/tài khoản đã dùng thanh toán đơn hàng. Bạn không cần nhập tài khoản khác.
                </div>
            @else
                <p class="mt-2 text-sm text-[#777]">Đơn COD được hoàn bằng chuyển khoản ngân hàng. Vui lòng kiểm tra kỹ thông tin.</p>
                <div class="mt-4 grid gap-4 sm:grid-cols-2">
                    <label class="text-sm font-semibold text-[#444]">Ngân hàng <span class="text-red-500">*</span><select name="refund_bank_name" required class="mt-2 w-full rounded-xl border border-[#ddd8d1] px-4 py-3 font-normal"><option value="">Chọn ngân hàng</option>@foreach(['Vietcombank','VietinBank','BIDV','Agribank','Techcombank','MB Bank','ACB','VPBank','TPBank','Sacombank','HDBank','VIB','SHB','OCB','MSB'] as $bank)<option value="{{ $bank }}" @selected(old('refund_bank_name') === $bank)>{{ $bank }}</option>@endforeach</select></label>
                    <label class="text-sm font-semibold text-[#444]">Số tài khoản <span class="text-red-500">*</span><input name="refund_bank_account" value="{{ old('refund_bank_account') }}" required inputmode="numeric" pattern="[0-9]{6,20}" maxlength="20" class="mt-2 w-full rounded-xl border border-[#ddd8d1] px-4 py-3 font-normal" placeholder="Chỉ nhập chữ số"></label>
                    <label class="text-sm font-semibold text-[#444] sm:col-span-2">Tên chủ tài khoản <span class="text-red-500">*</span><input name="refund_account_name" value="{{ old('refund_account_name') }}" required maxlength="100" class="mt-2 w-full rounded-xl border border-[#ddd8d1] px-4 py-3 font-normal uppercase" placeholder="NGUYEN VAN A"></label>
                </div>
            @endif
        </section>
        <div class="flex justify-end"><button class="rounded-full bg-black px-6 py-3 text-sm font-semibold text-white transition-all duration-200 hover:bg-[#222]">Gửi yêu cầu hoàn hàng</button></div>
    </form>
</div>
@endsection
