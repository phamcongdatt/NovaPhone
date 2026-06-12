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

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.reveal, .reveal-stagger').forEach((el) => revealObserver.observe(el));
    hydrateImageSkeletons();

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
