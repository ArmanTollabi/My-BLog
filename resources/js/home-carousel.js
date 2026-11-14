// کاروسل پست‌های صفحه‌ی خانه: دکمه‌های قبلی/بعدی + کشیدن با ماوس. ساختار: [data-carousel] > [data-track], [data-prev], [data-next]
document.querySelectorAll('[data-carousel]').forEach(function (box) {
    const track = box.querySelector('[data-track]');
    const prevButton = box.querySelector('[data-prev]');
    const nextButton = box.querySelector('[data-next]');
    if (!track) return;

    const step = function () { return Math.max(track.clientWidth * 0.8, 280); };

    // صفحه RTL است، پس scrollLeft منفی می‌شود؛ «قبلی» به راست می‌رود
    prevButton?.addEventListener('click', function () { track.scrollBy({left: step(), behavior: 'smooth'}); });
    nextButton?.addEventListener('click', function () { track.scrollBy({left: -step(), behavior: 'smooth'}); });

    function updateButtons() {
        if (!prevButton || !nextButton) return;
        const maxScroll = track.scrollWidth - track.clientWidth;
        const current = Math.abs(track.scrollLeft);
        prevButton.disabled = current <= 2;
        nextButton.disabled = current >= maxScroll - 2;
    }

    track.addEventListener('scroll', updateButtons, {passive: true});
    window.addEventListener('resize', updateButtons);
    updateButtons();

    // کشیدن با ماوس
    let isDragging = false, hasMoved = false, startX = 0, startScrollLeft = 0;

    track.addEventListener('mousedown', function (e) {
        if (e.button !== 0) return;
        isDragging = true;
        hasMoved = false;
        startX = e.clientX;
        startScrollLeft = track.scrollLeft;
    });

    window.addEventListener('mousemove', function (e) {
        if (!isDragging) return;
        const distance = e.clientX - startX;
        if (!hasMoved && Math.abs(distance) > 5) {
            hasMoved = true;
            track.classList.add('is-dragging');
        }
        if (hasMoved) track.scrollLeft = startScrollLeft - distance;
    });

    function stopDragging() {
        if (!isDragging) return;
        isDragging = false;
        track.classList.remove('is-dragging');

        if (hasMoved) {
            track.dataset.justDragged = 'true';
            setTimeout(function () { delete track.dataset.justDragged; }, 100);
        }
        hasMoved = false;
    }

    window.addEventListener('mouseup', stopDragging);
    window.addEventListener('blur', stopDragging);

    // جلوگیری از باز شدن پست در اثر کشیدن موس
    track.addEventListener('click', function (e) {
        if (track.dataset.justDragged === 'true') {
            e.preventDefault();
            e.stopPropagation();
        }
    }, true);
});
