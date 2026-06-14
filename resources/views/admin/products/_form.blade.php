{{--
    Partial dùng chung cho Create & Edit.
    Biến truyền vào: $product (null khi tạo mới), $categories, $brands
--}}

@php
    $isEdit  = isset($product) && $product->exists;
    $variants = $isEdit ? $product->variants : collect();
@endphp

<div x-data="{
        variants: {{ $isEdit ? $variants->map(fn($v) => [
            'id' => $v->id,
            'name' => $v->name,
            'storage' => $v->storage,
            'color' => $v->color,
            'color_code' => $v->color_code,
            'additional_price' => (int) $v->additional_price,
            'sku' => $v->sku,
            'quantity' => $v->inventory->quantity ?? 0,
        ])->toJson() : '[]' }},
        deletedVariants: [],
        deletedImages: [],
        addVariant() {
            this.variants.push({ id: null, name: '', storage: '', color: '', color_code: '#000000', additional_price: 0, sku: '', quantity: 0 });
        },
        removeVariant(index) {
            const v = this.variants[index];
            if (v.id) this.deletedVariants.push(v.id);
            this.variants.splice(index, 1);
        },
        removeImage(id, el) {
            this.deletedImages.push(id);
            el.closest('[data-image-item]').remove();
        }
     }"
>
    <div class="grid gap-6 lg:grid-cols-3">

        {{-- ═══════════ Cột trái: thông tin chính ═══════════ --}}
        <div class="space-y-5 lg:col-span-2">

            <div class="rounded-2xl border border-white/5 bg-night-soft p-5">
                <h3 class="mb-4 text-sm font-bold uppercase tracking-wider text-gray-400">Thông tin cơ bản</h3>

                <div class="space-y-4">
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-300">Tên sản phẩm <span class="text-red-400">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $product->name ?? '') }}"
                               class="input-field" placeholder="VD: iPhone 17 Pro Max 256GB" required>
                        @error('name') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-300">Slug (URL)</label>
                        <input type="text" name="slug" value="{{ old('slug', $product->slug ?? '') }}"
                               class="input-field" placeholder="Để trống sẽ tự tạo từ tên">
                        @error('slug') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-300">Danh mục <span class="text-red-400">*</span></label>
                            <select name="category_id" class="input-field" required>
                                <option value="">-- Chọn danh mục --</option>
                                @foreach ($categories as $cat)
                                    <option value="{{ $cat->id }}" @selected(old('category_id', $product->category_id ?? null) == $cat->id)>
                                        {{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-300">Thương hiệu <span class="text-red-400">*</span></label>
                            <select name="brand_id" class="input-field" required>
                                <option value="">-- Chọn thương hiệu --</option>
                                @foreach ($brands as $brand)
                                    <option value="{{ $brand->id }}" @selected(old('brand_id', $product->brand_id ?? null) == $brand->id)>
                                        {{ $brand->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('brand_id') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-300">Mô tả ngắn</label>
                        <textarea name="description" rows="3" class="input-field">{{ old('description', $product->description ?? '') }}</textarea>
                        @error('description') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-300">Nội dung chi tiết</label>
                        <textarea name="content" rows="6" class="input-field">{{ old('content', $product->content ?? '') }}</textarea>
                        @error('content') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            {{-- ═══════════ Biến thể sản phẩm ═══════════ --}}
            <div class="rounded-2xl border border-white/5 bg-night-soft p-5">
                <div class="mb-4 flex items-center justify-between">
                    <h3 class="text-sm font-bold uppercase tracking-wider text-gray-400">Biến thể (Màu sắc / Dung lượng)</h3>
                    <button type="button" @click="addVariant()"
                            class="inline-flex items-center gap-1.5 rounded-lg bg-brand-600/15 px-3 py-1.5 text-xs font-bold text-brand-400 transition-all duration-200 hover:bg-brand-600/25">
                        <svg class="size-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                        Thêm biến thể
                    </button>
                </div>

                <template x-if="variants.length === 0">
                    <p class="rounded-xl border border-dashed border-white/10 py-6 text-center text-xs text-gray-500">
                        Chưa có biến thể. Sản phẩm sẽ dùng giá &amp; tồn kho gốc bên dưới.
                    </p>
                </template>

                <div class="space-y-3">
                    <template x-for="(variant, index) in variants" :key="index">
                        <div class="grid grid-cols-2 gap-2.5 rounded-xl border border-white/5 bg-white/[0.02] p-3.5 sm:grid-cols-6">
                            <input type="hidden" :name="`variants[${index}][id]`" x-model="variant.id">

                            <div class="col-span-2 sm:col-span-2">
                                <label class="mb-1 block text-[11px] text-gray-500">Tên biến thể</label>
                                <input type="text" :name="`variants[${index}][name]`" x-model="variant.name"
                                       class="input-field-sm" placeholder="256GB - Titan Đen" required>
                            </div>
                            <div>
                                <label class="mb-1 block text-[11px] text-gray-500">Dung lượng</label>
                                <input type="text" :name="`variants[${index}][storage]`" x-model="variant.storage"
                                       class="input-field-sm" placeholder="256GB">
                            </div>
                            <div>
                                <label class="mb-1 block text-[11px] text-gray-500">Màu</label>
                                <input type="text" :name="`variants[${index}][color]`" x-model="variant.color"
                                       class="input-field-sm" placeholder="Titan Đen">
                            </div>
                            <div>
                                <label class="mb-1 block text-[11px] text-gray-500">Mã màu</label>
                                <div class="flex items-center gap-1.5">
                                    <input type="color" :name="`variants[${index}][color_code]`" x-model="variant.color_code"
                                           class="h-9 w-9 cursor-pointer rounded-lg border border-white/10 bg-transparent">
                                </div>
                            </div>
                            <div>
                                <label class="mb-1 block text-[11px] text-gray-500">Giá cộng thêm</label>
                                <input type="number" :name="`variants[${index}][additional_price]`" x-model="variant.additional_price"
                                       class="input-field-sm" min="0" step="1000">
                            </div>

                            <div>
                                <label class="mb-1 block text-[11px] text-gray-500">SKU biến thể</label>
                                <input type="text" :name="`variants[${index}][sku]`" x-model="variant.sku"
                                       class="input-field-sm">
                            </div>
                            <div>
                                <label class="mb-1 block text-[11px] text-gray-500">Tồn kho</label>
                                <input type="number" :name="`variants[${index}][quantity]`" x-model="variant.quantity"
                                       class="input-field-sm" min="0">
                            </div>

                            <div class="col-span-2 flex items-end justify-end sm:col-span-1">
                                <button type="button" @click="removeVariant(index)"
                                        class="flex h-9 w-full items-center justify-center gap-1.5 rounded-lg bg-red-500/10 text-xs font-semibold text-red-400 transition-all duration-200 hover:bg-red-500/20 sm:w-9">
                                    <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 12h-15"/></svg>
                                    <span class="sm:hidden">Xoá</span>
                                </button>
                            </div>
                        </div>
                    </template>
                </div>

                {{-- Gửi danh sách id biến thể bị xoá --}}
                <template x-for="id in deletedVariants" :key="id">
                    <input type="hidden" name="deleted_variants[]" :value="id">
                </template>

                {{-- Tồn kho gốc — chỉ hiện khi không có biến thể nào --}}
                <div class="mt-4 grid gap-3 sm:grid-cols-2" x-show="variants.length === 0">
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-300">Tồn kho (sản phẩm không biến thể)</label>
                        <input type="number" name="base_quantity"
                               value="{{ old('base_quantity', $isEdit ? ($product->inventory->quantity ?? 0) : 0) }}"
                               class="input-field" min="0">
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-300">Ngưỡng cảnh báo hết hàng</label>
                        <input type="number" name="low_stock_threshold"
                               value="{{ old('low_stock_threshold', $isEdit ? ($product->inventory->low_stock_threshold ?? 5) : 5) }}"
                               class="input-field" min="0">
                    </div>
                </div>
            </div>

            {{-- ═══════════ Thư viện ảnh ═══════════ --}}
            <div class="rounded-2xl border border-white/5 bg-night-soft p-5">
                <h3 class="mb-4 text-sm font-bold uppercase tracking-wider text-gray-400">Thư viện ảnh</h3>

                @if ($isEdit && $product->images->isNotEmpty())
                    <div class="mb-4 grid grid-cols-3 gap-3 sm:grid-cols-5">
                        @foreach ($product->images as $img)
                            <div data-image-item class="group relative overflow-hidden rounded-xl border border-white/10">
                                <img src="{{ asset('storage/' . $img->image_url) }}" class="aspect-square w-full object-cover">
                                @if ($img->is_primary)
                                    <span class="absolute left-1.5 top-1.5 rounded-full bg-brand-600 px-2 py-0.5 text-[9px] font-bold text-white">Chính</span>
                                @endif
                                <button type="button" @click="removeImage({{ $img->id }}, $el)"
                                        class="absolute right-1.5 top-1.5 flex size-6 items-center justify-center rounded-full bg-red-500/80 text-white opacity-0 transition-opacity duration-200 group-hover:opacity-100">
                                    <svg class="size-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
                                </button>
                            </div>
                        @endforeach
                    </div>
                @endif

                {{-- Gửi danh sách id ảnh bị xoá --}}
                <template x-for="id in deletedImages" :key="id">
                    <input type="hidden" name="deleted_images[]" :value="id">
                </template>

                <input type="file" name="images[]" multiple accept="image/*"
                       class="block w-full cursor-pointer rounded-xl border border-dashed border-white/15 bg-white/[0.02] px-4 py-6 text-sm text-gray-400 file:mr-3 file:rounded-lg file:border-0 file:bg-brand-600 file:px-3 file:py-1.5 file:text-xs file:font-bold file:text-white">
                @error('images.*') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
            </div>
        </div>

        {{-- ═══════════ Cột phải: giá, trạng thái, ảnh đại diện ═══════════ --}}
        <div class="space-y-5">

            <div class="rounded-2xl border border-white/5 bg-night-soft p-5">
                <h3 class="mb-4 text-sm font-bold uppercase tracking-wider text-gray-400">Giá &amp; Mã SKU</h3>

                <div class="space-y-4">
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-300">Giá bán <span class="text-red-400">*</span></label>
                        <div class="relative">
                            <input type="number" name="price" value="{{ old('price', $product->price ?? '') }}"
                                   class="input-field pr-8" min="0" step="1000" required>
                            <span class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-sm text-gray-500">₫</span>
                        </div>
                        @error('price') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-300">Giá khuyến mãi</label>
                        <div class="relative">
                            <input type="number" name="sale_price" value="{{ old('sale_price', $product->sale_price ?? '') }}"
                                   class="input-field pr-8" min="0" step="1000">
                            <span class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-sm text-gray-500">₫</span>
                        </div>
                        @error('sale_price') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-300">SKU <span class="text-red-400">*</span></label>
                        <input type="text" name="sku" value="{{ old('sku', $product->sku ?? '') }}"
                               class="input-field" placeholder="VD: IP17PM-256" required>
                        @error('sku') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-white/5 bg-night-soft p-5">
                <h3 class="mb-4 text-sm font-bold uppercase tracking-wider text-gray-400">Ảnh đại diện</h3>

                @if ($isEdit && $product->thumbnail)
                    <img src="{{ asset('storage/' . $product->thumbnail) }}"
                         class="mb-3 aspect-square w-full rounded-xl border border-white/10 object-cover">
                @endif

                <input type="file" name="thumbnail" accept="image/*"
                       class="block w-full cursor-pointer rounded-xl border border-dashed border-white/15 bg-white/[0.02] px-4 py-6 text-sm text-gray-400 file:mr-3 file:rounded-lg file:border-0 file:bg-brand-600 file:px-3 file:py-1.5 file:text-xs file:font-bold file:text-white">
                @error('thumbnail') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
            </div>

            <div class="rounded-2xl border border-white/5 bg-night-soft p-5">
                <h3 class="mb-4 text-sm font-bold uppercase tracking-wider text-gray-400">Trạng thái</h3>

                <label class="flex items-center justify-between rounded-xl bg-white/[0.02] px-4 py-3">
                    <span class="text-sm font-medium text-gray-300">Hiển thị / Đang bán</span>
                    <input type="checkbox" name="is_active" value="1"
                           {{ old('is_active', $product->is_active ?? true) ? 'checked' : '' }}
                           class="size-5 cursor-pointer rounded-md border-white/20 bg-white/5 text-brand-600 focus:ring-brand-500/30">
                </label>

                <label class="mt-3 flex items-center justify-between rounded-xl bg-white/[0.02] px-4 py-3">
                    <span class="text-sm font-medium text-gray-300">Sản phẩm nổi bật</span>
                    <input type="checkbox" name="is_featured" value="1"
                           {{ old('is_featured', $product->is_featured ?? false) ? 'checked' : '' }}
                           class="size-5 cursor-pointer rounded-md border-white/20 bg-white/5 text-brand-600 focus:ring-brand-500/30">
                </label>
            </div>

            {{-- Nút submit --}}
            <div class="flex gap-3">
                <button type="submit"
                        class="flex-1 rounded-xl bg-brand-600 py-3 text-sm font-bold text-white shadow-lg shadow-brand-600/25 transition-all duration-200 hover:-translate-y-0.5 hover:bg-brand-500">
                    {{ $isEdit ? 'Cập nhật sản phẩm' : 'Thêm sản phẩm' }}
                </button>
                <a href="{{ route('admin.products.index') }}"
                   class="rounded-xl border border-white/10 px-5 py-3 text-sm font-bold text-gray-300 transition-all duration-200 hover:bg-white/5">
                    Hủy
                </a>
            </div>
        </div>
    </div>
</div>