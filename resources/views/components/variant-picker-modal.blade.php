<div
    data-variant-picker-modal
    class="fixed inset-0 z-[90] hidden"
    aria-hidden="true"
>
    <div
        data-variant-picker-close
        class="absolute inset-0 bg-black/50 backdrop-blur-sm"
    ></div>

    <section
        class="relative mx-auto mt-[10vh] w-[calc(100%-2rem)] max-w-lg rounded-[24px] bg-white p-5 shadow-2xl"
        role="dialog"
        aria-modal="true"
        aria-labelledby="variant-picker-title"
    >
        <div class="flex items-start justify-between gap-4">
            <div>
                <h2
                    id="variant-picker-title"
                    data-variant-picker-title
                    class="text-lg font-bold text-[#171717]"
                >
                    Chọn biến thể
                </h2>

                <p
                    data-variant-picker-price
                    class="mt-1 text-sm font-semibold text-[#111]"
                ></p>
            </div>

            <button
                type="button"
                data-variant-picker-close
                class="grid size-8 place-items-center rounded-full border border-[#e8e4de] text-lg text-[#777] hover:border-black hover:text-black"
                aria-label="Đóng"
            >
                ×
            </button>
        </div>

        <form
            method="POST"
            action="{{ route('cart.store') }}"
            data-variant-picker-form
            data-cart-add-form
            data-add-url="{{ route('cart.store') }}"
            data-buy-url="{{ route('cart.buy-now') }}"
            class="mt-5 space-y-5"
        >
            @csrf

            <input type="hidden" name="product_id" data-variant-product-id>
            <input type="hidden" name="variant_id" data-variant-id>
            <input type="hidden" name="quantity" value="1" data-variant-quantity>

            <div
                data-variant-picker-groups
                class="space-y-4"
            ></div>

            <div class="flex items-center justify-between gap-3">
                <label class="text-sm font-semibold text-[#171717]">
                    Số lượng
                </label>

                <input
                    type="number"
                    min="1"
                    max="100"
                    value="1"
                    data-variant-quantity-display
                    class="w-20 rounded-xl border border-[#e8e4de] px-3 py-2 text-center text-sm outline-none focus:border-black"
                >
            </div>

            <p
                data-variant-picker-error
                class="min-h-5 text-sm text-red-600"
                aria-live="polite"
            ></p>

            <div class="grid grid-cols-2 gap-3">
                <button
                    type="button"
                    data-variant-picker-close
                    class="rounded-full border border-[#e8e4de] px-4 py-3 text-sm font-semibold text-[#333] hover:border-black"
                >
                    Hủy
                </button>

                <button
                    type="submit"
                    data-variant-picker-submit
                    disabled
                    class="rounded-full bg-black px-4 py-3 text-sm font-semibold text-white transition hover:bg-[#222] disabled:cursor-not-allowed disabled:opacity-40"
                >
                    <span data-variant-picker-submit-label>
                        Thêm vào giỏ hàng
                    </span>
                </button>
            </div>
        </form>
    </section>
</div>
