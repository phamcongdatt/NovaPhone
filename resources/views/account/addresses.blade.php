@extends('layouts.app')

@section('title', 'Quản lý địa chỉ - NovaPhone')

@section('content')
<div class="space-y-4">
    <div class="flex items-center gap-2 text-sm text-[#8b8b8b]">
        <a href="{{ route('home') }}" class="hover:text-black">Trang chủ</a>
        <span>/</span>
        <a href="{{ route('account.show') }}" class="hover:text-black">Tài khoản</a>
        <span>/</span>
        <span class="text-black">Địa chỉ của tôi</span>
    </div>

    <div class="grid gap-4 lg:grid-cols-[280px_1fr]">
        <aside class="rounded-[28px] border border-[#ece8e2] bg-white p-5 shadow-[0_10px_35px_rgba(0,0,0,.04)]">
            <h3 class="text-sm font-bold text-[#171717]">Tài khoản của tôi</h3>
            <nav class="mt-4 space-y-1">
                @foreach ([
                    ['url' => 'account.show', 'label' => 'Thông tin tài khoản'],
                    ['url' => 'orders.index', 'label' => 'Đơn hàng của tôi'],
                    ['url' => 'wishlist.index', 'label' => 'Sản phẩm yêu thích'],
                    ['url' => 'account.addresses', 'label' => 'Địa chỉ của tôi', 'active' => true],
                ] as $item)
                    <a
                        href="{{ route($item['url']) }}"
                        class="block rounded-[14px] px-3 py-2.5 text-sm font-medium transition {{ ($item['active'] ?? false) ? 'bg-black text-white' : 'text-[#111] hover:bg-[#f7f5f2]' }}"
                    >
                        {{ $item['label'] }}
                    </a>
                @endforeach
                <a href="{{ route('account.show') }}#ma-giam-gia-cua-toi" class="block rounded-[14px] px-3 py-2.5 text-sm font-medium text-[#111] transition hover:bg-[#f7f5f2]">
                    Mã giảm giá của tôi
                </a>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full rounded-[14px] px-3 py-2.5 text-left text-sm font-medium text-red-600 transition hover:bg-red-50">
                        Đăng xuất
                    </button>
                </form>
            </nav>
        </aside>

        <section class="rounded-[28px] border border-[#ece8e2] bg-white p-5 sm:p-6 shadow-[0_10px_35px_rgba(0,0,0,.04)]">
            <div class="mb-6 flex items-center justify-between">
                <h1 class="text-2xl font-bold text-[#171717]">Địa chỉ của tôi</h1>
                <button type="button" class="rounded-full bg-black px-6 py-2.5 text-sm font-semibold text-white transition-all hover:bg-[#222]" id="add-address-btn">
                    + Thêm địa chỉ
                </button>
            </div>

            <div class="grid gap-4 md:grid-cols-2">
                @forelse ($addresses as $address)
                    <div class="relative rounded-[20px] border-2 {{ $address->is_default ? 'border-black bg-[#fbfaf8]' : 'border-[#ece8e2] hover:border-[#ddd]' }} p-5 transition hover:shadow-md">
                        <div class="absolute right-4 top-4 flex gap-2">
                            <button type="button" class="text-sm font-semibold text-[#8b8b8b] hover:text-black edit-address-btn" data-id="{{ $address->id }}" title="Sửa">Sửa</button>
                            <button type="button" class="text-sm font-semibold text-[#8b8b8b] hover:text-red-600 delete-address-btn" data-id="{{ $address->id }}" title="Xóa">Xóa</button>
                        </div>

                        @if ($address->is_default)
                            <span class="inline-block rounded-full bg-black text-white px-3 py-1 text-[10px] font-bold">MẶC ĐỊNH</span>
                        @endif

                        <div class="mt-4 space-y-2">
                            <div>
                                <p class="text-xs text-[#8b8b8b]">Tên người nhận</p>
                                <p class="font-semibold text-[#111]">{{ $address->name ?? Auth::user()->name }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-[#8b8b8b]">Số điện thoại</p>
                                <p class="font-semibold text-[#111]">{{ $address->phone }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-[#8b8b8b]">Địa chỉ</p>
                                <p class="font-semibold text-[#111]">{{ $address->full_address }}</p>
                                @if (! $address->province_code || ! $address->ward_code)
                                    <p class="mt-1 text-xs font-medium text-amber-700">Địa chỉ cũ, cần cập nhật đơn vị hành chính mới.</p>
                                @endif
                            </div>
                        </div>

                        @if (!$address->is_default)
                            <div class="mt-4 flex gap-2 border-t border-[#ece8e2] pt-4">
                                <button type="button" class="flex-1 rounded-[12px] border border-[#ece8e2] px-3 py-2 text-xs font-semibold text-[#111] transition hover:border-black hover:bg-[#fbfaf8] set-default-btn" data-id="{{ $address->id }}">
                                    Đặt làm mặc định
                                </button>
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="col-span-full rounded-[20px] border-2 border-dashed border-[#ece8e2] p-8 text-center">
                        <p class="text-sm text-[#8b8b8b]">Chưa có địa chỉ nào. Thêm địa chỉ mới để tiếp tục mua sắm!</p>
                        <button type="button" class="mt-4 rounded-lg bg-black px-6 py-2 text-sm font-semibold text-white transition hover:bg-[#222]" id="add-empty-btn">
                            Thêm địa chỉ ngay
                        </button>
                    </div>
                @endforelse

                @if (count($addresses) > 0)
                    <button type="button" class="rounded-[20px] border-2 border-dashed border-[#ece8e2] p-8 transition hover:border-black hover:bg-[#fbfaf8]" id="add-address-btn-2">
                        <div class="text-center">
                            <p class="mt-2 text-sm font-semibold text-[#111]">Thêm địa chỉ mới</p>
                        </div>
                    </button>
                @endif
            </div>
        </section>
    </div>

</div>

@push('modals')
    <div id="address-modal" class="fixed inset-0 z-[100] hidden overflow-y-auto bg-black/50 p-4 sm:p-6" aria-hidden="true">
        <div class="my-auto flex max-h-[90vh] w-full max-w-xl flex-col overflow-hidden rounded-[28px] border border-[#ece8e2] bg-white shadow-lg" role="dialog" aria-modal="true" aria-labelledby="modal-title">
            <div class="shrink-0 border-b border-[#ece8e2] bg-white p-5 sm:p-6">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-bold text-[#171717]" id="modal-title">Thêm địa chỉ mới</h2>
                    <button type="button" class="text-sm font-semibold text-[#8b8b8b] hover:text-black" id="close-modal">Đóng</button>
                </div>
            </div>

            <form id="address-form" class="min-h-0 flex-1 space-y-4 overflow-y-auto p-5 sm:p-6" method="POST" action="{{ route('address.store') }}">
                @csrf
                <input type="hidden" id="address-id" name="address_id">
                <input type="hidden" name="_method" id="form-method" value="POST">

                <div>
                    <label class="text-xs font-semibold text-[#8b8b8b]">Tên người nhận</label>
                    <input type="text" name="name" id="name" placeholder="Nhập tên người nhận" class="mt-2 w-full rounded-[12px] border border-[#ece8e2] bg-[#fbfaf8] px-4 py-2.5 text-sm outline-none transition focus:border-black" required>
                </div>

                <div>
                    <label class="text-xs font-semibold text-[#8b8b8b]">Số điện thoại</label>
                    <input type="tel" name="phone" id="phone" placeholder="Nhập số điện thoại" class="mt-2 w-full rounded-[12px] border border-[#ece8e2] bg-[#fbfaf8] px-4 py-2.5 text-sm outline-none transition focus:border-black" required>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="text-xs font-semibold text-[#8b8b8b]">Tỉnh/Thành phố</label>
                        <select name="province_code" id="province_code" class="mt-2 w-full rounded-[12px] border border-[#ece8e2] bg-[#fbfaf8] px-4 py-2.5 text-sm outline-none transition focus:border-black" required>
                            <option value="">Đang tải tỉnh/thành phố...</option>
                        </select>
                    </div>

                    <div>
                        <label class="text-xs font-semibold text-[#8b8b8b]">Phường/Xã</label>
                        <select name="ward_code" id="ward_code" class="mt-2 w-full rounded-[12px] border border-[#ece8e2] bg-[#fbfaf8] px-4 py-2.5 text-sm outline-none transition focus:border-black disabled:cursor-not-allowed disabled:opacity-60" required disabled>
                            <option value="">Chọn tỉnh/thành phố trước</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="text-xs font-semibold text-[#8b8b8b]">Địa chỉ chi tiết</label>
                    <input type="text" name="street" id="street" placeholder="Nhập địa chỉ chi tiết" class="mt-2 w-full rounded-[12px] border border-[#ece8e2] bg-[#fbfaf8] px-4 py-2.5 text-sm outline-none transition focus:border-black" required>
                </div>

                <label class="flex items-center gap-3">
                    <input type="checkbox" name="is_default" id="is_default" class="size-4 accent-black">
                    <span class="text-sm text-[#111]">Đặt làm địa chỉ mặc định</span>
                </label>

                <div class="flex gap-3 border-t border-[#ece8e2] pt-4">
                    <button type="button" class="flex-1 rounded-[12px] border border-[#ece8e2] px-4 py-3 text-sm font-semibold text-[#111] transition hover:border-black hover:bg-[#fbfaf8]" id="cancel-btn">
                        Hủy
                    </button>
                    <button type="submit" class="flex-1 rounded-[12px] bg-black px-4 py-3 text-sm font-semibold text-white transition-all hover:bg-[#222]">
                        Lưu địa chỉ
                    </button>
                </div>
            </form>
        </div>
    </div>
@endpush

@push('scripts')
<script>
    const modal = document.getElementById('address-modal');
    const form = document.getElementById('address-form');
    const modalTitle = document.getElementById('modal-title');
    const openBtns = document.querySelectorAll('#add-address-btn, #add-address-btn-2, #add-empty-btn');
    const closeBtn = document.getElementById('close-modal');
    const cancelBtn = document.getElementById('cancel-btn');
    const deleteButtons = document.querySelectorAll('.delete-address-btn');
    const editButtons = document.querySelectorAll('.edit-address-btn');
    const setDefaultButtons = document.querySelectorAll('.set-default-btn');
    const provinceSelect = document.getElementById('province_code');
    const wardSelect = document.getElementById('ward_code');
    const provincesUrl = @json(route('locations.provinces'));
    const wardsUrlTemplate = @json(route('locations.wards', ['provinceCode' => '__province_code__']));

    const replaceOptions = (select, placeholder, options = []) => {
        select.replaceChildren(new Option(placeholder, ''));
        options.forEach(option => select.add(new Option(option.name, option.code)));
    };

    async function loadWards(provinceCode, selectedWardCode = '') {
        wardSelect.disabled = true;
        replaceOptions(wardSelect, provinceCode ? 'Đang tải phường/xã...' : 'Chọn tỉnh/thành phố trước');

        if (!provinceCode) return;

        try {
            const response = await fetch(wardsUrlTemplate.replace('__province_code__', encodeURIComponent(provinceCode)));
            if (!response.ok) throw new Error('Không thể tải danh sách phường/xã.');

            const { data } = await response.json();
            replaceOptions(wardSelect, 'Chọn phường/xã', data);
            wardSelect.value = selectedWardCode;
            wardSelect.disabled = false;
        } catch (error) {
            replaceOptions(wardSelect, 'Không thể tải phường/xã');
        }
    }

    async function loadProvinces(selectedProvinceCode = '', selectedWardCode = '') {
        provinceSelect.disabled = true;
        replaceOptions(provinceSelect, 'Đang tải tỉnh/thành phố...');

        try {
            const response = await fetch(provincesUrl);
            if (!response.ok) throw new Error('Không thể tải danh sách tỉnh/thành phố.');

            const { data } = await response.json();
            replaceOptions(provinceSelect, 'Chọn tỉnh/thành phố', data);
            provinceSelect.value = selectedProvinceCode;
            provinceSelect.disabled = false;
            await loadWards(selectedProvinceCode, selectedWardCode);
        } catch (error) {
            replaceOptions(provinceSelect, 'Không thể tải tỉnh/thành phố');
        }
    }

    const openModal = () => {
        modal.classList.remove('hidden');
        modal.classList.add('flex', 'items-center', 'justify-center');
        modal.setAttribute('aria-hidden', 'false');
        document.body.classList.add('overflow-hidden');
        requestAnimationFrame(() => document.getElementById('name')?.focus());
    };

    const closeModal = () => {
        modal.classList.add('hidden');
        modal.classList.remove('flex', 'items-center', 'justify-center');
        modal.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('overflow-hidden');
    };

    openBtns.forEach(btn => {
        btn.addEventListener('click', async () => {
            modalTitle.textContent = 'Thêm địa chỉ mới';
            form.reset();
            form.action = '{{ route("address.store") }}';
            document.getElementById('form-method').value = 'POST';
            document.getElementById('address-id').value = '';
            await loadProvinces();
            openModal();
        });
    });

    closeBtn.addEventListener('click', closeModal);
    cancelBtn.addEventListener('click', closeModal);

    modal.addEventListener('click', (e) => {
        if (e.target === modal) closeModal();
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && !modal.classList.contains('hidden')) closeModal();
    });

    deleteButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            if (confirm('Bạn có chắc chắn muốn xóa địa chỉ này?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `{{ route('address.destroy', ':id') }}`.replace(':id', btn.dataset.id);
                form.innerHTML = `@csrf<input type="hidden" name="_method" value="DELETE">`;
                document.body.appendChild(form);
                form.submit();
            }
        });
    });

    editButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            const addressId = btn.dataset.id;
            fetch(`{{ route('address.show', ':id') }}`.replace(':id', addressId))
                .then(r => r.json())
                .then(async data => {
                    modalTitle.textContent = 'Sửa địa chỉ';
                    document.getElementById('address-id').value = addressId;
                    document.getElementById('name').value = data.name || data.full_name || '';
                    document.getElementById('phone').value = data.phone || '';
                    document.getElementById('street').value = data.street || data.address || '';
                    document.getElementById('is_default').checked = data.is_default || false;
                    form.action = `{{ route('address.update', ':id') }}`.replace(':id', addressId);
                    document.getElementById('form-method').value = 'PUT';
                    await loadProvinces(data.province_code || '', data.ward_code || '');
                    openModal();
                });
        });
    });

    provinceSelect.addEventListener('change', async function() {
        await loadWards(this.value);
    });

    setDefaultButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `{{ route('address.set-default', ':id') }}`.replace(':id', btn.dataset.id);
            form.innerHTML = `@csrf`;
            document.body.appendChild(form);
            form.submit();
        });
    });
</script>
@endpush
@endsection
