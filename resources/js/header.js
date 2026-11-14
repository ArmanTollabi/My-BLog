// رفتار هدر: زیرمنوی Posts و کشوهای اعلان/منوی کاربر (قبلاً <script> داخل layouts/header.blade.php)
    // زیرمنوی Posts: با ماوس (هاور) باز می‌شود؛ روی موبایل/کیبورد با دکمه‌ی فلش
    (function () {
        const dd = document.getElementById('posts-nav');
        const toggle = document.getElementById('posts-nav-toggle');
        if (!dd || !toggle) return;

        function setOpen(open) {
            dd.classList.toggle('is-open', open);
            toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
        }

        toggle.addEventListener('click', function (e) {
            e.stopPropagation();
            setOpen(!dd.classList.contains('is-open'));
        });
        document.addEventListener('click', function (e) { if (!dd.contains(e.target)) setOpen(false); });
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && dd.classList.contains('is-open')) { setOpen(false); toggle.focus(); }
        });
    })();

    (function () {
        // نوتیفیکیشن و پنل کاربری: هر دو دراپ‌داون با یک منطق مشترک
        const pairs = [
            ['notification-button', 'notification-dropdown'],
            ['user-menu-button', 'user-dropdown']
        ].map(function (ids) {
            return {
                button: document.getElementById(ids[0]),
                panel: document.getElementById(ids[1])
            };
        }).filter(function (p) { return p.button && p.panel; });

        // برای مهمان‌ها این المان‌ها وجود ندارند
        if (!pairs.length) return;

        const mobile = window.matchMedia('(max-width: 639.98px)');

        // موبایل: کشو fixed است؛ درست زیر گروه دکمه‌ها قرار می‌گیرد (گروه = پدرِ پدرِ کشو). دسکتاپ: CSS خودش جا می‌دهد.
        function place(p) {
            const group = p.panel.parentElement.parentElement;
            p.panel.style.top = mobile.matches ? (group.getBoundingClientRect().bottom + 12) + 'px' : '';
        }

        function setOpen(p, open) {
            if (open) place(p);
            p.panel.classList.toggle('hidden', !open);
            p.button.setAttribute('aria-expanded', open ? 'true' : 'false');
        }

        function closeAll(except) {
            pairs.forEach(function (p) { if (p !== except) setOpen(p, false); });
        }

        // موبایل: با اسکرول صفحه کشوی fixed از دکمه جدا می‌شود، پس بسته شود
        window.addEventListener('scroll', function () { if (mobile.matches) closeAll(); }, {passive: true});

        pairs.forEach(function (p) {
            p.button.addEventListener('click', function (e) {
                e.stopPropagation();
                const willOpen = p.panel.classList.contains('hidden');
                closeAll(p);            // با باز شدن یکی، دیگری بسته می‌شود
                setOpen(p, willOpen);
            });
        });

        // بستن با کلیک بیرون یا Esc
        document.addEventListener('click', function (e) {
            pairs.forEach(function (p) {
                if (!p.panel.contains(e.target)) setOpen(p, false);
            });
        });
        document.addEventListener('keydown', function (e) {
            if (e.key !== 'Escape') return;
            pairs.forEach(function (p) {
                if (!p.panel.classList.contains('hidden')) {
                    setOpen(p, false);
                    p.button.focus();
                }
            });
        });
    })();
