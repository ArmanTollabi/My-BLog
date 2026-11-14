// پاپ‌آپ «برای این کار باید وارد شوید» (فقط مهمان‌ها؛ partial: posts/_login-modal).
// باز کردن از هر جای صفحه:
//   document.dispatchEvent(new CustomEvent('open-login-modal', {detail: {kind: 'like' | 'save'}}))
const modal = document.getElementById('login-modal');

if (modal) {
    const TEXTS = {
        like: {
            title: 'این پست را دوست داشتید؟',
            text: 'برای لایک کردن باید وارد حساب کاربری‌تان شوید یا ثبت‌نام کنید. فقط چند ثانیه طول می‌کشد!',
        },
        save: {
            title: 'پست را برای بعد نگه دارید',
            text: 'برای ذخیره‌ی پست در بوکمارک‌ها باید وارد حساب کاربری‌تان شوید یا ثبت‌نام کنید. فقط چند ثانیه طول می‌کشد!',
        },
    };
    let lastFocus = null;

    function open(kind) {
        kind = TEXTS[kind] ? kind : 'save';
        modal.dataset.kind = kind;
        document.getElementById('login-modal-title').textContent = TEXTS[kind].title;
        document.getElementById('login-modal-text').textContent = TEXTS[kind].text;
        modal.querySelectorAll('[data-kind-icon]').forEach(function (icon) {
            icon.hidden = icon.dataset.kindIcon !== kind;
        });

        lastFocus = document.activeElement;
        modal.hidden = false;
        document.body.style.overflow = 'hidden';
        modal.querySelector('a.btn-create')?.focus();
    }

    function close() {
        if (modal.hidden) return;
        modal.hidden = true;
        document.body.style.overflow = '';
        lastFocus?.focus();
    }

    document.addEventListener('open-login-modal', function (e) { open(e.detail?.kind); });
    modal.addEventListener('click', function (e) { if (e.target.closest('[data-close]')) close(); });
    document.addEventListener('keydown', function (e) { if (e.key === 'Escape') close(); });
}
