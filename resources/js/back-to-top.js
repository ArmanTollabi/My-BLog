// دکمه‌ی بازگشت به بالا: <button data-back-to-top class="hidden ...">
const btn = document.querySelector('[data-back-to-top]');

if (btn) {
    window.addEventListener('scroll', function () {
        const show = window.scrollY > 950;
        btn.classList.toggle('hidden', !show);
        btn.classList.toggle('flex', show);
    }, {passive: true});

    btn.addEventListener('click', function () {
        window.scrollTo({top: 0, behavior: 'smooth'});
    });
}
