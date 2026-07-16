/* =====================================================================
 * Nạp thư viện biểu đồ theo kiểu lazy.
 *
 * View gọi novaChart(cb) / novaChartJs(cb) qua stub đặt trong <head>;
 * callback được xếp vào window.__novaChartQueue. Module này rút hàng đợi
 * và chỉ thực sự tải ApexCharts / Chart.js khi có phần tử #chart-* sắp
 * lọt vào khung nhìn — trang không có biểu đồ thì không tải gì cả.
 * ===================================================================== */

const loaders = {
    apex: () => import('apexcharts').then((m) => m.default),
    chartjs: () => import('chart.js/auto').then((m) => m.default),
};

const cache = {};

function load(lib) {
    cache[lib] ??= loaders[lib]();
    return cache[lib];
}

function drain(queue) {
    queue.splice(0).forEach(({ lib, cb }) => load(lib).then(cb));
}

function run(queue) {
    const targets = document.querySelectorAll('[id^="chart-"]');
    if (!targets.length || !queue.length) return;

    if (!('IntersectionObserver' in window)) {
        drain(queue);
        return;
    }

    const observer = new IntersectionObserver(
        (entries) => {
            if (!entries.some((e) => e.isIntersecting)) return;
            observer.disconnect();
            drain(queue);
        },
        { rootMargin: '200px' },
    );
    targets.forEach((el) => observer.observe(el));
}

const queue = (window.__novaChartQueue ??= []);

// Module script luôn chạy trước DOMContentLoaded, nhưng vẫn phòng trường hợp ngược lại.
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => run(queue));
} else {
    run(queue);
}
