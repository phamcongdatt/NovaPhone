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
