// پس‌زمینه‌ی تعاملی: لکه‌ها و ستاره‌ها با ماوس پارالکس می‌گیرند + نوری که دنبال ماوس می‌آید.
// این کد را در انتهای resources/js/app.js بگذار.
(() => {
    const fine = matchMedia('(hover: hover) and (pointer: fine)').matches;
    const reduce = matchMedia('(prefers-reduced-motion: reduce)').matches;
    if (!fine || reduce) return;

    const root = document.documentElement;
    const glow = document.createElement('div');
    glow.className = 'cursor-glow';
    glow.setAttribute('aria-hidden', 'true');
    document.body.prepend(glow);

    let tx = innerWidth / 2, ty = innerHeight / 2;   // هدف (موقعیت واقعی ماوس)
    let x = tx, y = ty;                              // مقدار نرم‌شده
    let raf = 0;

    function frame() {
        x += (tx - x) * 0.08;
        y += (ty - y) * 0.08;

        root.style.setProperty('--px', ((x / innerWidth) * 2 - 1).toFixed(3));   // -1 تا 1
        root.style.setProperty('--py', ((y / innerHeight) * 2 - 1).toFixed(3));
        glow.style.transform = `translate3d(${x - 300}px, ${y - 300}px, 0)`;

        raf = (Math.abs(tx - x) > 0.3 || Math.abs(ty - y) > 0.3) ? requestAnimationFrame(frame) : 0;
    }

    addEventListener('pointermove', (e) => {
        tx = e.clientX; ty = e.clientY;
        glow.style.opacity = '1';
        if (!raf) raf = requestAnimationFrame(frame);
    }, {passive: true});

    document.addEventListener('pointerleave', () => { glow.style.opacity = '0'; });
})();
