@extends('admin.layout')

@php
    $isEdit = $flashSale->exists;
    $defaultItems = [];
    if ($isEdit) {
        $defaultItems = $flashSale->items->map(function($i) {
            return [
                'product_id' => $i->product_id,
                'name' => $i->product->name ?? 'N/A',
                'discount_percent' => $i->discount_percent,
                'quantity' => $i->quantity,
                'max_per_user' => $i->max_per_user,
                'sold' => $i->sold
            ];
        })->toArray();
    }
@endphp

@section('page-title', $isEdit ? 'Chỉnh sửa Flash Sale' : 'Tạo mới Flash Sale')
@section('page-subtitle', 'Thiết lập chiến dịch khuyến mãi chớp nhoáng')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div>
        <h2 class="text-xl font-bold text-white">{{ $isEdit ? 'Chỉnh sửa' : 'Thêm mới' }} chiến dịch</h2>
    </div>
    <a href="{{ route('admin.flash-sales.index') }}" class="text-sm text-slate-400 hover:text-white">← Quay lại</a>
</div>

<form action="{{ $isEdit ? route('admin.flash-sales.update', $flashSale) : route('admin.flash-sales.store') }}" method="POST" id="flash-sale-form">
    @csrf
    @if($isEdit) @method('PUT') @endif
    
    <div class="grid gap-6 lg:grid-cols-3">
        {{-- Thông tin chiến dịch --}}
        <div class="lg:col-span-1 space-y-6">
            <div class="rounded-2xl border border-white/5 bg-[#0b1523] p-5 shadow-xl">
                <h3 class="mb-4 text-sm font-semibold text-white">Thông tin cơ bản</h3>
                
                <div class="space-y-4">
                    <div>
                        <label class="mb-1.5 block text-xs font-medium text-slate-400">Tên chiến dịch <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $flashSale->name) }}" required class="w-full rounded-xl border border-white/[0.07] bg-black/10 px-3 py-2 text-sm text-white placeholder:text-slate-600 focus:border-blue-500/40 focus:ring-2 focus:ring-blue-500/10">
                    </div>
                    
                    <div>
                        <label class="mb-1.5 block text-xs font-medium text-slate-400">Thời gian bắt đầu <span class="text-red-500">*</span></label>
                        <input type="datetime-local" name="start_time" value="{{ old('start_time', $isEdit ? $flashSale->start_time->format('Y-m-d\TH:i') : '') }}" required class="w-full rounded-xl border border-white/[0.07] bg-black/10 px-3 py-2 text-sm text-white focus:border-blue-500/40 focus:ring-2 focus:ring-blue-500/10">
                    </div>
                    
                    <div>
                        <label class="mb-1.5 block text-xs font-medium text-slate-400">Thời gian kết thúc <span class="text-red-500">*</span></label>
                        <input type="datetime-local" name="end_time" value="{{ old('end_time', $isEdit ? $flashSale->end_time->format('Y-m-d\TH:i') : '') }}" required class="w-full rounded-xl border border-white/[0.07] bg-black/10 px-3 py-2 text-sm text-white focus:border-blue-500/40 focus:ring-2 focus:ring-blue-500/10">
                    </div>
                    
                    <div class="flex items-center gap-2 pt-2">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $flashSale->is_active ?? true) ? 'checked' : '' }} class="size-4 rounded border-white/10 bg-black/20 text-blue-500 focus:ring-blue-500/20 focus:ring-offset-0">
                        <label for="is_active" class="text-sm font-medium text-slate-300">Kích hoạt chiến dịch</label>
                    </div>
                </div>
            </div>
            
            <button type="submit" class="w-full rounded-xl bg-blue-600 py-3 text-sm font-bold text-white shadow-lg shadow-blue-500/20 transition-all hover:bg-blue-500 hover:-translate-y-0.5">
                {{ $isEdit ? 'Cập nhật chiến dịch' : 'Thêm chiến dịch' }}
            </button>
        </div>

        {{-- Cấu hình sản phẩm --}}
        <div class="lg:col-span-2 space-y-6">
            <div class="rounded-2xl border border-white/5 bg-[#0b1523] p-5 shadow-xl">
                <h3 class="mb-4 text-sm font-semibold text-white">Thêm nhanh hàng loạt</h3>
                <p class="mb-4 text-xs italic text-red-400">
                    * Lưu ý: Chọn xong sản phẩm và thông số ở đây, hãy nhấn nút <b>Thêm sản phẩm</b> để hệ thống tự động đưa xuống danh sách bên dưới.
                </p>
                
                <div class="rounded-xl border border-white/5 bg-white/[0.02] p-4">
                    <label class="mb-2 block text-xs font-medium text-slate-400">Chọn các sản phẩm muốn thêm</label>
                    
                    <div class="mb-4">
                        <input type="text" id="product-search" placeholder="Search sản phẩm..." class="w-full rounded-lg border border-white/[0.07] bg-black/20 px-3 py-2 text-sm text-white placeholder:text-slate-600 focus:border-blue-500/40 focus:ring-2 focus:ring-blue-500/10">
                    </div>
                    
                    <div class="mb-4 max-h-48 overflow-y-auto rounded-lg border border-white/5 bg-black/10 p-2" id="product-list-container">
                        @foreach($products as $product)
                            <label class="flex cursor-pointer items-center gap-3 rounded-md px-2 py-1.5 transition-colors hover:bg-white/5 product-option" data-name="{{ strtolower($product->name) }}">
                                <input type="checkbox" class="bulk-product-checkbox size-4 rounded border-white/10 bg-black/20 text-blue-500 focus:ring-offset-0" value="{{ $product->id }}" data-name="{{ $product->name }}" data-price="{{ $product->price }}">
                                <span class="text-sm text-slate-300">{{ $product->name }} <span class="text-xs text-slate-500">({{ number_format($product->price) }}đ)</span></span>
                            </label>
                        @endforeach
                    </div>
                    
                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label class="mb-1.5 block text-xs font-medium text-slate-400">% giảm giá (1-99%)</label>
                            <input type="number" id="bulk-discount" min="1" max="99" class="w-full rounded-lg border border-white/[0.07] bg-black/20 px-3 py-2 text-sm text-white">
                        </div>
                        <div>
                            <label class="mb-1.5 block text-xs font-medium text-slate-400">Số lượng mở bán</label>
                            <input type="number" id="bulk-quantity" min="1" class="w-full rounded-lg border border-white/[0.07] bg-black/20 px-3 py-2 text-sm text-white">
                        </div>
                        <div>
                            <label class="mb-1.5 block text-xs font-medium text-slate-400">Tối đa 1 lần mua</label>
                            <input type="number" id="bulk-max" min="1" value="1" class="w-full rounded-lg border border-white/[0.07] bg-black/20 px-3 py-2 text-sm text-white">
                        </div>
                    </div>
                    
                    <div class="mt-4 flex justify-end">
                        <button type="button" id="btn-bulk-add" class="rounded-lg bg-blue-600/20 px-4 py-2 text-sm font-semibold text-blue-400 transition-colors hover:bg-blue-600/30">
                            Thêm sản phẩm
                        </button>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-white/5 bg-[#0b1523] p-5 shadow-xl">
                <h3 class="mb-4 text-sm font-semibold text-white">Danh sách sản phẩm tham gia</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-300">
                        <thead class="border-b border-white/5 bg-white/5 text-xs text-slate-400">
                            <tr>
                                <th class="px-4 py-3 font-medium">Sản phẩm (Bắt buộc) <span class="text-red-500">*</span></th>
                                <th class="px-4 py-3 font-medium w-24">% giảm <span class="text-red-500">*</span></th>
                                <th class="px-4 py-3 font-medium w-28">SL giảm <span class="text-red-500">*</span></th>
                                <th class="px-4 py-3 font-medium w-28">Tối đa <span class="text-red-500">*</span></th>
                                <th class="px-4 py-3 font-medium w-16"></th>
                            </tr>
                        </thead>
                        <tbody id="selected-items-body" class="divide-y divide-white/5">
                            {{-- Chứa các sản phẩm đã chọn --}}
                        </tbody>
                    </table>
                    <div id="empty-items-msg" class="py-6 text-center text-xs text-slate-500">Chưa có sản phẩm nào tham gia.</div>
                </div>
            </div>
        </div>
    </div>
</form>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('product-search');
        const productOptions = document.querySelectorAll('.product-option');
        
        // Tìm kiếm sản phẩm trong danh sách bulk
        searchInput.addEventListener('input', function(e) {
            const val = e.target.value.toLowerCase();
            productOptions.forEach(opt => {
                if (opt.getAttribute('data-name').includes(val)) {
                    opt.style.display = 'flex';
                } else {
                    opt.style.display = 'none';
                }
            });
        });

        // Xử lý nút Bulk Add
        const btnBulkAdd = document.getElementById('btn-bulk-add');
        const tbody = document.getElementById('selected-items-body');
        const emptyMsg = document.getElementById('empty-items-msg');
        
        let itemIndex = 0; // Dùng để tạo tên array name cho form (vd: items[0][product_id])

        // Chứa danh sách dữ liệu cũ nếu đang là Edit hoặc có form error (old('items'))
        const oldItems = @json(old('items', $defaultItems));

        function addRowToTable(product_id, name, discount, qty, max, sold = 0) {
            // Kiểm tra xem đã có product_id này chưa
            if (document.querySelector(`input[name="items[${product_id}][product_id]"]`)) return;

            emptyMsg.style.display = 'none';

            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td class="px-4 py-2">
                    <input type="hidden" name="items[${product_id}][product_id]" value="${product_id}">
                    <input type="hidden" name="items[${product_id}][sold]" value="${sold}">
                    <span class="text-xs font-medium">${name}</span>
                    ${sold > 0 ? `<div class="text-[10px] text-emerald-400 mt-1">Đã bán: ${sold}</div>` : ''}
                </td>
                <td class="px-4 py-2">
                    <input type="number" name="items[${product_id}][discount_percent]" value="${discount}" min="1" max="99" required class="w-full rounded-md border border-white/10 bg-black/20 px-2 py-1 text-xs text-white">
                </td>
                <td class="px-4 py-2">
                    <input type="number" name="items[${product_id}][quantity]" value="${qty}" min="1" required class="w-full rounded-md border border-white/10 bg-black/20 px-2 py-1 text-xs text-white">
                </td>
                <td class="px-4 py-2">
                    <input type="number" name="items[${product_id}][max_per_user]" value="${max}" min="1" required class="w-full rounded-md border border-white/10 bg-black/20 px-2 py-1 text-xs text-white">
                </td>
                <td class="px-4 py-2 text-right">
                    <button type="button" class="btn-remove-row text-slate-500 hover:text-red-400">
                        <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </td>
            `;
            tbody.appendChild(tr);

            // Bỏ check ở trên (nếu có)
            const cb = document.querySelector(`.bulk-product-checkbox[value="${product_id}"]`);
            if(cb) cb.checked = false;
        }

        // Init old data
        if (oldItems && Array.isArray(oldItems) || typeof oldItems === 'object') {
            Object.values(oldItems).forEach(item => {
                // Nếu error từ validation, product không có name. Phải tìm name.
                let name = item.name;
                if (!name) {
                    const cb = document.querySelector(`.bulk-product-checkbox[value="${item.product_id}"]`);
                    name = cb ? cb.getAttribute('data-name') : 'N/A';
                }
                addRowToTable(item.product_id, name, item.discount_percent, item.quantity, item.max_per_user, item.sold || 0);
            });
        }

        btnBulkAdd.addEventListener('click', function() {
            const discount = document.getElementById('bulk-discount').value;
            const qty = document.getElementById('bulk-quantity').value;
            const max = document.getElementById('bulk-max').value;

            if (!discount || !qty || !max) {
                alert('Vui lòng nhập đủ % giảm, Số lượng và Tối đa 1 lần mua.');
                return;
            }

            const checkedBoxes = document.querySelectorAll('.bulk-product-checkbox:checked');
            if (checkedBoxes.length === 0) {
                alert('Vui lòng chọn ít nhất 1 sản phẩm.');
                return;
            }

            checkedBoxes.forEach(cb => {
                addRowToTable(cb.value, cb.getAttribute('data-name'), discount, qty, max);
            });
            
            // Clear inputs bulk sau khi add (nếu muốn)
            // document.getElementById('bulk-discount').value = '';
            // document.getElementById('bulk-quantity').value = '';
            // document.getElementById('bulk-max').value = '1';
        });

        tbody.addEventListener('click', function(e) {
            const btn = e.target.closest('.btn-remove-row');
            if (btn) {
                btn.closest('tr').remove();
                if (tbody.children.length === 0) {
                    emptyMsg.style.display = 'block';
                }
            }
        });
    });
</script>
@endpush
