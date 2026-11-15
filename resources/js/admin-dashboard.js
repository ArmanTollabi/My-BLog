// نمودارهای داشبورد ادمین (قبلاً <script> داخل admin/dashboard.blade.php؛ Chart.js حالا از npm می‌آید نه CDN)
import Chart from 'chart.js/auto';

(function () {
    const dataEl = document.getElementById('admin-dashboard-data');
    const usersEl = document.getElementById('usersChart');
    if (!dataEl || !usersEl) return;   // صفحه‌ی داشبورد نیست یا داده/بوم وجود ندارد

    const {chart: DATA, categories: CATEGORIES} = JSON.parse(dataEl.textContent);

    Chart.defaults.color = 'rgba(255,255,255,.65)';
    Chart.defaults.borderColor = 'rgba(255,255,255,.08)';
    Chart.defaults.font.family = getComputedStyle(document.body).fontFamily;

    // ریسپانسیو: روی موبایل برچسب‌ها کمتر، فونت کوچک‌تر و محور دوم مخفی
    const mq = window.matchMedia('(max-width: 639px)');
    Chart.defaults.font.size = mq.matches ? 11 : 12;
    const xTicks = () => ({autoSkip: true, maxRotation: 0, maxTicksLimit: mq.matches ? 5 : 12});

    const tooltip = {
        backgroundColor: 'rgba(24,12,48,.92)',
        borderColor: 'rgba(255,255,255,.15)',
        borderWidth: 1,
        padding: 10,
        cornerRadius: 12,
    };

    function gradient(ctx, from, to) {
        const g = ctx.createLinearGradient(0, 0, 0, 320);
        g.addColorStop(0, from);
        g.addColorStop(1, to);
        return g;
    }

    // ── رشد کاربران: خط تجمعی + ستون کاربران جدید ──
    const usersCtx = usersEl.getContext('2d');
    const usersChart = new Chart(usersCtx, {
        type: 'line',
        data: {
            labels: [],
            datasets: [
                {
                    label: 'کل کاربران',
                    data: [],
                    borderColor: '#a78bfa',
                    backgroundColor: gradient(usersCtx, 'rgba(139,92,246,.55)', 'rgba(139,92,246,0)'),
                    fill: true, tension: .4, borderWidth: 3,
                    pointRadius: 3, pointHoverRadius: 6, pointBackgroundColor: '#fff',
                    yAxisID: 'y', order: 1,
                },
                {
                    type: 'bar',
                    label: 'کاربران جدید',
                    data: [],
                    backgroundColor: 'rgba(34,211,238,.45)',
                    borderRadius: 6, maxBarThickness: 22,
                    yAxisID: 'y1', order: 2,
                },
            ],
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            interaction: {mode: 'index', intersect: false},
            plugins: {legend: {labels: {usePointStyle: true, boxWidth: 8}}, tooltip},
            scales: {
                x: {grid: {display: false}, ticks: xTicks()},
                y: {beginAtZero: true, ticks: {precision: 0}},
                y1: {display: !mq.matches, beginAtZero: true, position: 'right', grid: {display: false}, ticks: {precision: 0}},
            },
        },
    });

    // ── فعالیت: پست و کامنت ──
    const activityChart = new Chart(document.getElementById('activityChart'), {
        type: 'bar',
        data: {
            labels: [],
            datasets: [
                {label: 'پست', data: [], backgroundColor: 'rgba(217,70,239,.6)', borderRadius: 6, maxBarThickness: 22},
                {label: 'کامنت', data: [], backgroundColor: 'rgba(34,211,238,.6)', borderRadius: 6, maxBarThickness: 22},
            ],
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            interaction: {mode: 'index', intersect: false},
            plugins: {legend: {labels: {usePointStyle: true, boxWidth: 8}}, tooltip},
            scales: {x: {grid: {display: false}, ticks: xTicks()}, y: {beginAtZero: true, ticks: {precision: 0}}},
        },
    });

    // ── دونات کتگوری‌ها ──
    const categoryEl = document.getElementById('categoryChart');
    if (categoryEl && CATEGORIES.length) {
        new Chart(categoryEl, {
            type: 'doughnut',
            data: {
                labels: CATEGORIES.map(c => c.label),
                datasets: [{
                    data: CATEGORIES.map(c => c.value),
                    backgroundColor: ['#8b5cf6', '#22d3ee', '#d946ef', '#34d399', '#fbbf24', '#fb7185', '#60a5fa', '#a3e635'],
                    borderColor: 'rgba(24,12,48,.8)', borderWidth: 3, hoverOffset: 8,
                }],
            },
            options: {
                responsive: true, maintainAspectRatio: false, cutout: '62%',
                plugins: {legend: {position: 'bottom', labels: {usePointStyle: true, boxWidth: 8}}, tooltip},
            },
        });
    }

    // ── انتخاب بازه ──
    const buttons = document.querySelectorAll('#range-buttons [data-range]');

    function setRange(range) {
        const d = DATA[range];

        usersChart.data.labels = d.labels;
        usersChart.data.datasets[0].data = d.users;
        usersChart.data.datasets[1].data = d.newUsers;
        usersChart.update();

        activityChart.data.labels = d.labels;
        activityChart.data.datasets[0].data = d.posts;
        activityChart.data.datasets[1].data = d.comments;
        activityChart.update();

        buttons.forEach(function (b) {
            const active = b.dataset.range === range;
            b.classList.toggle('btn-create', active);
            b.classList.toggle('btn-glass', !active);
        });
    }

    buttons.forEach(function (b) {
        b.addEventListener('click', function () { setRange(b.dataset.range); });
    });

    // با عوض شدن عرض (چرخاندن گوشی و ...) تنظیمات چارت‌ها به‌روز شود
    mq.addEventListener('change', function () {
        Chart.defaults.font.size = mq.matches ? 11 : 12;
        [usersChart, activityChart].forEach(function (c) {
            c.options.scales.x.ticks = xTicks();
            if (c.options.scales.y1) c.options.scales.y1.display = !mq.matches;
            c.update();
        });
    });

    setRange('30d');

})();
