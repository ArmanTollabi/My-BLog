// دکمه‌های لایک و بوکمارک (صفحه‌ی پست و کارت‌های لیست). یک کد برای هر دو:
//   <button data-toggle-btn data-kind="like|save" data-url="..." data-auth="1|0">
//   [data-like-count] داخل دکمه = عدد لایک؛ [data-toggle-error] هرجای صفحه = جای نمایش خطا (اختیاری)
import { csrfToken } from './lib/csrf';

function restartPop(btn, on) {
    btn.classList.remove('pop');
    void btn.offsetWidth;              // ری‌استارت انیمیشن
    if (on) btn.classList.add('pop');
}

function showError(btn, message) {
    const el = document.querySelector('[data-toggle-error]');
    if (el) el.textContent = message; else btn.title = message;
}

const APPLY = {
    like(btn, data) {
        btn.classList.toggle('is-liked', data.liked);
        btn.setAttribute('aria-pressed', data.liked ? 'true' : 'false');
        const count = btn.querySelector('[data-like-count]');
        if (count) count.textContent = Number(data.count).toLocaleString('en-US');
        restartPop(btn, data.liked);
    },
    save(btn, data) {
        btn.classList.toggle('is-saved', data.saved);
        btn.setAttribute('aria-pressed', data.saved ? 'true' : 'false');
        if (btn.hasAttribute('title')) btn.title = data.saved ? 'برداشتن از بوکمارک‌ها' : 'ذخیره در بوکمارک‌ها';
        restartPop(btn, data.saved);
    },
};

document.querySelectorAll('[data-toggle-btn]').forEach(function (btn) {
    const kind = btn.dataset.kind;
    let busy = false;

    btn.addEventListener('click', async function () {
        const askLogin = function () {
            btn.dataset.auth = '0';
            document.dispatchEvent(new CustomEvent('open-login-modal', {detail: {kind}}));
        };

        if (btn.dataset.auth !== '1') { askLogin(); return; }      // مهمان
        if (busy) return;

        busy = true;
        btn.disabled = true;
        showError(btn, '');

        try {
            const res = await fetch(btn.dataset.url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken(),
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });

            if (res.status === 401) { askLogin(); return; }        // نشست تمام شده
            if (res.status === 419) { showError(btn, 'نشست شما منقضی شده است؛ صفحه را رفرش کن.'); return; }
            if (!res.ok) throw new Error('bad status');

            APPLY[kind]?.(btn, await res.json());
        } catch (e) {
            showError(btn, 'خطا در انجام عملیات، دوباره تلاش کن.');
        } finally {
            busy = false;
            btn.disabled = false;
        }
    });
});
