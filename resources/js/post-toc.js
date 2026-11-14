// فهرست مطالب صفحه‌ی پست: از h1/h2/h3 داخل [data-reading-body] ساخته می‌شود.
// کلیک روی هر مورد ← اسکرول نرم به همان بخش؛ مورد بخشِ در حال خواندن هایلایت می‌شود.
// مارکاپ: posts/show.blade.php (<details data-toc> با <ol data-toc-list>)
const body = document.querySelector('[data-reading-body]');
const toc = document.querySelector('[data-toc]');
const list = toc ? toc.querySelector('[data-toc-list]') : null;
const layout = document.querySelector('.ps-layout');

if (body && toc && list) {
    const headings = [...body.querySelectorAll('h1, h2, h3')].filter(h => h.textContent.trim() !== '');
    const desktop = window.matchMedia('(min-width: 1024px)');
    const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)');

    if (!headings.length) {
        // نه فهرست دارد نه دسته‌بندی ← ستون کناری حذف شود
        if (layout && !layout.querySelector('.ps-cats')) layout.classList.add('ps-layout--solo');
    } else {
        const minLevel = Math.min(...headings.map(h => Number(h.tagName[1])));
        const links = headings.map(function (h, i) {
            if (!h.id) h.id = 'sec-' + (i + 1);

            const li = document.createElement('li');
            li.className = 'ps-toc-l' + (Number(h.tagName[1]) - minLevel);
            const a = document.createElement('a');
            a.href = '#' + h.id;
            a.textContent = h.textContent.trim();
            li.appendChild(a);
            list.appendChild(li);
            return a;
        });

        toc.hidden = false;
        toc.open = desktop.matches;                       // موبایل: بسته | دسکتاپ: باز

        function goTo(h) {
            h.scrollIntoView({behavior: reduceMotion.matches ? 'auto' : 'smooth', block: 'start'});
            history.replaceState(null, '', '#' + h.id);
        }

        list.addEventListener('click', function (e) {
            const a = e.target.closest('a');
            if (!a) return;
            e.preventDefault();
            goTo(headings[links.indexOf(a)]);
            if (!desktop.matches) toc.open = false;       // موبایل: بعد از انتخاب، فهرست جمع شود
        });

        // هایلایت بخش جاری
        const side = toc.closest('.ps-side');
        let active = -1;
        let ticking = false;

        function update() {
            ticking = false;
            let now = -1;
            if (body.getBoundingClientRect().bottom > 0) {
                headings.forEach(function (h, i) { if (h.getBoundingClientRect().top <= 140) now = i; });
            }
            if (now === active) return;

            if (active > -1) { links[active].classList.remove('is-active'); links[active].removeAttribute('aria-current'); }
            active = now;
            if (active < 0) return;

            const a = links[active];
            a.classList.add('is-active');
            a.setAttribute('aria-current', 'true');

            // اگر ستون کناری اسکرول داخلی دارد، مورد فعال دیده شود
            if (side && desktop.matches && side.scrollHeight > side.clientHeight) {
                const r = a.getBoundingClientRect();
                const s = side.getBoundingClientRect();
                if (r.top < s.top || r.bottom > s.bottom) side.scrollTop += r.top - s.top - 60;
            }
        }

        window.addEventListener('scroll', function () {
            if (!ticking) { ticking = true; requestAnimationFrame(update); }
        }, {passive: true});
        window.addEventListener('resize', update);
        update();

        // لینک مستقیم به یک بخش (#sec-3): بعد از ساخت id ها به آن برو
        if (location.hash) {
            const target = document.getElementById(decodeURIComponent(location.hash.slice(1)));
            if (target && body.contains(target)) requestAnimationFrame(() => target.scrollIntoView({block: 'start'}));
        }
    }
}
