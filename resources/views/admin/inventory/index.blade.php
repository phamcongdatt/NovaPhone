@extends('admin.layout')

@section('title', 'Quản lý tồn kho')
@section('page-title', 'Quản lý tồn kho')
@section('page-subtitle', 'Theo dõi và quản lý hàng tồn kho, nhập xuất hàng hóa')

@section('content')
<div class="space-y-5">

    {{-- Dòng 1: Dashboard Thống kê kho --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        {{-- Tổng sản phẩm --}}
        <div class="rounded-2xl border border-blue-400/10 bg-[#081321] p-5 shadow-lg shadow-black/20">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold uppercase tracking-wider text-gray-400">TỔNG SẢN PHẨM</span>
                <span class="flex size-8 items-center justify-center rounded-lg bg-blue-500/10 text-blue-400">
                    <svg class="size-4.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m20.25 7.5-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z"/></svg>
                </span>
            </div>
            <p class="mt-2 text-2xl font-bold text-white">{{ number_format($stats['total_products'], 0, ',', '.') }}</p>
            <p class="mt-1 text-[11px] text-gray-500">Mã sản phẩm riêng biệt</p>
        </div>

        {{-- Tổng hàng còn --}}
        <div class="rounded-2xl border border-emerald-400/10 bg-[#081321] p-5 shadow-lg shadow-black/20">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold uppercase tracking-wider text-gray-400">TỔNG HÀNG CÒN</span>
                <span class="flex size-8 items-center justify-center rounded-lg bg-emerald-500/10 text-emerald-400">
                    <svg class="size-4.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5.586a1 1 0 0 1 .707.293l5.414 5.414a1 1 0 0 1 .293.707V19a2 2 0 0 1-2 2Z"/></svg>
                </span>
            </div>
            <p class="mt-2 text-2xl font-bold text-white">{{ number_format($stats['total_stock'], 0, ',', '.') }}</p>
            <p class="mt-1 text-[11px] text-gray-500">Tổng số thiết bị trong kho</p>
        </div>

        {{-- Sản phẩm sắp hết --}}
        <div class="rounded-2xl border border-amber-400/10 bg-[#081321] p-5 shadow-lg shadow-black/20">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold uppercase tracking-wider text-gray-400">SẢN PHẨM SẮP HẾT</span>
                <span class="flex size-8 items-center justify-center rounded-lg bg-amber-500/10 text-amber-400">
                    <svg class="size-4.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/></svg>
                </span>
            </div>
            <p class="mt-2 text-2xl font-bold text-amber-400">{{ number_format($stats['low_stock'], 0, ',', '.') }}</p>
            <p class="mt-1 text-[11px] text-gray-500">Số lượng từ 1 đến 5 chiếc</p>
        </div>

        {{-- Sản phẩm hết hàng --}}
        <div class="rounded-2xl border border-red-400/10 bg-[#081321] p-5 shadow-lg shadow-black/20">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold uppercase tracking-wider text-gray-400">SẢN PHẨM HẾT HÀNG</span>
                <span class="flex size-8 items-center justify-center rounded-lg bg-red-500/10 text-red-400">
                    <svg class="size-4.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m9.75 9.75 4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                </span>
            </div>
            <p class="mt-2 text-2xl font-bold text-red-400">{{ number_format($stats['out_of_stock'], 0, ',', '.') }}</p>
            <p class="mt-1 text-[11px] text-gray-500">Số lượng bằng 0 chiếc</p>
        </div>
    </div>

    {{-- Bộ lọc & Tìm kiếm --}}
    <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
        <form method="GET" action="{{ route('admin.inventory.index') }}" class="flex flex-1 flex-wrap items-center gap-2">
            <div class="relative min-w-[240px] flex-1">
                <input
                    type="text" name="search" value="{{ $filters['search'] ?? '' }}"
                    placeholder="Tìm sản phẩm theo tên hoặc SKU..."
                    class="w-full rounded-xl border border-white/10 bg-white/5 py-2.5 pl-10 pr-4 text-xs text-white outline-none transition-all duration-200 placeholder:text-gray-500 focus:border-brand-500 focus:ring-2 focus:ring-brand-500/25"
                >
                <svg class="pointer-events-none absolute left-3.5 top-1/2 size-4 -translate-y-1/2 text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35M17 10.5a6.5 6.5 0 1 1-13 0 6.5 6.5 0 0 1 13 0Z"/></svg>
            </div>

            <select name="category_id" class="rounded-xl border border-white/10 bg-[#081321] px-3 py-2.5 text-xs text-white outline-none focus:border-brand-500">
                <option value="">Tất cả danh mục</option>
                @foreach ($categories as $cat)
                    <option value="{{ $cat->id }}" @selected(($filters['category_id'] ?? null) == $cat->id)>{{ $cat->name }}</option>
                @endforeach
            </select>

            <select name="status" class="rounded-xl border border-white/10 bg-[#081321] px-3 py-2.5 text-xs text-white outline-none focus:border-brand-500">
                <option value="">Tất cả tồn kho</option>
                <option value="in_stock" @selected(($filters['status'] ?? null) === 'in_stock')>Còn hàng (> 5)</option>
                <option value="low_stock" @selected(($filters['status'] ?? null) === 'low_stock')>Sắp hết (1 - 5)</option>
                <option value="out_of_stock" @selected(($filters['status'] ?? null) === 'out_of_stock')>Hết hàng (0)</option>
            </select>

            <button type="submit" class="rounded-xl bg-blue-600 px-4 py-2.5 text-xs font-semibold text-white transition-all duration-200 hover:bg-blue-500">
                Lọc dữ liệu
            </button>

            @if (collect($filters)->filter()->isNotEmpty())
                <a href="{{ route('admin.inventory.index') }}" class="text-xs text-gray-500 transition-colors hover:text-gray-300">Xoá lọc</a>
            @endif
        </form>

        <a href="{{ route('admin.inventory.history') }}"
           class="inline-flex shrink-0 items-center justify-center gap-2 rounded-xl bg-white/5 border border-white/10 px-5 py-2.5 text-xs font-bold text-white shadow-lg transition-all duration-200 hover:bg-white/10">
            <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
            Xem lịch sử kho
        </a>
    </div>

    {{-- Bảng danh sách sản phẩm & tồn kho --}}
    <div class="overflow-x-auto rounded-2xl border border-white/5 bg-[#081321]">
        <table class="w-full text-left text-xs">
            <thead>
                <tr class="border-b border-white/5 text-[10px] uppercase tracking-wider text-gray-500">
                    <th class="px-4 py-3.5">Sản phẩm</th>
                    <th class="px-4 py-3.5">Danh mục</th>
                    <th class="px-4 py-3.5">Giá bán</th>
                    <th class="px-4 py-3.5 text-center">Tồn kho</th>
                    <th class="px-4 py-3.5 text-center">Trạng thái</th>
                    <th class="px-4 py-3.5 text-right">Thao tác</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
                @forelse ($inventories as $inv)
                    @php
                        $product = $inv->product;
                        $variant = $inv->variant;
                        
                        // Tính giá dựa trên có biến thể hay không
                        $price = $product->sale_price ?? $product->price;
                        if ($variant) {
                            $price += $variant->additional_price;
                        }
                    @endphp
                    <tr class="transition-colors duration-150 hover:bg-white/[0.02]">
                        {{-- Cột sản phẩm --}}
                        <td class="px-4 py-3.5">
                            <div class="flex items-center gap-3">
                                <img
                                    src="{{ $product->thumbnail ? asset('storage/' . $product->thumbnail) : 'https://placehold.co/64x64/12151d/93c5fd?text=No+Img' }}"
                                    alt="{{ $product->name }}"
                                    class="size-11 shrink-0 rounded-lg border border-white/10 object-cover bg-white/5"
                                >
                                <div class="min-w-0">
                                    <p class="truncate font-semibold text-white text-xs">{{ $product->name }}</p>
                                    @if ($variant)
                                        <p class="mt-0.5 text-[10px] text-brand-400 font-medium">Phiên bản: {{ $variant->name }} ({{ $variant->storage }} | {{ $variant->color }})</p>
                                    @endif
                                    <p class="mt-0.5 text-[10px] text-gray-500">SKU: {{ $variant->sku ?? $product->sku }}</p>
                                </div>
                            </div>
                        </td>

                        {{-- Cột danh mục --}}
                        <td class="px-4 py-3.5 text-gray-400 text-xs">{{ $product->category->name ?? '—' }}</td>

                        {{-- Cột giá --}}
                        <td class="px-4 py-3.5 text-white font-semibold text-xs">{{ number_format($price, 0, ',', '.') }}₫</td>

                        {{-- Cột số lượng tồn --}}
                        <td class="px-4 py-3.5 text-center">
                            @if ($inv->quantity == 0)
                                <span class="rounded-lg bg-red-500/10 border border-red-500/20 px-2.5 py-1 text-red-400 font-bold text-xs">
                                    0
                                </span>
                            @elseif ($inv->quantity <= 5)
                                <span class="rounded-lg bg-amber-500/10 border border-amber-500/20 px-2.5 py-1 text-amber-400 font-bold text-xs">
                                    {{ $inv->quantity }}
                                </span>
                            @else
                                <span class="rounded-lg bg-emerald-500/10 border border-emerald-500/20 px-2.5 py-1 text-emerald-400 font-bold text-xs">
                                    {{ $inv->quantity }}
                                </span>
                            @endif
                        </td>

                        {{-- Cột trạng thái --}}
                        <td class="px-4 py-3.5 text-center">
                            @if ($inv->quantity == 0)
                                <span class="inline-flex items-center gap-1 text-[10px] font-bold text-red-500 uppercase">
                                    <span class="size-1.5 rounded-full bg-red-500"></span> Hết hàng
                                </span>
                            @elseif ($inv->quantity <= 5)
                                <span class="inline-flex items-center gap-1 text-[10px] font-bold text-amber-500 uppercase">
                                    <span class="size-1.5 rounded-full bg-amber-500"></span> Sắp hết
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 text-[10px] font-bold text-emerald-500 uppercase">
                                    <span class="size-1.5 rounded-full bg-emerald-500"></span> Còn hàng
                                </span>
                            @endif
                        </td>

                        {{-- Cột hành động --}}
                        <td class="px-4 py-3.5 text-right">
                            <div class="inline-flex items-center gap-1.5">
                                {{-- Nhập kho --}}
                                <button type="button"
                                    onclick="openModal('import', '{{ route('admin.inventory.import', $inv) }}', '{{ $product->name }}{{ $variant ? ' ('.$variant->name.')' : '' }}', {{ $inv->quantity }})"
                                    class="rounded-lg bg-emerald-500/10 border border-emerald-500/20 px-2.5 py-1.5 text-[10px] font-bold text-emerald-400 hover:bg-emerald-500/20 transition-all cursor-pointer">
                                    <i class="bi bi-plus-circle me-1"></i> Nhập kho
                                </button>

                                {{-- Xuất kho --}}
                                <button type="button"
                                    @if($inv->quantity == 0) disabled class="rounded-lg bg-gray-500/5 border border-gray-500/10 px-2.5 py-1.5 text-[10px] font-bold text-gray-500 opacity-50 cursor-not-allowed"
                                    @else onclick="openModal('export', '{{ route('admin.inventory.export', $inv) }}', '{{ $product->name }}{{ $variant ? ' ('.$variant->name.')' : '' }}', {{ $inv->quantity }})"
                                          class="rounded-lg bg-red-500/10 border border-red-500/20 px-2.5 py-1.5 text-[10px] font-bold text-red-400 hover:bg-red-500/20 transition-all cursor-pointer"
                                    @endif>
                                    <i class="bi bi-dash-circle me-1"></i> Xuất kho
                                </button>

                                {{-- Điều chỉnh --}}
                                <button type="button"
                                    onclick="openModal('adjust', '{{ route('admin.inventory.adjust', $inv) }}', '{{ $product->name }}{{ $variant ? ' ('.$variant->name.')' : '' }}', {{ $inv->quantity }})"
                                    class="rounded-lg bg-blue-500/10 border border-blue-500/20 px-2.5 py-1.5 text-[10px] font-bold text-blue-400 hover:bg-blue-500/20 transition-all cursor-pointer">
                                    <i class="bi bi-sliders me-1"></i> Điều chỉnh
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-12 text-center text-gray-500 text-xs">
                            Không tìm thấy sản phẩm hoặc thông tin tồn kho tương ứng.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Phân trang --}}
    <div class="mt-4">
        {{ $inventories->links() }}
    </div>

</div>

{{-- MODALS QUẢN LÝ KHO (Sử dụng CSS Overlay + JS để kiểm soát đóng mở) --}}
@foreach(['import', 'export', 'adjust'] as $mType)
    @php
        $modalTitle = $mType === 'import' ? 'Nhập hàng vào kho' : ($mType === 'export' ? 'Xuất hàng khỏi kho' : 'Điều chỉnh số lượng tồn kho');
        $submitBtn = $mType === 'import' ? 'Xác nhận nhập kho' : ($mType === 'export' ? 'Xác nhận xuất kho' : 'Xác nhận điều chỉnh');
        $btnColor = $mType === 'import' ? 'from-emerald-600 to-emerald-500 shadow-emerald-950/20' : ($mType === 'export' ? 'from-red-600 to-red-500 shadow-red-950/20' : 'from-blue-600 to-blue-500 shadow-blue-950/20');
        $inputLabel = $mType === 'import' ? 'Số lượng nhập thêm' : ($mType === 'export' ? 'Số lượng xuất kho' : 'Số lượng tồn thực tế mới');
        $placeholder = $mType === 'import' ? 'Nhập số lượng thiết bị muốn nhập thêm (ví dụ: 10)' : ($mType === 'export' ? 'Nhập số lượng thiết bị muốn xuất đi (ví dụ: 5)' : 'Nhập tổng số lượng tồn kho mới sau điều chỉnh');
    @endphp
    <div id="modal-{{ $mType }}" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm hidden transition-all duration-300">
        <div class="relative w-full max-w-md overflow-hidden rounded-2xl border border-white/10 bg-[#07111e] p-6 shadow-2xl transition-all duration-300">
            {{-- Header --}}
            <div class="mb-4 flex items-center justify-between">
                <h3 class="text-sm font-bold text-white uppercase tracking-wider flex items-center gap-2">
                    @if($mType === 'import')
                        <i class="bi bi-plus-circle-fill text-emerald-400"></i>
                    @elseif($mType === 'export')
                        <i class="bi bi-dash-circle-fill text-red-400"></i>
                    @else
                        <i class="bi bi-sliders text-blue-400"></i>
                    @endif
                    {{ $modalTitle }}
                </h3>
                <button type="button" onclick="closeModal('{{ $mType }}')" class="rounded-lg p-1.5 text-slate-400 hover:bg-white/5 hover:text-white cursor-pointer">
                    <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" d="M6 6l12 12M18 6 6 18"/></svg>
                </button>
            </div>

            {{-- Form --}}
            <form id="form-{{ $mType }}" method="POST" onsubmit="return confirmAction('{{ $mType }}')" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-[11px] font-bold uppercase text-slate-400 mb-1.5">Sản phẩm chọn</label>
                    <p id="product-display-{{ $mType }}" class="rounded-xl border border-white/5 bg-white/[0.02] px-3.5 py-2.5 text-xs text-white font-semibold truncate"></p>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[11px] font-bold uppercase text-slate-400 mb-1.5">Số lượng hiện tại</label>
                        <p id="qty-display-{{ $mType }}" class="rounded-xl border border-white/5 bg-white/[0.02] px-3.5 py-2.5 text-xs text-slate-300 font-bold"></p>
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold uppercase text-slate-400 mb-1.5">{{ $inputLabel }}</label>
                        <input
                            type="number"
                            id="input-qty-{{ $mType }}"
                            name="quantity"
                            required
                            min="{{ $mType === 'adjust' ? '0' : '1' }}"
                            placeholder="Số lượng"
                            class="w-full rounded-xl border border-white/10 bg-black/20 px-3.5 py-2 text-xs text-white font-semibold outline-none focus:border-brand-500"
                        >
                    </div>
                </div>

                <div>
                    <label class="block text-[11px] font-bold uppercase text-slate-400 mb-1.5">Ghi chú / Lý do thực hiện</label>
                    <textarea
                        name="note"
                        rows="2"
                        placeholder="Ví dụ: Nhập hàng đợt 2 tháng 7, Điều chỉnh kiểm kê thực tế..."
                        class="w-full rounded-xl border border-white/10 bg-black/20 px-3.5 py-2 text-xs text-white outline-none focus:border-brand-500 placeholder:text-slate-600"
                    ></textarea>
                </div>

                {{-- Action --}}
                <div class="flex gap-2.5 pt-2">
                    <button type="button" onclick="closeModal('{{ $mType }}')" class="flex-1 rounded-xl bg-white/5 py-2.5 text-xs font-semibold text-slate-300 hover:bg-white/10 hover:text-white transition-colors cursor-pointer">
                        Huỷ bỏ
                    </button>
                    <button type="submit" class="flex-1 rounded-xl bg-gradient-to-r {{ $btnColor }} py-2.5 text-xs font-bold text-white shadow-lg transition-all hover:brightness-110 cursor-pointer">
                        {{ $submitBtn }}
                    </button>
                </div>
            </form>
        </div>
    </div>
@endforeach

{{-- BOOTSTRAP ICONS LINK (Cho các biểu tượng đẹp mắt) --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

@push('scripts')
<script>
    // Hàm mở Modal
    function openModal(type, actionUrl, productName, currentQty) {
        const modal = document.getElementById('modal-' + type);
        const form = document.getElementById('form-' + type);
        const pDisplay = document.getElementById('product-display-' + type);
        const qDisplay = document.getElementById('qty-display-' + type);
        const inputQty = document.getElementById('input-qty-' + type);

        if (modal && form) {
            form.action = actionUrl;
            if (pDisplay) pDisplay.textContent = productName;
            if (qDisplay) qDisplay.textContent = currentQty + ' chiếc';
            if (inputQty) {
                inputQty.value = '';
                if (type === 'export') {
                    inputQty.max = currentQty;
                }
            }

            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden'; // Ngăn cuộn trang phía sau
        }
    }

    // Hàm đóng Modal
    function closeModal(type) {
        const modal = document.getElementById('modal-' + type);
        if (modal) {
            modal.classList.add('hidden');
            document.body.style.overflow = ''; // Cho phép cuộn lại
        }
    }

    // Xác nhận trước khi gửi form (Yêu cầu 9)
    function confirmAction(type) {
        const actionLabel = type === 'import' ? 'NHẬP KHO' : (type === 'export' ? 'XUẤT KHO' : 'ĐIỀU CHỈNH KHO');
        const qtyVal = document.getElementById('input-qty-' + type).value;

        // Validation phía Client: Kiểm tra số âm / ký tự
        if (!qtyVal || qtyVal === '') {
            alert('Vui lòng nhập số lượng hợp lệ!');
            return false;
        }

        const qtyNum = parseInt(qtyVal, 10);
        if (isNaN(qtyNum)) {
            alert('Số lượng nhập vào không hợp lệ!');
            return false;
        }

        if (type === 'adjust' && qtyNum < 0) {
            alert('Số lượng tồn điều chỉnh không thể nhỏ hơn 0!');
            return false;
        } else if (type !== 'adjust' && qtyNum <= 0) {
            alert('Số lượng thực hiện phải lớn hơn 0!');
            return false;
        }

        if (type === 'export') {
            const currentQtyText = document.getElementById('qty-display-export').textContent;
            const currentQty = parseInt(currentQtyText, 10);
            if (qtyNum > currentQty) {
                alert('Không thể xuất quá số lượng tồn kho hiện tại (' + currentQty + ' chiếc)!');
                return false;
            }
        }

        return confirm('Hành động: ' + actionLabel + '\nSố lượng thay đổi: ' + qtyNum + '\nBạn có chắc chắn muốn thực hiện giao dịch kho này không?');
    }

    // Nhấp ra ngoài modal để đóng
    window.onclick = function(event) {
        ['import', 'export', 'adjust'].forEach(type => {
            const modal = document.getElementById('modal-' + type);
            if (event.target === modal) {
                closeModal(type);
            }
        });
    }
</script>
@endpush

@endsection
