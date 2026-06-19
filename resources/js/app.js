/* =====================================================================
 * NovaPhone - Tương tác giao diện toàn cục
 * 1. Dark mode (lưu lựa chọn vào localStorage)
 * 2. Fade-in khi cuộn trang (IntersectionObserver)
 * 3. Skeleton loading cho ảnh sản phẩm
 * ===================================================================== */

// ---------- 1. Dark mode ----------
function applyTheme(theme) {
    document.documentElement.classList.toggle('dark', theme === 'dark');
    localStorage.setItem('nova-theme', theme);
    document.querySelectorAll('[data-theme-toggle]').forEach((btn) => {
        btn.setAttribute('aria-pressed', theme === 'dark');
    });
}

document.addEventListener('click', (e) => {
    const toggle = e.target.closest('[data-theme-toggle]');
    if (!toggle) return;
    const isDark = document.documentElement.classList.contains('dark');
    applyTheme(isDark ? 'light' : 'dark');
});

// ---------- 2. Fade-in khi cuộn ----------
const revealObserver = new IntersectionObserver(
    (entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                entry.target.classList.add('revealed');
                revealObserver.unobserve(entry.target);
            }
        });
    },
    { threshold: 0.12, rootMargin: '0px 0px -40px 0px' },
);

// ---------- 3. Skeleton loading cho ảnh ----------
function hydrateImageSkeletons(root = document) {
    root.querySelectorAll('img[data-skeleton]').forEach((img) => {
        const wrapper = img.closest('.skeleton');
        if (!wrapper) return;
        const done = () => wrapper.classList.remove('skeleton');
        if (img.complete && img.naturalWidth > 0) {
            done();
        } else {
            img.addEventListener('load', done, { once: true });
            img.addEventListener('error', done, { once: true });
        }
    });
}

function initHeroSlider() {
    const root = document.querySelector('[data-hero-slider]');
    if (!root) return;

    const slides = [
        {
            title: 'iPhone <span class="bg-gradient-to-r from-brand-400 to-cyan-300 bg-clip-text text-transparent">17 Pro Max</span>',
            subtitle: 'Titanium. Mạnh mẽ. Đẳng cấp.',
            chips: ['A19 Pro Chip', 'Camera 48MP', 'Pin cả ngày'],
            image: '/images/products/iphone-17-pro-max.jpg',
            alt: 'iPhone 17 Pro Max — flagship mới nhất tại NovaPhone',
            href: '/products/iphone-17-pro-max',
        },
        {
            title: 'Galaxy <span class="bg-gradient-to-r from-brand-400 to-cyan-300 bg-clip-text text-transparent">S24 Ultra</span>',
            subtitle: 'Camera sắc nét. Hiệu năng bền bỉ.',
            chips: ['S Pen', 'Camera 200MP', 'Pin 5000mAh'],
            image: '/images/banners/galaxy-s24-ultra.jpg',
            alt: 'Samsung Galaxy S24 Ultra 256GB',
            href: '/products/samsung-galaxy-s24-ultra-256gb',
        },
        {
            title: 'Xiaomi <span class="bg-gradient-to-r from-brand-400 to-cyan-300 bg-clip-text text-transparent">14T Pro</span>',
            subtitle: 'Sạc siêu nhanh. Camera Leica.',
            chips: ['512GB', '120W', 'Leica'],
            image: '/images/banners/xiaomi-14t-pro.jpg',
            alt: 'Xiaomi 14T Pro 512GB',
            href: '/products/xiaomi-14t-pro-512gb',
        },
        {
            title: 'OPPO <span class="bg-gradient-to-r from-brand-400 to-cyan-300 bg-clip-text text-transparent">Find X7 Ultra</span>',
            subtitle: 'Thiết kế sang. Chụp ảnh đỉnh.',
            chips: ['256GB', 'Camera Hasselblad', 'Màn hình cong'],
            image: '/images/banners/oppo-find-x7-ultra.jpeg',
            alt: 'OPPO Find X7 Ultra 256GB',
            href: '/products/oppo-find-x7-ultra-256gb',
        },
    ];

    const title = root.querySelector('[data-hero-title]');
    const subtitle = root.querySelector('[data-hero-subtitle]');
    const chips = root.querySelector('[data-hero-chips]');
    const image = root.querySelector('[data-hero-image]');
    const buy = root.querySelector('[data-hero-buy]');
    const detail = root.querySelector('[data-hero-detail]');
    const prev = root.querySelector('[data-hero-prev]');
    const next = root.querySelector('[data-hero-next]');
    const dots = [...root.querySelectorAll('[data-hero-dot]')];
    const autoplayDelay = 5000;
    let current = 0;
    let autoplayTimer;

    const chipClass = 'rounded-full border border-white/10 bg-white/5 px-4 py-1.5 text-xs font-semibold text-gray-300 backdrop-blur';

    function render(index) {
        current = (index + slides.length) % slides.length;
        const slide = slides[current];

        title.innerHTML = slide.title;
        subtitle.textContent = slide.subtitle;
        chips.innerHTML = slide.chips.map((chip) => `<span class="${chipClass}">${chip}</span>`).join('');
        image.src = slide.image;
        image.alt = slide.alt;
        buy.href = slide.href;
        detail.href = slide.href;

        dots.forEach((dot, dotIndex) => {
            const active = dotIndex === current;
            dot.classList.toggle('h-1.5', active);
            dot.classList.toggle('w-6', active);
            dot.classList.toggle('bg-brand-500', active);
            dot.classList.toggle('size-1.5', !active);
            dot.classList.toggle('bg-white/25', !active);
            dot.setAttribute('aria-current', active ? 'true' : 'false');
        });
    }

    function stopAutoplay() {
        window.clearInterval(autoplayTimer);
    }

    function startAutoplay() {
        stopAutoplay();
        autoplayTimer = window.setInterval(() => render(current + 1), autoplayDelay);
    }

    function goTo(index) {
        render(index);
        startAutoplay();
    }

    prev?.addEventListener('click', () => goTo(current - 1));
    next?.addEventListener('click', () => goTo(current + 1));
    dots.forEach((dot) => {
        dot.addEventListener('click', () => goTo(Number(dot.dataset.heroDot || 0)));
    });
    root.addEventListener('mouseenter', stopAutoplay);
    root.addEventListener('mouseleave', startAutoplay);

    startAutoplay();
}

document.addEventListener('DOMContentLoaded', () => {
    // --- Live search suggestions ---
    const searchInput = document.getElementById('search-input');
    const suggestionsBox = document.getElementById('search-suggestions');
    const suggestionsList = suggestionsBox ? suggestionsBox.querySelector('ul') : null;

    let debounceTimer;
    const debounce = (func, delay) => {
        return (...args) => {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => func.apply(this, args), delay);
        };
    };

    const formatVND = (price) => {
        if (!price) return '';
        return new Intl.NumberFormat('vi-VN').format(Math.round(price)) + ' đ';
    };

    const fetchSuggestions = async (query) => {
        if (!query) {
            suggestionsBox.classList.add('hidden');
            suggestionsList.innerHTML = '';
            return;
        }
        try {
            const res = await fetch(`/search?q=${encodeURIComponent(query)}`);
            if (!res.ok) throw new Error('Network response was not ok');
            const data = await res.json();
            
            suggestionsList.innerHTML = '';

            if (data.length === 0) {
                const li = document.createElement('li');
                li.className = 'px-4 py-4 text-center text-sm text-gray-400';
                li.innerHTML = `
                    <svg class="mx-auto size-6 text-gray-600 mb-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/>
                    </svg>
                    Không tìm thấy sản phẩm phù hợp
                `;
                suggestionsList.appendChild(li);
                suggestionsBox.classList.remove('hidden');
                return;
            }

            data.forEach((item, index) => {
                const li = document.createElement('li');
                const a = document.createElement('a');
                a.href = `/products/${item.slug}`;
                a.className = 'flex items-center gap-3 px-4 py-3 hover:bg-white/5 transition-colors duration-150';

                // Thumbnail
                const imgUrl = item.thumbnail || '/images/placeholder.png';
                const img = document.createElement('img');
                img.src = imgUrl;
                img.alt = item.name;
                img.className = 'size-11 rounded-lg bg-white/5 object-contain p-1 border border-white/5';

                // Text details
                const infoDiv = document.createElement('div');
                infoDiv.className = 'flex-1 min-w-0';

                // Badge + Name row
                const nameRow = document.createElement('div');
                nameRow.className = 'flex items-center gap-2 mb-0.5';

                const nameSpan = document.createElement('span');
                nameSpan.className = 'text-sm font-semibold text-white truncate block';
                nameSpan.textContent = item.name;
                nameRow.appendChild(nameSpan);

                // Best Match Badge for first item
                if (index === 0) {
                    const badge = document.createElement('span');
                    badge.className = 'inline-flex items-center rounded-md bg-brand-500/10 px-2 py-0.5 text-[10px] font-bold text-brand-400 ring-1 ring-inset ring-brand-500/20';
                    badge.textContent = 'Khớp nhất';
                    nameRow.appendChild(badge);
                } else if (index === 1) {
                    const badge = document.createElement('span');
                    badge.className = 'inline-flex items-center rounded-md bg-white/5 px-2 py-0.5 text-[10px] font-medium text-gray-400 border border-white/10';
                    badge.textContent = 'Liên quan';
                    nameRow.appendChild(badge);
                }
                
                infoDiv.appendChild(nameRow);

                // Pricing row
                const priceRow = document.createElement('div');
                priceRow.className = 'flex items-baseline gap-2';

                const priceVal = parseFloat(item.sale_price || item.price);
                const originalVal = item.sale_price ? parseFloat(item.price) : null;

                const priceSpan = document.createElement('span');
                priceSpan.className = 'text-xs font-bold text-brand-400';
                priceSpan.textContent = formatVND(priceVal);
                priceRow.appendChild(priceSpan);

                if (originalVal) {
                    const origSpan = document.createElement('span');
                    origSpan.className = 'text-[10px] text-gray-500 line-through';
                    origSpan.textContent = formatVND(originalVal);
                    priceRow.appendChild(origSpan);
                }

                infoDiv.appendChild(priceRow);

                a.appendChild(img);
                a.appendChild(infoDiv);
                li.appendChild(a);
                suggestionsList.appendChild(li);
            });

            suggestionsBox.classList.remove('hidden');
        } catch (e) {
            console.error('Search suggestion error:', e);
            if (suggestionsBox) suggestionsBox.classList.add('hidden');
        }
    };

    const debouncedFetch = debounce(() => {
        if (searchInput) {
            fetchSuggestions(searchInput.value.trim());
        }
    }, 300);

    if (searchInput && suggestionsBox) {
        searchInput.addEventListener('input', debouncedFetch);
        // Show suggestions on focus if there's text
        searchInput.addEventListener('focus', () => {
            if (searchInput.value.trim()) {
                fetchSuggestions(searchInput.value.trim());
            }
        });
        // Hide suggestions when clicking outside
        document.addEventListener('click', (e) => {
            if (!suggestionsBox.contains(e.target) && e.target !== searchInput) {
                suggestionsBox.classList.add('hidden');
            }
        });
    }

    document.querySelectorAll('.reveal, .reveal-stagger').forEach((el) => revealObserver.observe(el));
    hydrateImageSkeletons();
    initHeroSlider();

    // Menu mobile
    const menuBtn = document.querySelector('[data-mobile-menu-toggle]');
    const menu = document.querySelector('[data-mobile-menu]');
    if (menuBtn && menu) {
        menuBtn.addEventListener('click', () => {
            const open = menu.classList.toggle('hidden') === false;
            menuBtn.setAttribute('aria-expanded', open);
        });
    }
});
