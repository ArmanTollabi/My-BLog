// ویرایش «متن جایگزین» (alt) تصاویر داخل ادیتور Quill.
// روی هر تصویر کلیک کن ← یک پاپ‌آپ کوچک کنارش باز می‌شود. بعد از آپلود هم برای تصویر تازه خودکار باز می‌شود.
// alt خالی = تصویر تزئینی (alt="" ذخیره می‌شود)؛ تصویر بدون alt با کادر زرد خط‌چین در ادیتور مشخص است.
import '../css/editor-alt.css';

export function initImageAlt(quill, Quill, rte) {
    const root = quill.root;

    // راهنمای زیر ادیتور
    const hint = document.createElement('p');
    hint.className = 'rte-hint';
    hint.textContent = 'برای نوشتن متن جایگزین (alt) یا حذف یک تصویر، روی آن کلیک کنید.';
    rte.after(hint);

    // پاپ‌آپ
    const pop = document.createElement('div');
    pop.className = 'alt-pop';
    pop.hidden = true;
    pop.setAttribute('role', 'dialog');
    pop.setAttribute('aria-label', 'متن جایگزین تصویر');
    pop.innerHTML =
        '<label class="alt-pop__label" for="alt-pop-input">متن جایگزین تصویر (Alt)</label>' +
        '<input id="alt-pop-input" class="alt-pop__input" type="text" maxlength="200" autocomplete="off" ' +
        'placeholder="توضیح کوتاه تصویر، مثلاً: نمایی از دماوند در غروب">' +
        '<p class="alt-pop__hint">برای نابینایان و موتورهای جستجو. اگر تصویر فقط تزئینی است، خالی بگذارید.</p>' +
        '<div class="alt-pop__actions">' +
        '<button type="button" class="alt-pop__btn alt-pop__btn--primary" data-alt-save>ثبت</button>' +
        '<button type="button" class="alt-pop__btn" data-alt-close>بستن</button>' +
        '<button type="button" class="alt-pop__btn alt-pop__btn--danger" data-alt-remove>حذف تصویر</button>' +
        '</div>';
    document.body.appendChild(pop);

    const input = pop.querySelector('.alt-pop__input');
    let current = null;

    function place(img) {
        const r = img.getBoundingClientRect();
        const w = pop.offsetWidth || 352;
        const left = Math.min(Math.max(r.left + window.scrollX, 8), window.scrollX + document.documentElement.clientWidth - w - 8);
        pop.style.left = left + 'px';
        pop.style.top = (r.bottom + window.scrollY + 8) + 'px';
    }

    function open(img) {
        current = img;
        input.value = img.getAttribute('alt') || '';
        pop.hidden = false;
        place(img);
        input.focus({preventScroll: true});
        input.select();
    }

    function close() {
        pop.hidden = true;
        current = null;
    }

    function save() {
        if (current) current.setAttribute('alt', input.value.trim());   // خالی = تزئینی
        close();
    }

    function removeImage() {
        const blot = current && Quill.find(current);
        if (blot) quill.deleteText(quill.getIndex(blot), 1, 'user');
        close();
    }

    pop.addEventListener('click', function (e) {
        if (e.target.closest('[data-alt-save]')) save();
        else if (e.target.closest('[data-alt-close]')) close();
        else if (e.target.closest('[data-alt-remove]')) removeImage();
    });

    input.addEventListener('keydown', function (e) {
        if (e.key === 'Enter') { e.preventDefault(); save(); }
        if (e.key === 'Escape') { e.preventDefault(); close(); }
    });

    root.addEventListener('click', function (e) {
        if (e.target instanceof HTMLImageElement) open(e.target);
        else if (!pop.hidden) close();
    });

    // کلیک بیرون از پاپ‌آپ و ادیتور
    document.addEventListener('mousedown', function (e) {
        if (!pop.hidden && !pop.contains(e.target) && !(e.target instanceof HTMLImageElement && root.contains(e.target))) close();
    });
    document.addEventListener('keydown', function (e) { if (e.key === 'Escape' && !pop.hidden) close(); });

    // اسکرول داخلی ادیتور / تغییر اندازه ← موقعیت عکس عوض می‌شود
    root.addEventListener('scroll', close, {passive: true});
    window.addEventListener('resize', function () { if (current) place(current); });

    return {open};
}
