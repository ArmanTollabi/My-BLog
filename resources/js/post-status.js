// وضعیت انتشار در فرم پست: نمایش توضیح هر وضعیت + نمایش تقویم جلالی فقط برای «زمان‌بندی‌شده».
// مارکاپ: posts/_form.blade.php ([data-post-status]). از post-form.js ایمپورت می‌شود.
import '@majidh1/jalalidatepicker/dist/jalalidatepicker.min.css';
import '@majidh1/jalalidatepicker';
import '../css/jdp-dark.css';

const root = document.querySelector('[data-post-status]');

if (root) {
    const select = root.querySelector('[data-status-select]');
    const hint = root.querySelector('[data-status-hint]');
    const box = root.querySelector('[data-schedule-box]');
    const input = box ? box.querySelector('input') : null;

    function sync() {
        const option = select.options[select.selectedIndex];
        hint.textContent = option ? option.dataset.hint || '' : '';

        const scheduled = select.value === 'scheduled';
        box.classList.toggle('hidden', !scheduled);
        input.required = scheduled;
        if (!scheduled && document.activeElement === input) input.blur();
    }

    select.addEventListener('change', sync);
    sync();

    // تقویم جلالی (فقط روی input[data-jdp] همین فرم)
    if (window.jalaliDatepicker) {
        window.jalaliDatepicker.startWatch({
            time: true,
            hasSecond: false,
            minDate: 'today',
            persianDigits: false,
            showTodayBtn: true,
            showEmptyBtn: false,
            showCloseBtn: true,
            hideAfterChange: false,
            autoShow: true,
            position: 'right',
            topSpace: 6,
            bottomSpace: 6,
            zIndex: 1000,
        });
    }
}
