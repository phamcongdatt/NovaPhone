@extends('admin.layout')

@section('content')
@php
    $hasVariants = $product->variants->isNotEmpty();
    $baseInventory = $product->inventory;
@endphp

<div class="mx-auto max-w-6xl space-y-5">

    {{-- ═══════════ Header ═══════════ --}}
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <div class="flex items-center gap-2 text-xs text-gray-500">
                <a href="{{ route('admin.products.index') }}" class="hover:text-gray-300">Sản phẩm</a>
                <span>/</span>
                <span class="text-gray-400">Chi tiết</span>
            </div>
            <h1 class="mt-1 text-xl font-bold text-white">{{ $product->name }}</h1>
        </div>

        <div class="flex gap-2">
            <a href="{{ route('admin.products.edit', $product) }}"
               class="inline-flex items-center gap-1.5 rounded-xl bg-brand-600 px-4 py-2.5 text-sm font-bold text-white shadow-lg shadow-brand-600/25 transition-all duration-200 hover:-translate-y-0.5 hover:bg-brand-500">
                <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Z"/></svg>
                Chỉnh sửa
            </a>
            <a href="{{ route('admin.products.index') }}"
               class="rounded-xl border border-white/10 px-4 py-2.5 text-sm font-bold text-gray-300 transition-all duration-200 hover:bg-white/5">
                Quay lại
            </a>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">

        {{-- ═══════════ Cột trái ═══════════ --}}
        <div class="space-y-5 lg:col-span-2">

            {{-- Thông tin cơ bản --}}
            <div class="rounded-2xl border border-white/5 bg-night-soft p-5">
                <h3 class="mb-4 text-sm font-bold uppercase tracking-wider text-gray-400">Thông tin cơ bản</h3>

                <dl class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <dt class="text-xs text-gray-500">Danh mục</dt>
                        <dd class="mt-0.5 text-sm font-medium text-gray-200">{{ $product->category->name ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-500">Thương hiệu</dt>
                        <dd class="mt-0.5 text-sm font-medium text-gray-200">{{ $product->brand->name ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-500">Slug</dt>
                        <dd class="mt-0.5 text-sm font-medium text-gray-200">{{ $product->slug }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-500">SKU</dt>
                        <dd class="mt-0.5 text-sm font-medium text-gray-200">{{ $product->sku }}</dd>
                    </div>
                </dl>

                @if ($product->description)
                    <div class="mt-4 border-t border-white/10 pt-4">
                        <dt class="text-xs text-gray-500">Mô tả ngắn</dt>
                        <dd class="mt-1 text-sm leading-relaxed text-gray-300">{{ $product->description }}</dd>
                    </div>
                @endif

                @if ($product->content)
                    <div class="mt-4 border-t border-white/10 pt-4">
                        <dt class="text-xs text-gray-500">Nội dung chi tiết</dt>
                        <dd class="prose prose-invert prose-sm mt-1 max-w-none text-gray-300">{!! nl2br(e($product->content)) !!}</dd>
                    </div>
                @endif
            </div>

            {{-- ═══════════ Biến thể ═══════════ --}}
            <div class="rounded-2xl border border-white/5 bg-night-soft p-5">
                <div class="mb-4 flex items-center justify-between">
                    <h3 class="text-sm font-bold uppercase tracking-wider text-gray-400">
                        Biến thể (Màu sắc / Dung lượng)
                    </h3>
                    <span class="rounded-full bg-white/5 px-2.5 py-1 text-xs font-semibold text-gray-400">
                        {{ $product->variants->count() }} biến thể
                    </span>
                </div>

                @if (! $hasVariants)
                    <div class="rounded-xl border border-dashed border-white/10 py-6 text-center text-xs text-gray-500">
                        Sản phẩm này không có biến thể — dùng giá &amp; tồn kho gốc bên dưới.
                    </div>

                    <div class="mt-4 grid gap-3 sm:grid-cols-2">
                        <div class="rounded-xl bg-white/[0.02] p-3.5">
                            <dt class="text-xs text-gray-500">Tồn kho (gốc)</dt>
                            <dd class="mt-1 text-lg font-bold text-gray-100">{{ $baseInventory->quantity ?? 0 }}</dd>
                        </div>
                        <div class="rounded-xl bg-white/[0.02] p-3.5">
                            <dt class="text-xs text-gray-500">Ngưỡng cảnh báo hết hàng</dt>
                            <dd class="mt-1 text-lg font-bold text-gray-100">{{ $baseInventory->low_stock_threshold ?? 5 }}</dd>
                        </div>
                    </div>
                @else
                    <div class="overflow-x-auto rounded-xl border border-white/5">
                        <table class="w-full text-left text-sm">
                            <thead class="bg-white/[0.03] text-xs uppercase tracking-wide text-gray-500">
                                <tr>
                                    <th class="px-3.5 py-2.5">Ảnh</th>
                                    <th class="px-3.5 py-2.5">Tên biến thể</th>
                                    <th class="px-3.5 py-2.5">Dung lượng</th>
                                    <th class="px-3.5 py-2.5">Màu</th>
                                    <th class="px-3.5 py-2.5">Giá cộng thêm</th>
                                    <th class="px-3.5 py-2.5">SKU</th>
                                    <th class="px-3.5 py-2.5">Tồn kho</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-white/5">
                                @foreach ($product->variants as $variant)
                                    @php
                                        $qty = $variant->inventory->quantity ?? 0;
                                        $lowStock = $qty <= ($variant->inventory->low_stock_threshold ?? 5);
                                    @endphp
                                    <tr class="hover:bg-white/[0.02]">
                                        <td class="px-3.5 py-2.5">
                                            @if ($variant->image)
                                                <img src="{{ asset($variant->image) }}"
                                                     class="size-10 rounded-lg border border-white/10 object-cover">
                                            @else
                                                <div class="flex size-10 items-center justify-center rounded-lg border border-dashed border-white/10 text-[10px] text-gray-600">
                                                    N/A
                                                </div>
                                            @endif
                                        </td>
                                        <td class="px-3.5 py-2.5 font-medium text-gray-200">{{ $variant->name }}</td>
                                        <td class="px-3.5 py-2.5 text-gray-400">{{ $variant->storage ?: '—' }}</td>
                                        <td class="px-3.5 py-2.5">
                                            <span class="inline-flex items-center gap-1.5 text-gray-400">
                                                @if ($variant->color_code)
                                                    <span class="size-3 rounded-full border border-white/20"
                                                          style="background-color: {{ $variant->color_code }}"></span>
                                                @endif
                                                {{ $variant->color ?: '—' }}
                                            </span>
                                        </td>
                                        <td class="px-3.5 py-2.5 text-gray-300">
                                            {{ $variant->additional_price > 0 ? '+' . number_format($variant->additional_price) . '₫' : '—' }}
                                        </td>
                                        <td class="px-3.5 py-2.5 text-gray-400">{{ $variant->sku ?: '—' }}</td>
                                        <td class="px-3.5 py-2.5">
                                            <span class="font-bold {{ $lowStock ? 'text-red-400' : 'text-gray-200' }}">
                                                {{ $qty }}
                                            </span>
                                            @if ($lowStock)
                                                <span class="ml-1 text-[10px] font-semibold text-red-400">(sắp hết)</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            {{-- ═══════════ Thư viện ảnh ═══════════ --}}
            @if ($product->images->isNotEmpty())
                <div class="rounded-2xl border border-white/5 bg-night-soft p-5">
                    <h3 class="mb-4 text-sm font-bold uppercase tracking-wider text-gray-400">Thư viện ảnh</h3>
                    <div class="grid grid-cols-3 gap-3 sm:grid-cols-5">
                        @foreach ($product->images as $img)
                            <div class="group relative overflow-hidden rounded-xl border border-white/10">
                                <img src="{{ str_starts_with($img->image_url, 'images/') ? asset($img->image_url) : asset('storage/' . $img->image_url) }}"
                                     class="aspect-square w-full object-cover">
                                @if ($img->is_primary)
                                    <span class="absolute left-1.5 top-1.5 rounded-full bg-brand-600 px-2 py-0.5 text-[9px] font-bold text-white">Chính</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- ═══════════ Thông số hiệu năng ═══════════ --}}
            @if ($product->performance)
                @php $p = $product->performance; @endphp
                <div class="rounded-2xl border border-white/5 bg-night-soft p-5">
                    <h3 class="mb-4 text-sm font-bold uppercase tracking-wider text-gray-400">Thông số hiệu năng</h3>

                    <div class="space-y-5">
                        <div>
                            <p class="mb-2 text-xs font-bold uppercase tracking-wide text-brand-400">Chip &amp; Benchmark</p>
                            <dl class="grid gap-3 sm:grid-cols-2">
                                @foreach ([
                                    'chipset' => 'Chip / SoC',
                                    'gpu' => 'GPU',
                                    'cpu_cores' => 'CPU',
                                    'antutu_score' => 'Antutu Benchmark',
                                    'geekbench_single' => 'Geekbench Single-Core',
                                    'geekbench_multi' => 'Geekbench Multi-Core',
                                ] as $field => $label)
                                    @if (filled($p->$field))
                                        <div>
                                            <dt class="text-xs text-gray-500">{{ $label }}</dt>
                                            <dd class="mt-0.5 text-sm font-medium text-gray-200">{{ $p->$field }}</dd>
                                        </div>
                                    @endif
                                @endforeach
                            </dl>
                        </div>

                        <div class="border-t border-white/10 pt-5">
                            <p class="mb-2 text-xs font-bold uppercase tracking-wide text-cyan-400">Màn hình</p>
                            <dl class="grid gap-3 sm:grid-cols-3">
                                @foreach ([
                                    'display_size_inch' => 'Kích thước',
                                    'display_type' => 'Loại màn hình',
                                    'refresh_rate' => 'Tần số quét',
                                ] as $field => $label)
                                    @if (filled($p->$field))
                                        <div>
                                            <dt class="text-xs text-gray-500">{{ $label }}</dt>
                                            <dd class="mt-0.5 text-sm font-medium text-gray-200">{{ $p->$field }}{{ $field === 'display_size_inch' ? '"' : '' }}</dd>
                                        </div>
                                    @endif
                                @endforeach
                            </dl>
                        </div>

                        <div class="border-t border-white/10 pt-5">
                            <p class="mb-2 text-xs font-bold uppercase tracking-wide text-purple-400">Camera</p>
                            <dl class="grid gap-3 sm:grid-cols-3">
                                @foreach ([
                                    'main_camera_mp' => 'Camera chính',
                                    'ultra_wide_camera_mp' => 'Camera siêu rộng',
                                    'front_camera_mp' => 'Camera trước',
                                    'video_recording' => 'Quay video',
                                ] as $field => $label)
                                    @if (filled($p->$field))
                                        <div>
                                            <dt class="text-xs text-gray-500">{{ $label }}</dt>
                                            <dd class="mt-0.5 text-sm font-medium text-gray-200">{{ $p->$field }}</dd>
                                        </div>
                                    @endif
                                @endforeach
                            </dl>
                        </div>

                        <div class="border-t border-white/10 pt-5">
                            <p class="mb-2 text-xs font-bold uppercase tracking-wide text-emerald-400">Pin, RAM &amp; Kết nối</p>
                            <dl class="grid gap-3 sm:grid-cols-2">
                                @foreach ([
                                    'battery_mah' => 'Dung lượng pin',
                                    'charging_speed_w' => 'Sạc nhanh',
                                    'ram' => 'RAM',
                                    'os' => 'Hệ điều hành',
                                    'network_support' => 'Kết nối mạng',
                                ] as $field => $label)
                                    @if (filled($p->$field))
                                        <div class="{{ $field === 'network_support' ? 'sm:col-span-2' : '' }}">
                                            <dt class="text-xs text-gray-500">{{ $label }}</dt>
                                            <dd class="mt-0.5 text-sm font-medium text-gray-200">
                                                {{ $p->$field }}{{ $field === 'battery_mah' ? ' mAh' : ($field === 'charging_speed_w' ? 'W' : '') }}
                                            </dd>
                                        </div>
                                    @endif
                                @endforeach
                            </dl>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        {{-- ═══════════ Cột phải ═══════════ --}}
        <div class="space-y-5">

            <div class="rounded-2xl border border-white/5 bg-night-soft p-5">
                <h3 class="mb-4 text-sm font-bold uppercase tracking-wider text-gray-400">Ảnh đại diện</h3>
                @if ($product->thumbnail)
                    <img src="{{ str_starts_with($product->thumbnail, 'images/') ? asset($product->thumbnail) : asset('storage/' . $product->thumbnail) }}"
                         class="aspect-square w-full rounded-xl border border-white/10 object-cover">
                @else
                    <div class="flex aspect-square w-full items-center justify-center rounded-xl border border-dashed border-white/15 text-xs text-gray-600">
                        Chưa có ảnh
                    </div>
                @endif
            </div>

            <div class="rounded-2xl border border-white/5 bg-night-soft p-5">
                <h3 class="mb-4 text-sm font-bold uppercase tracking-wider text-gray-400">Giá bán</h3>
                <div class="space-y-3">
                    <div>
                        <dt class="text-xs text-gray-500">Giá gốc</dt>
                        <dd class="mt-0.5 text-lg font-bold text-gray-100">{{ number_format($product->price) }}₫</dd>
                    </div>
                    @if ($product->sale_price)
                        <div>
                            <dt class="text-xs text-gray-500">Giá khuyến mãi</dt>
                            <dd class="mt-0.5 text-lg font-bold text-brand-400">{{ number_format($product->sale_price) }}₫</dd>
                        </div>
                    @endif
                </div>
            </div>

            <div class="rounded-2xl border border-white/5 bg-night-soft p-5">
                <h3 class="mb-4 text-sm font-bold uppercase tracking-wider text-gray-400">Trạng thái</h3>
                <div class="space-y-2.5">
                    <div class="flex items-center justify-between rounded-xl bg-white/[0.02] px-4 py-3">
                        <span class="text-sm font-medium text-gray-300">Hiển thị / Đang bán</span>
                        @if ($product->is_active)
                            <span class="rounded-full bg-emerald-500/15 px-2.5 py-1 text-xs font-bold text-emerald-400">Đang bán</span>
                        @else
                            <span class="rounded-full bg-gray-500/15 px-2.5 py-1 text-xs font-bold text-gray-400">Đã ẩn</span>
                        @endif
                    </div>
                    <div class="flex items-center justify-between rounded-xl bg-white/[0.02] px-4 py-3">
                        <span class="text-sm font-medium text-gray-300">Sản phẩm nổi bật</span>
                        @if ($product->is_featured)
                            <span class="rounded-full bg-amber-500/15 px-2.5 py-1 text-xs font-bold text-amber-400">Nổi bật</span>
                        @else
                            <span class="rounded-full bg-gray-500/15 px-2.5 py-1 text-xs font-bold text-gray-400">Bình thường</span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-white/5 bg-night-soft p-5 text-xs text-gray-500">
                <p>Tạo lúc: {{ $product->created_at?->format('d/m/Y H:i') }}</p>
                <p class="mt-1">Cập nhật lần cuối: {{ $product->updated_at?->format('d/m/Y H:i') }}</p>
            </div>
        </div>
    </div>
</div>
@endsection