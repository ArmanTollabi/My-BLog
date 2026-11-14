// فرم ایجاد/ویرایش پست: ادیتور Quill + آپلود تصویر شاخص + انتخاب تگ.
// فقط صفحه‌ی فرم پست آن را لود می‌کند (posts/_form با @vite)، پس Quill در باندل بقیه‌ی سایت نیست.
// ورودی‌ها از data-attribute می‌آیند:  #rte[data-upload-url]  و  #upload-box[data-upload-url]
import Quill from 'quill';
import 'quill/dist/quill.snow.css';
import { csrfToken } from './lib/csrf';
import { initImageAlt } from './editor-image-alt';
import './post-status';

// =====================================================================  ادیتور متن
(function () {
    const rte = document.getElementById('rte');
    const contentInput = document.getElementById('content');
    if (!rte || !contentInput) return;

    const uploadUrl = rte.dataset.uploadUrl;
    const statusEl = document.getElementById('rte-status');
    let pending = 0;

    function setStatus(msg, isError) {
        statusEl.textContent = msg || '';
        statusEl.classList.toggle('is-error', !!isError);
    }

    const quill = new Quill('#editor', {
        theme: 'snow',
        placeholder: 'متن پست را اینجا بنویس…',
        modules: {
            toolbar: {
                container: [
                    [{header: [false, 1, 2, 3]}],
                    ['bold', 'italic', 'underline', 'strike'],
                    [{list: 'ordered'}, {list: 'bullet'}],
                    [{align: ''}, {align: 'center'}, {align: 'right'}, {align: 'justify'}],
                    ['blockquote', 'link', 'image'],
                    ['clean'],
                ],
                handlers: {image: pickImage},
            },
            history: {delay: 800, maxStack: 200, userOnly: true},
        },
    });

    // کلیک روی تصویر ← ویرایش alt
    const altEditor = initImageAlt(quill, Quill, rte);

    // جهت هر بلوک خودکار (فارسی راست‌چین، انگلیسی چپ‌چین)
    function autoDir() {
        quill.root.querySelectorAll('p,h1,h2,h3,li,blockquote').forEach(el => el.setAttribute('dir', 'auto'));
    }
    quill.on('text-change', autoDir);
    autoDir();

    async function upload(file) {
        if (!file || !/^image\/(jpeg|png|webp|jpg)$/.test(file.type)) {
            setStatus('فقط عکس JPG، PNG یا WebP مجاز است.', true);
            return;
        }
        const range = quill.getSelection(true);
        pending++;
        rte.classList.add('is-uploading');
        setStatus('در حال آپلود عکس…');

        try {
            const fd = new FormData();
            fd.append('image', file);
            fd.append('_token', csrfToken());
            const res = await fetch(uploadUrl, {method: 'POST', body: fd, headers: {'Accept': 'application/json'}});
            const data = await res.json().catch(() => ({}));
            if (!res.ok || !data.url) {
                throw new Error(data.errors?.image?.[0] || data.message || 'آپلود ناموفق بود.');
            }
            quill.insertEmbed(range.index, 'image', data.url, 'user');
            quill.setSelection(range.index + 1, 0, 'silent');
            setStatus('');

            // بلافاصله alt تصویر تازه پرسیده شود
            const [leaf] = quill.getLeaf(range.index);
            if (leaf && leaf.domNode instanceof HTMLImageElement) altEditor.open(leaf.domNode);
        } catch (e) {
            setStatus(e.message, true);
        } finally {
            pending--;
            if (!pending) rte.classList.remove('is-uploading');
        }
    }

    function pickImage() {
        const input = document.createElement('input');
        input.type = 'file';
        input.accept = 'image/jpeg,image/png,image/webp';
        input.onchange = () => upload(input.files[0]);
        input.click();
    }

    // paste / drop عکس
    quill.root.addEventListener('paste', function (e) {
        const files = [...(e.clipboardData?.files || [])].filter(f => f.type.startsWith('image/'));
        if (files.length) { e.preventDefault(); files.forEach(upload); }
    });
    quill.root.addEventListener('drop', function (e) {
        const files = [...(e.dataTransfer?.files || [])].filter(f => f.type.startsWith('image/'));
        if (files.length) { e.preventDefault(); files.forEach(upload); }
    });
    // عکس base64 داخل HTML پیست‌شده حذف شود (سرور فقط عکس‌های آپلودشده را می‌پذیرد)
    quill.clipboard.addMatcher('IMG', function (node, delta) {
        return /^data:|^blob:/.test(node.getAttribute('src') || '') ? new (Quill.import('delta'))() : delta;
    });

    // ارسال فرم
    contentInput.closest('form').addEventListener('submit', function (e) {
        if (pending) {
            e.preventDefault();
            setStatus('صبر کن تا آپلود عکس تمام شود…', true);
            return;
        }
        const empty = quill.getText().trim() === '' && !quill.root.querySelector('img');
        if (empty) {
            e.preventDefault();
            setStatus('متن پست نمی‌تواند خالی باشد.', true);
            quill.focus();
            return;
        }
        contentInput.value = quill.root.innerHTML;
    });
})();

// =====================================================================  آپلود تصویر شاخص (با نوار پیشرفت)
(function () {
    const box = document.getElementById('upload-box');
    const input = document.getElementById('image-input');
    if (!box || !input) return;

    const placeholder = document.getElementById('upload-placeholder');
    const preview = document.getElementById('image-preview');
    const wrapper = document.getElementById('progress-wrapper');
    const bar = document.getElementById('progress-bar');
    const text = document.getElementById('progress-text');
    const success = document.getElementById('upload-success');
    const pathInput = document.getElementById('image_path');

    function fail(msg) {
        text.textContent = msg;
        text.classList.add('text-rose-300');
        text.classList.remove('text-white/60');
    }

    input.addEventListener('change', function () {
        const file = this.files[0];
        if (!file) return;

        // پیش‌نمایش فوری
        const reader = new FileReader();
        reader.onload = function (e) {
            preview.src = e.target.result;
            preview.classList.remove('hidden');
            placeholder.classList.add('hidden');
        };
        reader.readAsDataURL(file);

        // ریست وضعیت قبلی
        success.classList.add('hidden');
        box.classList.remove('border-emerald-400');
        box.classList.add('border-white/25');
        wrapper.classList.remove('hidden');
        bar.style.width = '0%';
        text.textContent = '0%';
        text.classList.remove('text-rose-300');
        text.classList.add('text-white/60');

        const formData = new FormData();
        formData.append('image', file);
        formData.append('purpose', 'cover');          // سرور برای عکس شاخص thumbnail هم می‌سازد
        formData.append('_token', csrfToken());

        // XMLHttpRequest چون درصد پیشرفت آپلود را گزارش می‌دهد
        const xhr = new XMLHttpRequest();
        xhr.open('POST', box.dataset.uploadUrl, true);
        xhr.setRequestHeader('Accept', 'application/json');

        xhr.upload.addEventListener('progress', function (e) {
            if (!e.lengthComputable) return;
            const percent = Math.round((e.loaded / e.total) * 100);
            bar.style.width = percent + '%';
            text.textContent = percent + '%';
        });

        xhr.onload = function () {
            if (xhr.status === 200) {
                pathInput.value = JSON.parse(xhr.responseText).path;
                wrapper.classList.add('hidden');
                success.classList.remove('hidden');
                box.classList.remove('border-white/25');
                box.classList.add('border-emerald-400');
                return;
            }
            let msg = 'خطا در آپلود، دوباره تلاش کنید.';
            try { msg = JSON.parse(xhr.responseText).errors.image[0] || msg; } catch (e) {}
            fail(msg);
        };
        xhr.onerror = function () { fail('خطا در اتصال به سرور.'); };

        xhr.send(formData);
    });
})();

// =====================================================================  انتخاب تگ‌ها
(function () {
    const root = document.getElementById('tag-select');
    if (!root) return;

    const button = document.getElementById('tag-select-button');
    const dropdown = document.getElementById('tag-dropdown');
    const search = document.getElementById('tag-search');
    const selected = document.getElementById('selected-tags');
    const EMPTY = '<span class="text-white/40">انتخاب تگ‌ها…</span>';

    function setOpen(open) {
        dropdown.classList.toggle('hidden', !open);
        button.setAttribute('aria-expanded', open ? 'true' : 'false');
    }

    function render() {
        const checked = root.querySelectorAll('.tag-checkbox:checked');
        selected.innerHTML = '';
        if (!checked.length) { selected.innerHTML = EMPTY; return; }

        checked.forEach(function (cb) {
            const chip = document.createElement('span');
            chip.className = 'rounded-lg border border-violet-300/30 bg-violet-500/25 px-2.5 py-1 text-sm text-violet-100';
            chip.textContent = cb.closest('.tag-option').querySelector('span').textContent.trim();
            selected.appendChild(chip);
        });
    }

    button.addEventListener('click', function () { setOpen(dropdown.classList.contains('hidden')); });
    root.querySelectorAll('.tag-checkbox').forEach(function (cb) { cb.addEventListener('change', render); });

    search.addEventListener('input', function () {
        const q = this.value.toLowerCase();
        root.querySelectorAll('.tag-option').forEach(function (o) {
            o.classList.toggle('hidden', !o.dataset.name.includes(q));
        });
    });

    document.addEventListener('click', function (e) { if (!root.contains(e.target)) setOpen(false); });
    document.addEventListener('keydown', function (e) { if (e.key === 'Escape') setOpen(false); });

    render();
})();
