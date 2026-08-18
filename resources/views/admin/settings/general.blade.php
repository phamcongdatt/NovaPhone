@extends('admin.layout')

@section('page-title', 'Cài đặt chung')
@section('page-subtitle', 'General Settings')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div>
        <h2 class="text-xl font-bold text-white">General Settings</h2>
        <p class="mt-1 text-sm text-slate-400">Thiết lập các cấu hình chung cho hệ thống.</p>
    </div>
</div>

<form action="{{ route('admin.settings.general') }}" method="POST">
    @csrf
    
    <div class="rounded-2xl border border-white/5 bg-[#0b1523] shadow-xl overflow-hidden">
        {{-- Section Header --}}
        <div class="border-b border-white/5 bg-white/[0.02] px-6 py-4 flex items-center justify-between">
            <h3 class="text-base font-semibold text-white">Cấu hình thanh toán</h3>
            <button type="submit" class="rounded-lg bg-blue-600 px-6 py-2 text-sm font-bold text-white shadow-lg shadow-blue-500/20 transition-all hover:bg-blue-500 hover:-translate-y-0.5">
                Cập nhật
            </button>
        </div>
        
        <div class="p-6 space-y-8 divide-y divide-white/5">
            {{-- Tax Rate Input --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 pt-0">
                <div class="md:col-span-1">
                    <label class="text-sm font-medium text-slate-300">Thuế VAT (%)</label>
                </div>
                <div class="md:col-span-2">
                    <input type="number" step="0.01" min="0" max="100" name="tax_rate" value="{{ old('tax_rate', $settings['tax_rate']) }}" class="w-full rounded-xl border border-white/[0.07] bg-black/10 px-4 py-2.5 text-sm text-white focus:border-blue-500/40 focus:ring-2 focus:ring-blue-500/10" required>
                    <p class="mt-2 text-xs text-slate-500">Mức thuế áp dụng chung cho tất cả các đơn hàng. Ví dụ: nhập 10 cho mức thuế 10%.</p>
                    @error('tax_rate')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>
    </div>
</form>
@endsection
