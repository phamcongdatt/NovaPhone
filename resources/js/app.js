import './echo';

// NovaPhone public UI interactions.
// Each feature initializes only when its matching markup is present.

function initHeroSlider() {
    const root = document.querySelector('[data-hero-slider]');
    if (!root) return;

    const slides = [...root.querySelectorAll('[data-hero-slide]')];
    if (slides.length <= 1) return;

    const track = root.querySelector('[data-hero-track]');
    const prev = root.querySelector('[data-hero-prev]');
    const next = root.querySelector('[data-hero-next]');
    const dots = [...root.querySelectorAll('[data-hero-dot]')];
    if (!track) return;

    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
    let current = 0;
    let timer = null;
    let transitionTimer = null;
    let isTransitioning = false;
    let queuedIndex = null;

    const normalizeIndex = (index) => (index + slides.length) % slides.length;

    const preloadSlide = (index) => {
        const image = slides[normalizeIndex(index)]?.querySelector('[data-hero-image]');
        if (!image || image.complete) return;

        image.loading = 'eager';
        image.decode?.().catch(() => {});
    };

    const updateControls = () => {
        slides.forEach((slide, slideIndex) => {
            slide.setAttribute('aria-hidden', slideIndex === current ? 'false' : 'true');
        });

        dots.forEach((dot, dotIndex) => {
            const active = dotIndex === current;
            dot.classList.toggle('bg-black', active);
            dot.classList.toggle('bg-[#d8d4cd]', !active);
            dot.classList.toggle('scale-125', active);
            dot.setAttribute('aria-current', active ? 'true' : 'false');
        });
    };

    const finishTransition = () => {
        window.clearTimeout(transitionTimer);
        transitionTimer = null;
        isTransitioning = false;

        if (queuedIndex !== null && queuedIndex !== current) {
            const nextIndex = queuedIndex;
            queuedIndex = null;
            show(nextIndex);
        } else {
            queuedIndex = null;
        }
    };

    const show = (index) => {
        const target = normalizeIndex(index);
        if (target === current) return;

        if (isTransitioning) {
            queuedIndex = target;
            return;
        }

        current = target;
        updateControls();
        preloadSlide(current + 1);
        preloadSlide(current - 1);
        track.style.transform = `translate3d(-${current * 100}%, 0, 0)`;

        if (!reducedMotion.matches) {
            isTransitioning = true;
            transitionTimer = window.setTimeout(finishTransition, 900);
        }
    };

    const stop = () => {
        window.clearInterval(timer);
        timer = null;
    };

    const start = () => {
        stop();
        if (document.hidden || reducedMotion.matches) return;
        timer = window.setInterval(() => show(current + 1), 6500);
    };

    track.addEventListener('transitionend', (event) => {
        if (event.target === track && event.propertyName === 'transform') {
            finishTransition();
        }
    });

    prev?.addEventListener('click', () => {
        show(current - 1);
        start();
    });
    next?.addEventListener('click', () => {
        show(current + 1);
        start();
    });
    dots.forEach((dot) => {
        dot.addEventListener('click', () => {
            show(Number(dot.dataset.heroDot || 0));
            start();
        });
    });
    root.addEventListener('mouseenter', stop);
    root.addEventListener('mouseleave', start);
    root.addEventListener('focusin', stop);
    root.addEventListener('focusout', () => {
        if (!root.contains(document.activeElement)) start();
    });
    document.addEventListener('visibilitychange', () => {
        if (document.hidden) {
            stop();
        } else {
            start();
        }
    });
    reducedMotion.addEventListener?.('change', start);
    preloadSlide(1);
    start();
}

function initQuickSearch() {
    const input =
        document.getElementById('quick-search-input') ||
        document.getElementById('search-input');
    const box =
        document.getElementById('quick-search-results') ||
        document.getElementById('search-suggestions');
    if (!input || !box) return;

    const list = box.querySelector('ul') || box;
    const searchUrl = input.dataset.quickSearchUrl || box.dataset.quickSearchUrl;
    const fullResultsUrl = input.dataset.searchResultsUrl;
    const quickLinks = document.querySelector('[data-search-quick-links]');
    if (!searchUrl) return;
    let timer;
    let activeRequest;

    const fetchSuggestions = async (query) => {
        if (!query) {
            activeRequest?.abort();
            activeRequest = null;
            box.classList.add('hidden');
            list.innerHTML = '';
            quickLinks?.classList.remove('hidden');
            return;
        }

        quickLinks?.classList.add('hidden');
        activeRequest?.abort();
        const request = new AbortController();
        activeRequest = request;

        try {
            const url = new URL(searchUrl, window.location.origin);
            url.searchParams.set('q', query);
            const res = await fetch(url, {
                signal: request.signal,
                headers: { Accept: 'application/json' },
            });
            if (!res.ok) throw new Error('Search failed');
            const items = await res.json();
            if (activeRequest !== request) return;
            list.innerHTML = '';

            if (!items.length) {
                list.innerHTML = '<div class="px-4 py-4 text-center text-sm text-[#8a8a8a]">Không tìm thấy sản phẩm phù hợp</div>';
                box.classList.remove('hidden');
                return;
            }

            items.forEach((item, index) => {
                const a = document.createElement('a');
                a.href = item.url;
                a.className = 'flex items-center gap-3 px-4 py-3 transition hover:bg-[#f7f5f2]';
                const imageWrap = document.createElement('div');
                imageWrap.className = 'flex size-12 shrink-0 items-center justify-center overflow-hidden rounded-xl border border-[#eee8e1] bg-white';
                const image = document.createElement('img');
                image.src = item.thumbnail || '';
                image.alt = item.name || '';
                image.className = 'h-full w-full object-contain';
                imageWrap.appendChild(image);

                const content = document.createElement('div');
                content.className = 'min-w-0 flex-1';
                const title = document.createElement('div');
                title.className = 'flex items-center gap-2';
                const name = document.createElement('span');
                name.className = 'truncate text-sm font-semibold text-[#151515]';
                name.textContent = item.name || '';
                title.appendChild(name);
                if (index === 0) {
                    const match = document.createElement('span');
                    match.className = 'rounded-full bg-black px-2 py-0.5 text-[9px] font-bold text-white';
                    match.textContent = 'Khớp nhất';
                    title.appendChild(match);
                }
                const prices = document.createElement('div');
                prices.className = 'mt-0.5 flex items-center gap-2 text-[11px]';
                const price = document.createElement('span');
                price.className = 'font-bold text-black';
                price.textContent = item.price || '';
                prices.appendChild(price);
                if (item.old_price) {
                    const oldPrice = document.createElement('span');
                    oldPrice.className = 'text-[#9b9b9b] line-through';
                    oldPrice.textContent = item.old_price;
                    prices.appendChild(oldPrice);
                }
                content.append(title, prices);
                a.append(imageWrap, content);
                list.appendChild(a);
            });

            if (fullResultsUrl) {
                const allResults = new URL(fullResultsUrl, window.location.origin);
                allResults.searchParams.set('search', query);

                const footer = document.createElement('div');
                footer.className = 'border-t border-[#e5e5e7] p-3';
                const allResultsLink = document.createElement('a');
                allResultsLink.href = allResults.toString();
                allResultsLink.className = 'flex items-center justify-between rounded-xl px-3 py-2.5 text-sm font-semibold text-[#0066cc] transition hover:bg-[#f5f5f7]';
                const allResultsText = document.createElement('span');
                allResultsText.textContent = `Xem tất cả kết quả cho “${query}”`;
                const arrow = document.createElement('span');
                arrow.setAttribute('aria-hidden', 'true');
                arrow.textContent = '→';
                allResultsLink.append(allResultsText, arrow);
                footer.appendChild(allResultsLink);
                list.appendChild(footer);
            }

            box.classList.remove('hidden');
        } catch (error) {
            if (error.name !== 'AbortError') {
                box.classList.add('hidden');
            }
        } finally {
            if (activeRequest === request) {
                activeRequest = null;
            }
        }
    };

    input.addEventListener('input', () => {
        clearTimeout(timer);
        const query = input.value.trim();
        quickLinks?.classList.toggle('hidden', Boolean(query));
        timer = window.setTimeout(() => fetchSuggestions(query), 220);
    });

    input.addEventListener('focus', () => {
        if (input.value.trim()) fetchSuggestions(input.value.trim());
    });

    document.addEventListener('click', (e) => {
        if (!box.contains(e.target) && e.target !== input) {
            box.classList.add('hidden');
        }
    });

    document.addEventListener('nova:search-close', () => {
        clearTimeout(timer);
        activeRequest?.abort();
        activeRequest = null;
        input.value = '';
        list.innerHTML = '';
        box.classList.add('hidden');
        quickLinks?.classList.remove('hidden');
    });
}

function initSearchOverlay() {
    const header = document.querySelector('[data-site-header]');
    const overlay = document.querySelector('[data-search-overlay]');
    const openButton = document.querySelector('[data-search-open]');
    const closeButton = overlay?.querySelector('[data-search-close]');
    const input = overlay?.querySelector('#quick-search-input');
    if (!header || !overlay || !openButton || !closeButton || !input) return;

    let isOpen = false;
    let lastFocusedElement = null;
    let closeTimer;

    const syncHeaderHeight = () => {
        document.documentElement.style.setProperty('--nova-header-height', `${header.offsetHeight}px`);
    };

    const getFocusableElements = () => Array.from(
        overlay.querySelectorAll('a[href], button:not([disabled]), input:not([disabled]), select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex="-1"])')
    ).filter((element) => !element.hasAttribute('hidden') && element.getClientRects().length > 0);

    const open = () => {
        if (isOpen) return;

        isOpen = true;
        window.clearTimeout(closeTimer);
        lastFocusedElement = document.activeElement;
        syncHeaderHeight();
        overlay.removeAttribute('inert');
        overlay.setAttribute('aria-hidden', 'false');
        openButton.setAttribute('aria-expanded', 'true');
        document.body.classList.add('overflow-hidden');

        window.requestAnimationFrame(() => {
            overlay.classList.add('is-open');
            window.setTimeout(() => input.focus({ preventScroll: true }), 80);
        });
    };

    const close = () => {
        if (!isOpen) return;

        isOpen = false;
        overlay.classList.remove('is-open');
        overlay.setAttribute('aria-hidden', 'true');
        openButton.setAttribute('aria-expanded', 'false');
        document.body.classList.remove('overflow-hidden');
        document.dispatchEvent(new CustomEvent('nova:search-close'));

        closeTimer = window.setTimeout(() => {
            if (isOpen) return;
            overlay.setAttribute('inert', '');
            if (lastFocusedElement instanceof HTMLElement) {
                lastFocusedElement.focus({ preventScroll: true });
            }
        }, 300);
    };

    openButton.addEventListener('click', () => {
        if (isOpen) {
            close();
            return;
        }

        open();
    });

    closeButton.addEventListener('click', close);
    overlay.addEventListener('click', (event) => {
        if (event.target === overlay) close();
    });

    document.addEventListener('keydown', (event) => {
        if ((event.ctrlKey || event.metaKey) && event.key.toLowerCase() === 'k') {
            event.preventDefault();
            if (isOpen) {
                close();
            } else {
                open();
            }
            return;
        }

        if (!isOpen) return;

        if (event.key === 'Escape') {
            event.preventDefault();
            close();
            return;
        }

        if (event.key !== 'Tab') return;

        const focusableElements = getFocusableElements();
        if (!focusableElements.length) return;

        const first = focusableElements[0];
        const last = focusableElements[focusableElements.length - 1];

        if (event.shiftKey && document.activeElement === first) {
            event.preventDefault();
            last.focus();
        } else if (!event.shiftKey && document.activeElement === last) {
            event.preventDefault();
            first.focus();
        }
    });

    window.addEventListener('resize', syncHeaderHeight, { passive: true });
    syncHeaderHeight();
}

async function postJson(url, payload) {
    const response = await fetch(url, {
        method: 'POST',
        headers: {
            Accept: 'application/json',
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
        },
        body: JSON.stringify(payload),
    });

    const data = await response.json().catch(() => ({}));
    if (response.redirected && response.url.includes('/login')) {
        const error = new Error('Vui lòng đăng nhập để tiếp tục.');
        error.status = 401;
        throw error;
    }
    if (!response.ok) {
        const error = new Error(data.message || 'Thao tác thất bại.');
        error.status = response.status;
        throw error;
    }

    return data;
}

function initWishlistAndCompare() {
    if (!document.querySelector('[data-wishlist-toggle], [data-compare-toggle]')) return;

    document.addEventListener('click', async (event) => {
        const button = event.target.closest('[data-wishlist-toggle], [data-compare-toggle]');
        if (!button || button.disabled) return;

        event.preventDefault();
        button.disabled = true;
        const isWishlist = button.hasAttribute('data-wishlist-toggle');
        const url = isWishlist ? button.dataset.wishlistUrl : button.dataset.compareUrl;

        try {
            const data = await postJson(url, { product_id: button.dataset.productId });
            if (isWishlist) {
                const added = data.status === 'added';
                button.setAttribute('aria-pressed', added ? 'true' : 'false');
                button.querySelector('svg')?.classList.toggle('fill-black', added);
                showToast(data.message || (added ? 'Đã thêm vào danh sách yêu thích.' : 'Đã xóa khỏi danh sách yêu thích.'));
            } else {
                showToast(data.message || 'Đã thêm sản phẩm vào danh sách so sánh.');
            }
        } catch (error) {
            if (isWishlist && error.status === 401) {
                window.location.href = button.dataset.loginUrl;
            } else {
                showToast(error.message, 'error');
            }
        } finally {
            button.disabled = false;
        }
    });
}

function initCouponActions() {
    if (!document.querySelector('[data-coupon-save]')) return;

    document.addEventListener('click', async (event) => {
        const button = event.target.closest('[data-coupon-save]');
        if (!button || button.disabled || button.dataset.saved === 'true') return;

        button.disabled = true;
        try {
            const data = await postJson(button.dataset.saveUrl, {});
            button.dataset.saved = 'true';
            button.textContent = 'Đã lưu';
            button.classList.remove('bg-black', 'text-white', 'hover:bg-[#222]');
            button.classList.add('border', 'border-[#e8e4de]', 'text-[#777]');
            showToast(data.message || 'Đã lưu mã giảm giá.');
        } catch (error) {
            if (error.status === 401 && button.dataset.loginUrl) {
                window.location.href = button.dataset.loginUrl;
                return;
            }
            showToast(error.message, 'error');
            button.disabled = false;
        }
    });
}

function initReviewForm() {
    if (!document.querySelector('[data-review-form]')) return;

    document.addEventListener('submit', async (event) => {
        const form = event.target.closest('[data-review-form]');
        if (!form || form.dataset.submitting === 'true') return;

        event.preventDefault();
        form.dataset.submitting = 'true';
        const submit = form.querySelector('button[type="submit"]');
        if (submit) submit.disabled = true;

        try {
            const response = await fetch(form.action, {
                method: 'POST',
                headers: {
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                },
                body: new FormData(form),
            });
            const data = await response.json().catch(() => ({}));
            if (!response.ok) throw new Error(data.message || 'Không thể gửi đánh giá.');
            showToast(data.message || 'Đánh giá đã được gửi.');
            window.setTimeout(() => window.location.reload(), 500);
        } catch (error) {
            showToast(error.message, 'error');
            form.dataset.submitting = 'false';
            if (submit) submit.disabled = false;
        }
    });
}

function initCompareActions() {
    if (!document.querySelector('[data-compare-remove], [data-compare-clear]')) return;

    document.addEventListener('click', async (event) => {
        const remove = event.target.closest('[data-compare-remove]');
        const clear = event.target.closest('[data-compare-clear]');
        if (!remove && !clear) return;
        if (!window.confirm(clear ? 'Xóa tất cả sản phẩm so sánh?' : 'Xóa sản phẩm khỏi danh sách so sánh?')) return;

        const button = remove || clear;
        button.disabled = true;
        const url = remove ? remove.dataset.removeUrl : clear.dataset.clearUrl;
        try {
            const response = await fetch(url, {
                method: 'DELETE',
                headers: {
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                },
            });
            const data = await response.json().catch(() => ({}));
            if (!response.ok || data.success === false) throw new Error(data.message || 'Không thể cập nhật danh sách so sánh.');
            window.location.reload();
        } catch (error) {
            showToast(error.message, 'error');
            button.disabled = false;
        }
    });
}

function initNovaChat() {
    const panel = document.querySelector('[data-nova-chat-panel]');
    const toggle = document.querySelector('[data-nova-chat-toggle]');
    const close = document.querySelector('[data-nova-chat-close]');
    const form = document.querySelector('[data-nova-chat-form]');
    const input = document.querySelector('[data-nova-chat-input]');
    const messagesMount = document.querySelector('[data-nova-chat-messages]');
    const send = document.querySelector('[data-nova-chat-send]');
    const error = document.querySelector('[data-nova-chat-error]');

    if (!panel || !toggle || !close || !form || !input || !messagesMount || !send || !error) return;

    const conversation = [];
    let sending = false;

    const setOpen = (open) => {
        panel.classList.toggle('hidden', !open);
        toggle.setAttribute('aria-expanded', open ? 'true' : 'false');

        if (open) {
            window.requestAnimationFrame(() => input.focus());
        }
    };

    const hideError = () => {
        error.textContent = '';
        error.classList.add('hidden');
    };

    const showError = (message) => {
        error.textContent = String(message);
        error.classList.remove('hidden');
    };

    const scrollToLatest = () => {
        messagesMount.scrollTop = messagesMount.scrollHeight;
    };

    const safeHttpUrl = (rawUrl) => {
        try {
            const url = new URL(rawUrl, window.location.origin);
            return ['http:', 'https:'].includes(url.protocol) ? url.href : null;
        } catch {
            return null;
        }
    };

    const productRequestCache = new Map();

    const productSlugFromUrl = (rawUrl) => {
        try {
            const url = new URL(rawUrl, window.location.origin);
            const segments = url.pathname.split('/').filter(Boolean);

            if (segments.length < 2 || segments.at(-2) !== 'products') {
                return null;
            }

            const slug = decodeURIComponent(segments.at(-1));
            return /^[a-z0-9-]+$/i.test(slug) ? slug : null;
        } catch {
            return null;
        }
    };

    const productApiUrl = (slug) => {
        const template = form.dataset.productApiTemplate;

        if (!template || !template.includes('__nova_product_slug__')) {
            return null;
        }

        return template.replace('__nova_product_slug__', encodeURIComponent(slug));
    };

    const fetchProductForActions = async (slug) => {
        const apiUrl = productApiUrl(slug);
        if (!apiUrl) return null;

        if (!productRequestCache.has(apiUrl)) {
            productRequestCache.set(apiUrl, fetch(apiUrl, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
            })
                .then(async (response) => {
                    if (!response.ok) return null;

                    const payload = await response.json().catch(() => null);
                    return Number.isInteger(Number(payload?.data?.id)) ? payload.data : null;
                })
                .catch(() => null));
        }

        return productRequestCache.get(apiUrl);
    };

    const appendParagraph = (mount, text) => {
        if (!text.trim()) return;
        const paragraph = document.createElement('p');
        paragraph.className = 'whitespace-pre-wrap';
        paragraph.textContent = text;
        mount.appendChild(paragraph);
    };

    const appendImage = (mount, image) => {
        const wrap = document.createElement('div');
        wrap.className = 'overflow-hidden rounded-xl border border-[#ece8e2] bg-[#fbfaf8]';
        const element = document.createElement('img');
        element.src = image.url;
        element.alt = image.alt;
        element.loading = 'lazy';
        element.decoding = 'async';
        element.className = 'max-h-44 w-full object-contain';
        wrap.appendChild(element);
        mount.appendChild(wrap);
    };

    const appendHiddenField = (actionForm, name, value) => {
        const field = document.createElement('input');
        field.type = 'hidden';
        field.name = name;
        field.value = String(value);
        actionForm.appendChild(field);
    };

    const createCartActionForm = (action, attribute, label, buttonClass, productId) => {
        const actionForm = document.createElement('form');
        actionForm.method = 'POST';
        actionForm.action = action;
        actionForm.setAttribute(attribute, '');

        const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
        if (csrf) appendHiddenField(actionForm, '_token', csrf);

        appendHiddenField(actionForm, 'product_id', productId);
        appendHiddenField(actionForm, 'quantity', 1);

        const button = document.createElement('button');
        button.type = 'submit';
        button.className = buttonClass;
        button.textContent = label;
        actionForm.appendChild(button);

        return actionForm;
    };

    const hydrateProductActions = async (card, product) => {
        const slug = productSlugFromUrl(product.url);
        const cartAddUrl = form.dataset.cartAddUrl;
        const buyNowUrl = form.dataset.buyNowUrl;

        if (!slug || !cartAddUrl || !buyNowUrl) return;

        const productData = await fetchProductForActions(slug);
        const productId = Number(productData?.id);

        if (!Number.isInteger(productId) || productId <= 0) return;

        const actions = document.createElement('div');
        actions.className = 'mt-3 grid grid-cols-2 gap-2';
        actions.append(
            createCartActionForm(
                cartAddUrl,
                'data-cart-add-form',
                'Thêm vào giỏ hàng',
                'w-full rounded-lg border border-[#ded9d2] bg-white px-2 py-2 text-[11px] font-semibold text-[#171717] transition-colors duration-300 hover:border-black hover:bg-[#f5f4f1] focus:outline-none focus:ring-2 focus:ring-black/10',
                productId,
            ),
            createCartActionForm(
                buyNowUrl,
                'data-buy-now-form',
                'Mua ngay',
                'w-full rounded-lg bg-black px-2 py-2 text-[11px] font-semibold text-white transition-colors duration-300 hover:bg-[#222] focus:outline-none focus:ring-2 focus:ring-black/20',
                productId,
            ),
        );

        card.appendChild(actions);
        scrollToLatest();
    };

    const appendProductCard = (mount, product, image) => {
        const card = document.createElement('article');
        card.className = 'rounded-xl border border-[#e7e3dd] bg-[#fbfaf8] p-2.5 transition-colors duration-300 hover:border-[#cfc8bf] hover:bg-white';

        const productLink = document.createElement('a');
        productLink.href = product.url;
        productLink.className = 'flex items-center gap-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0a84ff]/30';

        if (image) {
            const imageWrap = document.createElement('span');
            imageWrap.className = 'flex size-16 shrink-0 items-center justify-center overflow-hidden rounded-lg border border-[#ece8e2] bg-white p-1';
            const element = document.createElement('img');
            element.src = image.url;
            element.alt = image.alt || product.title;
            element.loading = 'lazy';
            element.decoding = 'async';
            element.className = 'size-full object-contain';
            imageWrap.appendChild(element);
            productLink.appendChild(imageWrap);
        }

        const body = document.createElement('span');
        body.className = 'min-w-0 flex-1';
        const title = document.createElement('span');
        title.className = 'block truncate text-sm font-semibold text-[#171717]';
        title.textContent = product.title;
        body.appendChild(title);

        if (product.detail) {
            const detail = document.createElement('span');
            detail.className = 'mt-1 block text-xs font-medium text-[#555]';
            detail.textContent = product.detail;
            body.appendChild(detail);
        }

        productLink.appendChild(body);
        card.appendChild(productLink);
        mount.appendChild(card);
        hydrateProductActions(card, product);
    };

    const renderAssistantMessage = (mount, content) => {
        const imagePattern = /^!\[([^\]]*)\]\(([^\s()]+)\)$/;
        const linkPattern = /^\[([^\]]+)\]\(([^\s()]+)\)(.*)$/;
        const lines = String(content).replace(/\r/g, '').split('\n');
        let paragraphLines = [];
        let pendingImage = null;

        const flushParagraph = () => {
            appendParagraph(mount, paragraphLines.join('\n'));
            paragraphLines = [];
        };

        const flushPendingImage = () => {
            if (pendingImage) appendImage(mount, pendingImage);
            pendingImage = null;
        };

        lines.forEach((line) => {
            const trimmed = line.trim();
            const imageMatch = trimmed.match(imagePattern);
            const linkMatch = trimmed.match(linkPattern);

            if (imageMatch) {
                flushParagraph();
                flushPendingImage();
                const imageUrl = safeHttpUrl(imageMatch[2]);
                if (imageUrl) {
                    pendingImage = { alt: imageMatch[1], url: imageUrl };
                } else {
                    paragraphLines.push(line);
                }
                return;
            }

            if (linkMatch) {
                flushParagraph();
                const productUrl = safeHttpUrl(linkMatch[2]);
                if (productUrl) {
                    appendProductCard(mount, {
                        title: linkMatch[1],
                        url: productUrl,
                        detail: linkMatch[3].replace(/^\s*-\s*/, '').trim(),
                    }, pendingImage);
                    pendingImage = null;
                } else {
                    flushPendingImage();
                    paragraphLines.push(line);
                }
                return;
            }

            if (!trimmed) {
                flushParagraph();
                flushPendingImage();
                return;
            }

            flushPendingImage();
            paragraphLines.push(line);
        });

        flushParagraph();
        flushPendingImage();
    };

    const appendMessage = (role, content) => {
        messagesMount.querySelector('[data-nova-chat-empty]')?.remove();

        const row = document.createElement('div');
        row.className = `flex ${role === 'user' ? 'justify-end' : 'justify-start'}`;

        const bubble = document.createElement('div');
        bubble.className = role === 'user'
            ? 'max-w-[85%] whitespace-pre-wrap rounded-2xl rounded-br-md bg-black px-3.5 py-2.5 text-sm leading-6 text-white'
            : 'max-w-[85%] space-y-3 rounded-2xl rounded-bl-md border border-[#e5e1db] bg-white px-3.5 py-2.5 text-sm leading-6 text-[#303030]';

        if (role === 'assistant') {
            renderAssistantMessage(bubble, content);
        } else {
            bubble.textContent = content;
        }
        row.appendChild(bubble);
        messagesMount.appendChild(row);
        scrollToLatest();
    };

    const appendTyping = () => {
        const row = document.createElement('div');
        row.dataset.novaChatTyping = 'true';
        row.className = 'flex justify-start';
        row.innerHTML = '<div class="inline-flex items-center gap-1.5 rounded-2xl rounded-bl-md border border-[#e5e1db] bg-white px-3.5 py-3 text-xs text-[#777]"><span class="size-1.5 animate-pulse rounded-full bg-[#777]"></span><span class="size-1.5 animate-pulse rounded-full bg-[#777] [animation-delay:150ms]"></span><span class="size-1.5 animate-pulse rounded-full bg-[#777] [animation-delay:300ms]"></span><span class="sr-only">Nova AI đang trả lời</span></div>';
        messagesMount.appendChild(row);
        scrollToLatest();
        return row;
    };

    const setSending = (isSending) => {
        sending = isSending;
        send.disabled = isSending;
        input.disabled = isSending;
        messagesMount.setAttribute('aria-busy', isSending ? 'true' : 'false');
    };

    toggle.addEventListener('click', () => setOpen(panel.classList.contains('hidden')));
    close.addEventListener('click', () => setOpen(false));

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && !panel.classList.contains('hidden')) {
            setOpen(false);
            toggle.focus();
        }
    });

    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        const content = input.value.trim();
        if (!content || sending) return;

        hideError();
        input.value = '';
        appendMessage('user', content);
        conversation.push({ role: 'user', content });
        setSending(true);
        const typing = appendTyping();

        try {
            const payload = new URLSearchParams();
            conversation.forEach((message, index) => {
                payload.set(`messages[${index}][role]`, message.role);
                payload.set(`messages[${index}][content]`, message.content);
            });

            const response = await fetch(form.dataset.chatApiUrl, {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    Accept: 'application/json',
                    'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                },
                body: payload.toString(),
            });
            const data = await response.json().catch(() => ({}));

            if (!response.ok || !data.reply) {
                throw new Error(data.reply || data.message || 'Không thể kết nối với Nova AI ngay lúc này.');
            }

            conversation.push({ role: 'assistant', content: data.reply });
            appendMessage('assistant', data.reply);
        } catch (requestError) {
            showError(requestError.message || 'Không thể gửi câu hỏi. Vui lòng thử lại.');
        } finally {
            typing.remove();
            setSending(false);
            input.focus();
        }
    });
}

function showToast(message, type = 'success') {
    if (!message) return;

    let mount = document.getElementById('nova-toast-mount');
    if (!mount) {
        mount = document.createElement('div');
        mount.id = 'nova-toast-mount';
        mount.className = 'fixed right-4 top-4 z-[80] flex w-[320px] max-w-[calc(100vw-2rem)] flex-col gap-2';
        document.body.appendChild(mount);
    }

    const palette = {
        success: 'border-[#d8eadc] text-[#1e4d2b]',
        error: 'border-[#f2c7c7] text-[#9a2d2d]',
        info: 'border-[#e8e4de] text-[#222]',
    };

    const el = document.createElement('div');
    el.className = `rounded-[18px] border bg-white px-4 py-3 shadow-[0_16px_50px_rgba(0,0,0,.12)] ${palette[type] || palette.info}`;
    const safeMessage = document.createElement('span');
    safeMessage.textContent = String(message);
    el.innerHTML = `
        <div class="flex items-start gap-3">
            <div class="mt-0.5 size-2.5 rounded-full ${type === 'error' ? 'bg-[#e35b5b]' : 'bg-[#23a052]'}"></div>
            <div class="min-w-0 flex-1 text-sm font-semibold">${safeMessage.innerHTML}</div>
            <button type="button" class="text-[#9a9a9a] transition hover:text-black" aria-label="Đóng">×</button>
        </div>
    `;
    const close = () => {
        el.classList.add('opacity-0', 'translate-y-1', 'transition', 'duration-300');
        window.setTimeout(() => el.remove(), 220);
    };
    el.querySelector('button')?.addEventListener('click', close);
    mount.appendChild(el);
    window.setTimeout(close, 3000);
}

function updateCartBadge(count) {
    let badge = document.querySelector('[data-cart-badge]');
    const cartLink = document.querySelector('a[href$="/cart"]');

    if (count > 0) {
        if (!badge && cartLink) {
            badge = document.createElement('span');
            badge.setAttribute('data-cart-badge', '');
            badge.className = 'absolute -right-1 -top-1 inline-flex size-4 items-center justify-center rounded-full bg-black text-[9px] font-bold text-white';
            cartLink.appendChild(badge);
        }

        if (badge) {
            badge.textContent = String(count);
            badge.classList.remove('hidden');
        }
        return;
    }

    badge?.remove();
}

function initCartActions() {
    if (document.documentElement.dataset.novaCartActionsBound === 'true') return;
    document.documentElement.dataset.novaCartActionsBound = 'true';

    const csrf = document.querySelector('meta[name="csrf-token"]')?.content;

    async function submitCartForm(form, isBuyNow = false) {
        const formData = new FormData(form);

        try {
            const res = await fetch(form.action, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrf || '',
                },
                body: formData,
            });

            const data = await res.json().catch(() => ({}));

            if (!res.ok || data.success === false) {
                throw new Error(data.message || 'Thao tác thất bại');
            }

            if (typeof data.cart_count === 'number') {
                updateCartBadge(data.cart_count);
            }

            showToast(data.message || (isBuyNow ? 'Đã chuẩn bị thanh toán.' : 'Đã thêm vào giỏ hàng.'), 'success');

            if (isBuyNow && data.redirect_url) {
                window.location.href = data.redirect_url;
            }
        } catch (error) {
            const message = error?.message || 'Thao tác thất bại';
            showToast(message, 'error');

        }
    }

    document.addEventListener('submit', (e) => {
        const addForm = e.target.closest('[data-cart-add-form]');
        const buyNowForm = e.target.closest('[data-buy-now-form]');

        if (!addForm && !buyNowForm) {
            return;
        }

        e.preventDefault();
        submitCartForm(e.target, Boolean(buyNowForm));
    });
}

function initCartPage() {
    const page = document.querySelector('[data-cart-page]');
    if (!page || page.dataset.novaCartPageBound === 'true') return;
    page.dataset.novaCartPageBound = 'true';

    const rows = new Map();
    page.querySelectorAll('[data-cart-item]').forEach((row) => {
        if (row.dataset.itemId) {
            rows.set(row.dataset.itemId, row);
        }
    });

    if (!rows.size) return;

    const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';
    const pendingUpdates = new Map();
    const currencyFormatter = new Intl.NumberFormat('vi-VN', { maximumFractionDigits: 0 });
    let isSyncing = false;

    const normalizeQuantity = (value) => Math.max(1, Number.parseInt(value, 10) || 1);
    const formatCurrency = (value) => `${currencyFormatter.format(Math.round(Number(value) || 0))}đ`;
    const getInput = (row) => row.querySelector('[data-cart-quantity-input]');
    const getDisplayedQuantity = (row) => normalizeQuantity(getInput(row)?.value || row.dataset.confirmedQuantity);
    const getUnitPrice = (row) => Number(row.dataset.unitPrice || 0);

    const setStatus = (row, message = '') => {
        const status = row.querySelector('[data-cart-item-status]');
        if (status) status.textContent = message;
    };

    const setItemPresentation = (row, quantity) => {
        const safeQuantity = normalizeQuantity(quantity);
        const input = getInput(row);
        if (input) input.value = String(safeQuantity);

        const subtotal = getUnitPrice(row) * safeQuantity;
        const subtotalNode = row.querySelector('[data-cart-item-subtotal]');
        if (subtotalNode) {
            subtotalNode.textContent = formatCurrency(subtotal);
            subtotalNode.dataset.cartItemSubtotalRaw = String(subtotal);
        }

        row.querySelectorAll('[data-cart-quantity-change]').forEach((button) => {
            button.disabled = Number(button.dataset.quantityDelta) < 0 && safeQuantity <= 1;
        });
    };

    const setCartTotal = (total) => {
        page.querySelectorAll('[data-cart-total]').forEach((node) => {
            node.textContent = formatCurrency(total);
            node.dataset.cartTotalRaw = String(total);
        });
    };

    const refreshDisplayedTotal = () => {
        const total = [...rows.values()].reduce(
            (sum, row) => sum + (getUnitPrice(row) * getDisplayedQuantity(row)),
            0,
        );
        setCartTotal(total);
    };

    const queueUpdate = (row, quantity) => {
        const safeQuantity = normalizeQuantity(quantity);
        const itemId = row.dataset.itemId;
        if (!itemId) return;

        setItemPresentation(row, safeQuantity);
        refreshDisplayedTotal();
        setStatus(row, 'Đang cập nhật…');
        pendingUpdates.set(itemId, { row, quantity: safeQuantity });
        flushUpdates();
    };

    const flushUpdates = async () => {
        if (isSyncing) return;

        const next = pendingUpdates.entries().next();
        if (next.done) return;

        const [itemId, pending] = next.value;
        pendingUpdates.delete(itemId);
        isSyncing = true;
        pending.row.dataset.cartSyncing = 'true';

        try {
            const response = await fetch(pending.row.dataset.updateUrl, {
                method: 'PATCH',
                headers: {
                    Accept: 'application/json',
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrf,
                },
                body: JSON.stringify({ quantity: pending.quantity }),
            });

            const data = await response.json().catch(() => ({}));
            if (!response.ok || data.success === false) {
                throw new Error(data.message || 'Không thể cập nhật giỏ hàng.');
            }

            const confirmedQuantity = normalizeQuantity(data.item_quantity ?? pending.quantity);
            pending.row.dataset.confirmedQuantity = String(confirmedQuantity);

            if (!pendingUpdates.has(itemId)) {
                setItemPresentation(pending.row, confirmedQuantity);
                setStatus(pending.row);
            }

            if (typeof data.cart_count === 'number') {
                updateCartBadge(data.cart_count);
            }

            if (!pendingUpdates.size && typeof data.cart_total_raw !== 'undefined') {
                setCartTotal(data.cart_total_raw);
            }
        } catch (error) {
            if (!pendingUpdates.has(itemId)) {
                setItemPresentation(pending.row, pending.row.dataset.confirmedQuantity);
                refreshDisplayedTotal();
                setStatus(pending.row);
                showToast(error?.message || 'Không thể cập nhật giỏ hàng.', 'error');
            }
        } finally {
            pending.row.dataset.cartSyncing = 'false';
            isSyncing = false;

            if (pendingUpdates.size) {
                window.setTimeout(flushUpdates, 0);
            } else {
                refreshDisplayedTotal();
            }
        }
    };

    page.addEventListener('click', (event) => {
        const button = event.target.closest('[data-cart-quantity-change]');
        if (!button || button.disabled) return;

        const row = button.closest('[data-cart-item]');
        const input = row ? getInput(row) : null;
        if (!row || !input) return;

        event.preventDefault();
        queueUpdate(row, getDisplayedQuantity(row) + Number(button.dataset.quantityDelta || 0));
    });

    page.addEventListener('change', (event) => {
        const input = event.target.closest('[data-cart-quantity-input]');
        if (!input) return;

        const row = input.closest('[data-cart-item]');
        if (row) queueUpdate(row, input.value);
    });

    rows.forEach((row) => setItemPresentation(row, getDisplayedQuantity(row)));
    refreshDisplayedTotal();
}

function initSmoothScrollReveal() {
    const main = document.querySelector('[data-page-enter]');
    const reducedMotion = window.matchMedia?.('(prefers-reduced-motion: reduce)');
    const supportsScrollTimeline = window.CSS?.supports?.('animation-timeline', 'view()');

    if (!main || reducedMotion?.matches) return;

    const targets = new Set();

    main.querySelectorAll('[data-scroll-reveal], article, [class~="grid"] > *, section, aside').forEach((element) => {
        if (element.closest('article') && element.tagName !== 'ARTICLE') return;

        if (
            element.tagName === 'SECTION'
            && (element.querySelector('article') || element.querySelector('[class~="grid"] > *'))
        ) {
            return;
        }

        if (element.getBoundingClientRect().height < 80) return;
        targets.add(element);
    });

    if (!targets.size) return;

    let revealIndex = 0;
    const revealTargets = [];

    targets.forEach((element) => {
        const isAlreadyNearViewport = element.getBoundingClientRect().top <= window.innerHeight * 0.72;
        if (isAlreadyNearViewport) return;

        element.style.setProperty('--nova-reveal-delay', `${(revealIndex % 4) * 45}ms`);
        element.classList.add('nova-scroll-reveal');
        revealTargets.push(element);
        revealIndex += 1;
    });

    if (supportsScrollTimeline || !revealTargets.length || !('IntersectionObserver' in window)) return;

    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (!entry.isIntersecting) return;

                window.requestAnimationFrame(() => entry.target.classList.add('is-revealed'));
                observer.unobserve(entry.target);
            });
        },
        {
            rootMargin: '0px 0px 35% 0px',
            threshold: 0.01,
        },
    );

    revealTargets.forEach((element) => observer.observe(element));
}

function initCharts() {
    const queue = window.__novaChartQueue;
    if (!queue?.length || !document.querySelector('[id^="chart-"]')) return;

    import('./charts');
}

function initAuthForms() {
    const password = document.querySelector('[data-password-strength-input]');
    const strength = document.querySelector('[data-password-strength]');
    const confirmation = document.querySelector('[data-password-confirmation]');
    const confirmationStatus = document.querySelector('[data-password-confirmation-status]');

    if (!password || !strength) return;

    const segments = [...strength.querySelectorAll('[data-password-strength-segment]')];
    const label = strength.querySelector('[data-password-strength-label]');
    const levels = [
        { label: 'Chưa nhập', color: '#8b8b93', fill: '#e6e2dc' },
        { label: 'Yếu', color: '#dc2626', fill: '#ef4444' },
        { label: 'Trung bình', color: '#d97706', fill: '#f59e0b' },
        { label: 'Mạnh', color: '#0a5ec2', fill: '#0a84ff' },
        { label: 'Rất mạnh', color: '#15803d', fill: '#22c55e' },
    ];

    const scorePassword = (value) => {
        if (!value) return 0;

        let score = 1;
        if (value.length >= 8 && /[a-z]/.test(value) && /[A-Z]/.test(value)) score += 1;
        if (/\d/.test(value)) score += 1;
        if (/[^A-Za-z0-9]/.test(value) || value.length >= 12) score += 1;

        return Math.min(score, 4);
    };

    const updateConfirmation = () => {
        if (!confirmation || !confirmationStatus) return;

        const hasValue = confirmation.value.length > 0;
        const matches = hasValue && confirmation.value === password.value;
        const mismatch = hasValue && !matches;

        confirmation.setCustomValidity(mismatch ? 'Mật khẩu xác nhận chưa khớp.' : '');
        confirmationStatus.textContent = matches ? 'Mật khẩu xác nhận khớp.' : (mismatch ? 'Mật khẩu xác nhận chưa khớp.' : '');
        confirmationStatus.classList.toggle('text-green-600', matches);
        confirmationStatus.classList.toggle('text-red-600', mismatch);
        confirmationStatus.classList.toggle('text-transparent', !hasValue);
    };

    const updateStrength = () => {
        const score = scorePassword(password.value);
        const level = levels[score];

        if (label) {
            label.textContent = level.label;
            label.style.color = level.color;
        }

        segments.forEach((segment, index) => {
            segment.style.backgroundColor = index < score ? level.fill : '#e6e2dc';
        });

        updateConfirmation();
    };

    password.addEventListener('input', updateStrength);
    confirmation?.addEventListener('input', updateConfirmation);
    updateStrength();
}

function initVariantPicker() {
    const modal = document.querySelector('[data-variant-picker-modal]');

    if (!modal || modal.dataset.bound === 'true') {
        return;
    }

    modal.dataset.bound = 'true';

    const form = modal.querySelector('[data-variant-picker-form]');
    const title = modal.querySelector('[data-variant-picker-title]');
    const price = modal.querySelector('[data-variant-picker-price]');
    const groups = modal.querySelector('[data-variant-picker-groups]');
    const error = modal.querySelector('[data-variant-picker-error]');
    const submit = modal.querySelector('[data-variant-picker-submit]');
    const submitLabel = modal.querySelector('[data-variant-picker-submit-label]');
    const productIdInput = modal.querySelector('[data-variant-product-id]');
    const variantIdInput = modal.querySelector('[data-variant-id]');
    const quantityInput = modal.querySelector('[data-variant-quantity]');
    const quantityDisplay = modal.querySelector('[data-variant-quantity-display]');

    const currency = new Intl.NumberFormat('vi-VN');

    const state = {
        variants: [],
        dimensions: [],
        selected: {},
        basePrice: 0,
    };

    const closeModal = () => {
        modal.classList.add('hidden');
        modal.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('overflow-hidden');
    };

    const getDimensions = () => {
        const dimensions = [
            { key: 'color', label: 'Màu sắc' },
            { key: 'storage', label: 'Dung lượng' },
        ].filter(({ key }) => {
            return state.variants.some((variant) => variant[key]);
        });

        // Trường hợp biến thể chỉ có name
        if (!dimensions.length && state.variants.length) {
            dimensions.push({
                key: 'label',
                label: 'Phiên bản',
            });
        }

        return dimensions;
    };

    const getSelectedVariant = () => {
        return state.variants.find((variant) => {
            if (Number(variant.stock || 0) <= 0) {
                return false;
            }

            return state.dimensions.every(({ key }) => {
                return state.selected[key]
                    && String(variant[key] || '') === state.selected[key];
            });
        });
    };

    const canSelectOption = (dimensionKey, value) => {
        return state.variants.some((variant) => {
            if (Number(variant.stock || 0) <= 0) {
                return false;
            }

            if (String(variant[dimensionKey] || '') !== value) {
                return false;
            }

            return state.dimensions.every(({ key }) => {
                if (key === dimensionKey) {
                    return true;
                }

                const selectedValue = state.selected[key];

                return !selectedValue
                    || String(variant[key] || '') === selectedValue;
            });
        });
    };

    const updateSummary = () => {
        const selectedVariant = getSelectedVariant();

        variantIdInput.value = selectedVariant?.id || '';
        submit.disabled = !selectedVariant;

        if (!selectedVariant) {
            error.textContent = 'Vui lòng chọn đầy đủ biến thể sản phẩm.';
            price.textContent = `Từ ${currency.format(Math.round(state.basePrice))}đ`;
            return;
        }

        error.textContent = '';

        const finalPrice =
            state.basePrice + Number(selectedVariant.additional_price || 0);

        price.textContent = currency.format(Math.round(finalPrice)) + 'đ';
    };

    const renderGroups = () => {
        groups.replaceChildren();

        state.dimensions.forEach(({ key, label }) => {
            const values = [
                ...new Set(
                    state.variants
                        .map((variant) => String(variant[key] || ''))
                        .filter(Boolean)
                ),
            ];

            const wrapper = document.createElement('div');

            const heading = document.createElement('p');
            heading.className = 'mb-2 text-sm font-semibold text-[#171717]';
            heading.textContent = label;

            const options = document.createElement('div');
            options.className = 'flex flex-wrap gap-2';

            values.forEach((value) => {
                const button = document.createElement('button');

                button.type = 'button';
                button.textContent = value;
                button.className =
                    'rounded-xl border border-[#e8e4de] bg-white px-3 py-2 text-xs font-medium text-[#222] transition hover:border-black disabled:cursor-not-allowed disabled:opacity-40';

                const isSelected = state.selected[key] === value;

                if (isSelected) {
                    button.classList.add('border-black', 'bg-black', 'text-white');
                }

                button.disabled = !canSelectOption(key, value);

                button.addEventListener('click', () => {
                    state.selected[key] = value;

                    // Hủy lựa chọn không còn phù hợp
                    state.dimensions.forEach(({ key: otherKey }) => {
                        if (otherKey === key) {
                            return;
                        }

                        const currentValue = state.selected[otherKey];

                        if (!currentValue) {
                            return;
                        }

                        const stillValid = state.variants.some((variant) => {
                            return Number(variant.stock || 0) > 0
                                && String(variant[key] || '') === value
                                && String(variant[otherKey] || '') === currentValue;
                        });

                        if (!stillValid) {
                            state.selected[otherKey] = null;
                        }
                    });

                    renderGroups();
                    updateSummary();
                });

                options.appendChild(button);
            });

            wrapper.appendChild(heading);
            wrapper.appendChild(options);
            groups.appendChild(wrapper);
        });
    };

    const openModal = (trigger) => {
        state.variants = JSON.parse(
            trigger.dataset.variantOptions || '[]'
        );

        state.basePrice = Number(trigger.dataset.basePrice || 0);
        state.dimensions = getDimensions();
        state.selected = {};

        state.dimensions.forEach(({ key }) => {
            state.selected[key] = null;
        });

        title.textContent = trigger.dataset.productName || 'Chọn biến thể';
        productIdInput.value = trigger.dataset.productId || '';
        variantIdInput.value = '';
        quantityInput.value = '1';
        quantityDisplay.value = '1';

        const isBuyNow = trigger.dataset.variantAction === 'buy';

        form.action = isBuyNow
            ? form.dataset.buyUrl
            : form.dataset.addUrl;

        form.removeAttribute('data-cart-add-form');
        form.removeAttribute('data-buy-now-form');

        form.setAttribute(
            isBuyNow ? 'data-buy-now-form' : 'data-cart-add-form',
            ''
        );

        submitLabel.textContent = isBuyNow
            ? 'Mua ngay'
            : 'Thêm vào giỏ hàng';

        renderGroups();
        updateSummary();

        modal.classList.remove('hidden');
        modal.setAttribute('aria-hidden', 'false');
        document.body.classList.add('overflow-hidden');
    };

    quantityDisplay.addEventListener('input', () => {
        const quantity = Math.max(
            1,
            Math.min(100, Number.parseInt(quantityDisplay.value || '1', 10))
        );

        quantityDisplay.value = quantity;
        quantityInput.value = quantity;
    });

    form.addEventListener('submit', (event) => {
        if (!variantIdInput.value) {
            event.preventDefault();
            event.stopPropagation();
            error.textContent = 'Vui lòng chọn đầy đủ biến thể sản phẩm.';
            return;
        }

        closeModal();
    });

    document.addEventListener('click', (event) => {
        const openButton = event.target.closest('[data-variant-picker-open]');

        if (openButton) {
            event.preventDefault();
            openModal(openButton);
            return;
        }

        if (event.target.closest('[data-variant-picker-close]')) {
            event.preventDefault();
            closeModal();
        }
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && !modal.classList.contains('hidden')) {
            closeModal();
        }
    });
}

document.addEventListener('DOMContentLoaded', () => {
    initHeroSlider();
    initNovaChat();
    initSearchOverlay();
    initQuickSearch();
    initCartActions();
    initVariantPicker();
    initCartPage();
    initSmoothScrollReveal();
    initWishlistAndCompare();
    initCouponActions();
    initReviewForm();
    initCompareActions();
    initCharts();
    initAuthForms();

    document.querySelectorAll('[data-toast]').forEach((node) => {
        showToast(node.dataset.toast || node.textContent.trim(), node.dataset.toastType || 'success');
        node.remove();
    });
});

window.addEventListener('nova-toast', (e) => {
    showToast(e.detail?.message || '', e.detail?.type || 'success');
});
