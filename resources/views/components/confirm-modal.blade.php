{{--
    پاپ‌آپ تایید شیشه‌ای؛ جایگزین confirm() خام مرورگر.

    نصب: این فایل را در resources/views/components/confirm-modal.blade.php بگذارید و
    یک‌بار، درست قبل از </body> در layout اصلی بنویسید:  <x-confirm-modal />

    بدون تغییر در فرم‌های فعلی کار می‌کند: هر فرمی که onsubmit="return confirm('...')" داشته باشد
    خودکار به پاپ‌آپ تبدیل می‌شود. برای کنترل بیشتر می‌توانید این attribute ها را روی <form> بگذارید:
      data-confirm="متن پیام"          (به‌جای onsubmit)
      data-confirm-title="عنوان"
      data-confirm-ok="متن دکمه تایید"
      data-confirm-cancel="متن دکمه انصراف"
      data-confirm-variant="danger | logout | default"
--}}
<dialog id="confirm-dialog" class="confirm-dialog" aria-labelledby="confirm-title" aria-describedby="confirm-text">
    <div class="confirm-box">

        <div id="confirm-icon" class="confirm-icon" aria-hidden="true"></div>

        <h2 id="confirm-title" class="confirm-title"></h2>
        <p id="confirm-text" class="confirm-text"></p>

        <div class="confirm-actions">
            <button type="button" id="confirm-cancel" class="btn-cancel" autofocus></button>
            <button type="button" id="confirm-ok" class="btn-create"></button>
        </div>

    </div>
</dialog>

<script>
    (function () {
        const dialog = document.getElementById('confirm-dialog');

        // اگر مرورگر <dialog> را پشتیبانی نکند، همان confirm() ساده‌ی قبلی کار می‌کند
        if (!dialog || typeof dialog.showModal !== 'function') return;

        const iconEl = document.getElementById('confirm-icon');
        const titleEl = document.getElementById('confirm-title');
        const textEl = document.getElementById('confirm-text');
        const okBtn = document.getElementById('confirm-ok');
        const cancelBtn = document.getElementById('confirm-cancel');

        const svg = function (d) {
            return '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.7" ' +
                'stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="' + d + '"/></svg>';
        };

        const ICONS = {
            danger: svg('m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0'),
            logout: svg('M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9'),
            default: svg('M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 5.25h.008v.008H12v-.008Z')
        };

        const LABELS = {
            danger: {
                title:  {fa: 'حذف شود؟',        en: 'Delete this item?'},
                ok:     {fa: 'بله، حذف کن',     en: 'Yes, delete'},
                cancel: {fa: 'انصراف',           en: 'Cancel'}
            },
            logout: {
                title:  {fa: 'خروج از حساب',    en: 'Log out?'},
                ok:     {fa: 'خروج',             en: 'Log out'},
                cancel: {fa: 'ماندن',            en: 'Stay'}
            },
            default: {
                title:  {fa: 'تایید عملیات',    en: 'Are you sure?'},
                ok:     {fa: 'تایید',            en: 'Confirm'},
                cancel: {fa: 'انصراف',           en: 'Cancel'}
            }
        };

        let pending = null;

        // پیام را از data-confirm یا از onsubmit="return confirm('...')" می‌خواند
        function getMessage(form) {
            if (form.dataset.confirm) return form.dataset.confirm;

            const attr = form.getAttribute('onsubmit');
            if (!attr || attr.indexOf('confirm(') === -1) return null;

            const m = attr.match(/confirm\(\s*(['"`])([\s\S]*?)\1\s*\)/);
            if (!m) return null;

            form.dataset.confirm = m[2];
            form.removeAttribute('onsubmit'); // تا confirm خام اجرا نشود
            return m[2];
        }

        function getVariant(form) {
            if (form.dataset.confirmVariant) return form.dataset.confirmVariant;

            const method = form.querySelector('input[name="_method"]');
            if (method && method.value.toUpperCase() === 'DELETE') return 'danger';
            if (/logout/i.test(form.getAttribute('action') || '')) return 'logout';
            return 'default';
        }

        function open(form, message, submitter) {
            const fa = /[؀-ۿ]/.test(message);
            const lang = fa ? 'fa' : 'en';
            const variant = getVariant(form);
            const t = LABELS[variant] || LABELS.default;

            dialog.dataset.variant = variant === 'danger' ? 'danger' : 'default';
            dialog.setAttribute('dir', fa ? 'rtl' : 'ltr');

            iconEl.innerHTML = ICONS[variant] || ICONS.default;
            titleEl.textContent = form.dataset.confirmTitle || t.title[lang];
            textEl.textContent = message;
            okBtn.textContent = form.dataset.confirmOk || t.ok[lang];
            cancelBtn.textContent = form.dataset.confirmCancel || t.cancel[lang];
            okBtn.className = variant === 'danger' ? 'btn-delete is-solid' : 'btn-create';

            pending = {form: form, submitter: submitter || null};
            dialog.showModal();
            cancelBtn.focus(); // فوکوس روی انصراف تا Enter تصادفی چیزی را حذف نکند
        }

        // در فاز capture تا قبل از handler های inline اجرا شود
        document.addEventListener('submit', function (e) {
            const form = e.target;
            if (!(form instanceof HTMLFormElement)) return;

            const message = getMessage(form);
            if (!message || form.dataset.confirmed === '1') return;

            e.preventDefault();
            e.stopImmediatePropagation();
            open(form, message, e.submitter);
        }, true);

        okBtn.addEventListener('click', function () {
            const p = pending;
            pending = null;
            dialog.close();
            if (!p) return;

            p.form.dataset.confirmed = '1';
            try {
                if (typeof p.form.requestSubmit === 'function') {
                    p.form.requestSubmit(p.submitter || undefined);
                } else {
                    p.form.submit();
                }
            } catch (err) {
                p.form.submit();
            }
            delete p.form.dataset.confirmed;
        });

        cancelBtn.addEventListener('click', function () {
            dialog.close();
        });

        // کلیک روی پس‌زمینه‌ی تار = انصراف
        dialog.addEventListener('click', function (e) {
            if (e.target === dialog) dialog.close();
        });

        // Esc یا هر بسته‌شدنی = انصراف
        dialog.addEventListener('close', function () {
            pending = null;
        });
    })();
</script>
