// حذف خط‌های خالی ادیتور (همان <p><br></p>ها که با Enter اضافه پیدا می‌شوند).
// فاصله‌ی بین بلوک‌ها در صفحه‌ی پست از margin می‌آید؛ پس خط خالی فقط فاصله‌ی اضافه می‌سازد.
// آخرین خط سند (که Quill همیشه نگه می‌دارد) دست‌نخورده می‌ماند.
export function stripEmptyLines(quill) {
    const lines = quill.getLines();
    for (let i = lines.length - 2; i >= 0; i--) {
        if (lines[i].length() === 1) quill.deleteText(quill.getIndex(lines[i]), 1, 'silent');
    }
}
