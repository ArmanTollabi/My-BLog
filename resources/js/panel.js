// پنل‌ها (داشبورد کاربر + ادمین)

// تاریخ امروز به‌صورت شمسی با ترتیب «شنبه، ۲۳ آبان ۱۴۰۵»
// (fa-IR به‌طور پیش‌فرض تقویم جلالی دارد؛ ولی ترتیب اجزا را خودش می‌چیند، پس با formatToParts دستی می‌چینیم)
(function () {
    const el = document.querySelector('[data-panel-date]');
    if (!el) return;
    try {
        const parts = {};
        new Intl.DateTimeFormat('fa-IR', {
            year: 'numeric', month: 'long', day: 'numeric', weekday: 'long',
        }).formatToParts(new Date()).forEach(function (p) { parts[p.type] = p.value; });

        el.textContent = parts.weekday + '، ' + parts.day + ' ' + parts.month + ' ' + parts.year;
    } catch (e) {}
})();

// باز و بسته شدن sheet «بیشتر» (موبایل)
(function () {
    const btn = document.getElementById('pbar-more');
    const sheet = document.getElementById('psheet');
    const overlay = document.getElementById('psheet-ov');
    if (!btn || !sheet || !overlay) return;

    function setOpen(open) {
        sheet.classList.toggle('is-open', open);
        overlay.classList.toggle('is-open', open);
        sheet.setAttribute('aria-hidden', open ? 'false' : 'true');
        btn.setAttribute('aria-expanded', open ? 'true' : 'false');
        document.body.style.overflow = open ? 'hidden' : '';
        if (open) {
            const first = sheet.querySelector('a, button');
            if (first) first.focus({preventScroll: true});
        } else {
            btn.focus({preventScroll: true});
        }
    }

    btn.addEventListener('click', function () { setOpen(!sheet.classList.contains('is-open')); });
    overlay.addEventListener('click', function () { setOpen(false); });
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && sheet.classList.contains('is-open')) setOpen(false);
    });

    // اگر صفحه به عرض دسکتاپ رفت (مثلاً چرخاندن تبلت)، sheet بسته شود
    window.matchMedia('(min-width: 1024px)').addEventListener('change', function (e) {
        if (e.matches && sheet.classList.contains('is-open')) setOpen(false);
    });
})();

// آیتم‌های کشویی سایدبار (مثلاً «پست‌ها» ← وضعیت‌ها)
(function () {
    document.querySelectorAll('[data-pnl-group]').forEach(function (group) {
        const btn = group.querySelector('[data-pnl-toggle]');
        const sub = group.querySelector('.pnl-sub');
        if (!btn || !sub) return;

        btn.addEventListener('click', function () {
            const open = !group.classList.contains('is-open');
            group.classList.toggle('is-open', open);
            btn.setAttribute('aria-expanded', open ? 'true' : 'false');
            sub.toggleAttribute('inert', !open);      // بسته: لینک‌ها با Tab فوکوس نگیرند
        });
    });
})();
