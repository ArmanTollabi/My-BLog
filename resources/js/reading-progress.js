// نوار پیشرفت مطالعه‌ی بالای صفحه‌ی پست: <div data-reading-progress><span></span></div> + ناحیه‌ی متن [data-reading-body]
const bar = document.querySelector('[data-reading-progress] > span');
const body = document.querySelector('[data-reading-body]');

if (bar && body) {
    let ticking = false;

    function update() {
        const rect = body.getBoundingClientRect();
        const total = rect.height - window.innerHeight * 0.5;          // تا وقتی نیمه‌ی پایین متن به وسط صفحه برسد
        const done = Math.min(Math.max(-rect.top + window.innerHeight * 0.3, 0), Math.max(total, 1));
        bar.style.transform = 'scaleX(' + (done / Math.max(total, 1)).toFixed(4) + ')';
        ticking = false;
    }

    window.addEventListener('scroll', function () {
        if (!ticking) { ticking = true; requestAnimationFrame(update); }
    }, {passive: true});
    window.addEventListener('resize', update);
    update();
}
