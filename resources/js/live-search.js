// جستجوی زنده‌ی سراسری (قبلاً <script> داخل layouts/_live-search.blade.php)
    (function () {
        const MIN_CHARS = 2;       // حداقل تعداد حرف برای شروع جستجو
        const WAIT_MS   = 1500;    // مکث بعد از آخرین حرف (۱ تا ۲ ثانیه)

        const root = document.getElementById('ls');
        if (!root) return;
        const SEARCH_URL = root.dataset.searchUrl;   // آدرس‌ها از data-attribute ویو خوانده می‌شوند
        const ALL_URL = root.dataset.allUrl;
        const form = document.getElementById('ls-form');
        const input = document.getElementById('ls-input');
        const body = document.getElementById('ls-body');
        const bar = document.getElementById('ls-wait');
        const spin = document.getElementById('ls-spin');
        const clearBtn = document.getElementById('ls-clear');

        let timer = null, ctrl = null, lastFocus = null;
        let items = [], active = -1;
        let shownQuery = '';              // عبارتی که نتیجه‌اش الان روی صفحه است
        const cache = new Map();          // عبارت → پاسخ (برای جلوگیری از درخواست تکراری)

        bar.style.setProperty('--ls-wait', WAIT_MS + 'ms');

        const fa = function (n) { return Number(n).toLocaleString('fa-IR'); };
        const len = function (s) { return Array.from(s).length; };
        const norm = function (s) { return s.replace(/\s+/g, ' ').trim().replace(/ي/g, 'ی').replace(/ك/g, 'ک'); };

        // ---------- ساخت DOM (همه‌چیز با textContent؛ بدون innerHTML برای امنیت) ----------
        function el(tag, cls, text) {
            const n = document.createElement(tag);
            if (cls) n.className = cls;
            if (text != null) n.textContent = text;
            return n;
        }
        function svg(path, cls) {
            const s = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
            s.setAttribute('viewBox', '0 0 24 24'); s.setAttribute('fill', 'none'); s.setAttribute('stroke', 'currentColor');
            s.setAttribute('stroke-width', '1.8'); s.setAttribute('aria-hidden', 'true');
            if (cls) s.setAttribute('class', cls);
            const p = document.createElementNS('http://www.w3.org/2000/svg', 'path');
            p.setAttribute('stroke-linecap', 'round'); p.setAttribute('stroke-linejoin', 'round'); p.setAttribute('d', path);
            s.appendChild(p);
            return s;
        }
        const ICON_SEARCH = 'm21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z';
        const ICON_IMG = 'm2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909M3.75 21h16.5A1.5 1.5 0 0 0 21.75 19.5v-15A1.5 1.5 0 0 0 20.25 3H3.75A1.5 1.5 0 0 0 2.25 4.5v15A1.5 1.5 0 0 0 3.75 21Z';
        const ICON_ARROW = 'M10.5 4.5 3 12m0 0 7.5 7.5M3 12h18';
        const ICON_WARN = 'M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z';

        function highlight(text, q) {
            const frag = document.createDocumentFragment();
            const i = text.toLowerCase().indexOf(q.toLowerCase());
            if (i < 0 || !q) { frag.appendChild(document.createTextNode(text)); return frag; }
            frag.appendChild(document.createTextNode(text.slice(0, i)));
            frag.appendChild(el('mark', 'ls-mark', text.slice(i, i + q.length)));
            frag.appendChild(document.createTextNode(text.slice(i + q.length)));
            return frag;
        }

        function setBody(node) { items = []; active = -1; body.replaceChildren(node); body.scrollTop = 0; }

        function message(icon, title, text, extra) {
            const box = el('div', 'ls-msg');
            const ic = el('div', 'ls-ico'); ic.appendChild(svg(icon)); box.appendChild(ic);
            box.appendChild(el('strong', null, title));
            if (text) {
                const p = el('span');
                if (typeof text === 'string') p.textContent = text; else p.appendChild(text);
                box.appendChild(p);
            }
            if (extra) box.appendChild(extra);
            return box;
        }

        function showIdle(n) {
            const keys = el('div', 'ls-keys');
            keys.append('برای بستن ', el('kbd', null, 'Esc'), ' را بزنید');
            setBody(message(ICON_SEARCH,
                n === 0 ? 'دنبال چه پستی هستید؟' : 'یک حرف دیگر بنویسید',
                n === 0 ? 'عنوان یا بخشی از متن پست را بنویسید.' : 'برای شروع جستجو حداقل ' + fa(MIN_CHARS) + ' حرف لازم است.',
                keys));
        }

        function showWait() {
            const keys = el('div', 'ls-keys');
            keys.append('برای جستجوی فوری ', el('kbd', null, 'Enter'), ' را بزنید');
            setBody(message(ICON_SEARCH, 'تا لحظه‌ای دیگر جستجو می‌شود…', 'منتظر می‌مانیم تا تایپ شما تمام شود.', keys));
            bar.classList.remove('is-run'); void bar.offsetWidth; bar.classList.add('is-run');
        }

        function showSkeleton() {
            const wrap = el('div');
            for (let i = 0; i < 3; i++) {
                const row = el('div', 'ls-skel');
                row.appendChild(el('i', 'a'));
                const b = el('div', 'b'); b.appendChild(el('i')); b.appendChild(el('i')); row.appendChild(b);
                wrap.appendChild(row);
            }
            setBody(wrap);
        }

        function showEmpty(q) {
            const t = el('span'); t.append('برای ', el('q', null, q), ' نتیجه‌ای وجود ندارد. عبارت دیگری را امتحان کنید.');
            setBody(message(ICON_SEARCH, 'چیزی پیدا نشد', t));
        }

        function showError(q) {
            const btn = el('button', 'ls-retry', 'تلاش دوباره'); btn.type = 'button';
            btn.addEventListener('click', function () { run(q); });
            setBody(message(ICON_WARN, 'خطا در جستجو', 'ارتباط با سرور برقرار نشد.', btn));
        }

        function showResults(data, q) {
            const wrap = el('div'); wrap.setAttribute('role', 'listbox');
            const list = [];

            data.results.forEach(function (r) {
                const a = el('a', 'ls-item'); a.href = r.url; a.setAttribute('role', 'option');

                const th = el('span', 'ls-thumb');
                if (r.image) { const im = el('img'); im.src = r.image; im.alt = ''; im.loading = 'lazy'; th.appendChild(im); }
                else th.appendChild(svg(ICON_IMG));
                a.appendChild(th);

                const tx = el('span', 'ls-txt');
                const ttl = el('span', 'ls-ttl'); ttl.style.display = 'block'; ttl.appendChild(highlight(r.title, q)); tx.appendChild(ttl);
                if (r.excerpt) { const ex = el('span', 'ls-exc'); ex.style.display = 'block'; ex.textContent = r.excerpt; tx.appendChild(ex); }
                const meta = [r.category, r.date].filter(Boolean).join(' · ');
                if (meta) { const m = el('span', 'ls-meta', meta); m.style.display = 'block'; tx.appendChild(m); }
                a.appendChild(tx);

                a.appendChild(svg(ICON_ARROW, 'ls-go'));
                wrap.appendChild(a); list.push(a);
            });

            // «مشاهده‌ی همه‌ی نتایج» (همیشه وقتی نتیجه هست)
            const all = el('a', 'ls-all'); all.href = data.see_all || (ALL_URL + '?search=' + encodeURIComponent(q));
            all.append('مشاهده‌ی همه‌ی نتایج', el('span', 'ls-count', fa(data.total)), svg(ICON_ARROW));
            wrap.appendChild(all); list.push(all);

            setBody(wrap);
            items = list;
        }

        // ---------- جستجو ----------
        function cancel() {
            clearTimeout(timer); timer = null;
            if (ctrl) { ctrl.abort(); ctrl = null; }
            bar.classList.remove('is-run'); spin.hidden = true;
        }

        function render(data, q) {
            shownQuery = q;
            if (data.total > 0 && data.results.length) showResults(data, q); else showEmpty(q);
        }

        async function run(q) {
            cancel();
            if (cache.has(q)) { render(cache.get(q), q); return; }

            ctrl = new AbortController();
            const mine = ctrl;
            spin.hidden = false;
            showSkeleton();

            try {
                const res = await fetch(SEARCH_URL + '?q=' + encodeURIComponent(q), {
                    headers: {'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest'},
                    signal: mine.signal,
                });
                if (!res.ok) throw new Error('bad status');
                const data = await res.json();
                if (mine !== ctrl) return;                       // جواب کهنه (کاربر در این فاصله چیز دیگری نوشته)
                cache.set(q, data);
                render(data, q);
            } catch (e) {
                if (e.name === 'AbortError') return;
                if (mine === ctrl) showError(q);
            } finally {
                if (mine === ctrl) { spin.hidden = true; ctrl = null; }
            }
        }

        input.addEventListener('input', function () {
            const q = norm(input.value);
            clearBtn.hidden = input.value === '';
            cancel(); shownQuery = '';

            if (len(q) < MIN_CHARS) { showIdle(len(q)); return; }     // کمتر از ۲ حرف: هیچ درخواستی ارسال نمی‌شود
            showWait();
            timer = setTimeout(function () { run(q); }, WAIT_MS);
        });

        // Enter: روی گزینه‌ی انتخاب‌شده برو | نتیجه آماده است → همه‌ی نتایج | وگرنه فوراً جستجو کن
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            const q = norm(input.value);
            if (active >= 0 && items[active]) { window.location.href = items[active].href; return; }
            if (len(q) < MIN_CHARS) return;
            if (shownQuery === q) { window.location.href = ALL_URL + '?search=' + encodeURIComponent(q); return; }
            run(q);
        });

        clearBtn.addEventListener('click', function () { input.value = ''; input.dispatchEvent(new Event('input')); input.focus(); });

        // ---------- باز و بسته ----------
        function open(from) {
            if (!root.hidden) return;
            lastFocus = from || document.activeElement;
            root.hidden = false;
            document.body.style.overflow = 'hidden';
            if (input.value === '') showIdle(0);
            input.focus();
            input.select();
        }
        function close() {
            if (root.hidden) return;
            cancel();
            root.hidden = true;
            document.body.style.overflow = '';
            if (lastFocus && lastFocus.focus) lastFocus.focus({preventScroll: true});
        }

        document.addEventListener('click', function (e) {
            const t = e.target.closest('[data-open-search]');
            if (t) { e.preventDefault(); open(t); return; }
            if (e.target.closest('#ls [data-ls-close]')) close();
        });

        document.addEventListener('keydown', function (e) {
            const tag = (e.target.tagName || '').toLowerCase();
            const typing = tag === 'input' || tag === 'textarea' || tag === 'select' || e.target.isContentEditable;

            if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'k') { e.preventDefault(); root.hidden ? open() : close(); return; }
            if (root.hidden) {
                if (e.key === '/' && !typing && !e.ctrlKey && !e.metaKey && !e.altKey) { e.preventDefault(); open(); }
                return;
            }

            if (e.key === 'Escape') { e.preventDefault(); close(); return; }

            if ((e.key === 'ArrowDown' || e.key === 'ArrowUp') && items.length) {
                e.preventDefault();
                active = e.key === 'ArrowDown' ? (active + 1) % items.length : (active <= 0 ? items.length - 1 : active - 1);
                items.forEach(function (n, i) { n.classList.toggle('is-active', i === active); n.setAttribute('aria-selected', i === active ? 'true' : 'false'); });
                items[active].scrollIntoView({block: 'nearest'});
                return;
            }

            // نگه داشتن Tab داخل پنجره
            if (e.key === 'Tab') {
                const f = Array.from(root.querySelectorAll('input, button, a[href]')).filter(function (n) { return !n.hidden && n.offsetParent !== null; });
                if (!f.length) return;
                const first = f[0], last = f[f.length - 1];
                if (e.shiftKey && document.activeElement === first) { e.preventDefault(); last.focus(); }
                else if (!e.shiftKey && document.activeElement === last) { e.preventDefault(); first.focus(); }
            }
        });

        // اگر ماوس روی نتیجه‌ای رفت، انتخاب صفحه‌کلید پاک شود
        body.addEventListener('mousemove', function () {
            if (active >= 0) { items.forEach(function (n) { n.classList.remove('is-active'); }); active = -1; }
        });
    })();
