// صفحه‌های ورود / ثبت‌نام / فراموشی و بازیابی رمز. همه‌چیز با data-attribute کار می‌کند، پس برای هر صفحه کد جدا لازم نیست:
//   [data-toggle-password="idِ ورودی"]  → دکمه‌ی نمایش/مخفی کردن رمز (داخلش [data-eye-open] و [data-eye-closed])
//   form[data-submit-lock]              → جلوگیری از ارسال دوباره؛ داخلش [data-submit-btn] [data-spinner] [data-label]
//                                          و data-loading-text روی فرم = متن حالت «در حال ارسال»
//   [data-password] / [data-password-confirm] / [data-strength-bars] / [data-strength-label] / [data-match-message]

// ---------- نمایش / مخفی کردن رمز ----------
document.querySelectorAll('[data-toggle-password]').forEach(function (toggle) {
    const input = document.getElementById(toggle.dataset.togglePassword);
    const open = toggle.querySelector('[data-eye-open]');
    const closed = toggle.querySelector('[data-eye-closed]');
    if (!input) return;

    toggle.addEventListener('click', function () {
        const show = input.type === 'password';
        input.type = show ? 'text' : 'password';
        open?.classList.toggle('hidden', show);
        closed?.classList.toggle('hidden', !show);
        toggle.setAttribute('aria-pressed', show ? 'true' : 'false');
        toggle.setAttribute('aria-label', show ? 'مخفی کردن رمز عبور' : 'نمایش رمز عبور');
        input.focus();
    });
});

// ---------- جلوگیری از ارسال دوباره ----------
document.querySelectorAll('form[data-submit-lock]').forEach(function (form) {
    const btn = form.querySelector('[data-submit-btn]');
    const spinner = form.querySelector('[data-spinner]');
    const label = form.querySelector('[data-label]');
    if (!btn || !label) return;

    const idleText = label.textContent.trim();

    form.addEventListener('submit', function () {
        btn.disabled = true;
        spinner?.classList.remove('hidden');
        label.textContent = form.dataset.loadingText || '…';
    });

    // برگشت با دکمه‌ی back مرورگر: دکمه دوباره فعال شود
    window.addEventListener('pageshow', function () {
        btn.disabled = false;
        spinner?.classList.add('hidden');
        label.textContent = idleText;
    });
});

// ---------- قدرت رمز و تطابق تکرار آن ----------
(function () {
    const pwd = document.querySelector('[data-password]');
    const confirmInput = document.querySelector('[data-password-confirm]');
    const bars = document.querySelectorAll('[data-strength-bars] span');
    const strengthLabel = document.querySelector('[data-strength-label]');
    const matchMsg = document.querySelector('[data-match-message]');
    if (!pwd) return;

    const levels = [
        {text: 'ضعیف',  bar: 'bg-rose-400',    txt: 'text-rose-300'},
        {text: 'متوسط', bar: 'bg-amber-400',   txt: 'text-amber-300'},
        {text: 'خوب',   bar: 'bg-sky-400',     txt: 'text-sky-300'},
        {text: 'قوی',   bar: 'bg-emerald-400', txt: 'text-emerald-300'},
    ];
    const allBar = levels.map(l => l.bar).concat('bg-white/15');
    const allTxt = levels.map(l => l.txt);

    function score(v) {
        let s = 0;
        if (v.length >= 8) s++;
        if (/[a-z]/.test(v) && /[A-Z]/.test(v)) s++;
        if (/\d/.test(v)) s++;
        if (/[^A-Za-z0-9]/.test(v)) s++;
        if (v.length < 6) s = Math.min(s, 1);
        return Math.max(s, 1);
    }

    function updateStrength() {
        const s = pwd.value ? score(pwd.value) : 0;
        const level = levels[s - 1];

        bars.forEach(function (bar, i) {
            bar.classList.remove(...allBar);
            bar.classList.add(i < s ? level.bar : 'bg-white/15');
        });

        if (!strengthLabel) return;
        strengthLabel.classList.remove(...allTxt);
        strengthLabel.textContent = s ? '— ' + level.text : '';
        if (s) strengthLabel.classList.add(level.txt);
    }

    function updateMatch() {
        if (!matchMsg || !confirmInput) return;
        matchMsg.classList.remove('text-emerald-300', 'text-rose-300');
        if (!confirmInput.value) { matchMsg.textContent = ''; return; }

        const same = confirmInput.value === pwd.value;
        matchMsg.textContent = same ? '✓ رمزها یکسان هستند.' : 'رمزها یکسان نیستند.';
        matchMsg.classList.add(same ? 'text-emerald-300' : 'text-rose-300');
    }

    pwd.addEventListener('input', function () { updateStrength(); updateMatch(); });
    confirmInput?.addEventListener('input', updateMatch);
})();
