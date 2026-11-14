// داشبورد کاربر: تغییر/حذف عکس پروفایل با AJAX (فرم #avatar-form در dashboard/index.blade.php)
    (function () {
        const form = document.getElementById('avatar-form');
        const input = document.getElementById('avatar-input');
        const box = document.getElementById('avatar-box');
        const actions = document.getElementById('avatar-actions');
        const resetBtn = document.getElementById('avatar-reset');
        const error = document.getElementById('avatar-client-error');
        const progress = document.getElementById('avatar-progress');
        const bar = document.getElementById('avatar-progress-bar');
        const barText = document.getElementById('avatar-progress-text');
        const status = document.getElementById('avatar-status');
        const deleteForm = document.getElementById('avatar-delete-form');

        if (!form || !input || !box) return;

        const submitBtn = form.querySelector('[type="submit"]');
        const submitLabel = submitBtn.textContent;
        const MAX_SIZE = 2 * 1024 * 1024;

        let original = box.innerHTML;   // آخرین عکسِ ذخیره‌شده (برای دکمه‌ی انصراف)
        let previewUrl = null;
        let busy = false;

        function clearPreviewUrl() {
            if (previewUrl) {
                URL.revokeObjectURL(previewUrl);
                previewUrl = null;
            }
        }

        function showStatus(type, message) {
            const el = document.createElement('div');
            el.className = 'alert alert-' + type + ' alert-auto';
            const body = document.createElement('div');
            body.className = 'alert-body';
            const text = document.createElement('span');
            text.className = 'alert-text';
            text.textContent = message;
            body.append(text);
            el.append(body);
            status.replaceChildren(el);
        }

        function setBusy(value) {
            busy = value;
            input.disabled = value;
            submitBtn.disabled = value;
            resetBtn.disabled = value;
            submitBtn.textContent = value ? 'در حال آپلود…' : submitLabel;
            progress.classList.toggle('hidden', !value);
            if (value) {
                bar.style.width = '0%';
                barText.textContent = '0%';
            }
        }

        // عکس هدر (دکمه‌ی پنل کاربری و منوی آن) هم بدون رفرش عوض می‌شود
        function updateHeaderAvatars(url) {
            document.querySelectorAll('.hdr-wrap .user-avatar, .pnl-user .user-avatar').forEach(function (el) {
                const img = document.createElement('img');
                img.src = url;
                img.alt = '';
                el.replaceChildren(img);
            });
        }

        // پیش‌نمایش عکس قبل از ذخیره
        input.addEventListener('change', function () {
            error.textContent = '';
            status.replaceChildren();
            const file = input.files[0];
            if (!file) return;

            if (!/^image\/(png|jpe?g|webp)$/.test(file.type)) {
                error.textContent = 'فقط تصویر JPG، PNG یا WebP مجاز است.';
                input.value = '';
                return;
            }
            if (file.size > MAX_SIZE) {
                error.textContent = 'حجم عکس باید کمتر از ۲ مگابایت باشد.';
                input.value = '';
                return;
            }

            clearPreviewUrl();
            previewUrl = URL.createObjectURL(file);

            const img = document.createElement('img');
            img.alt = 'پیش‌نمایش';
            img.src = previewUrl;
            box.replaceChildren(img);
            actions.classList.remove('hidden');
        });

        resetBtn.addEventListener('click', function () {
            input.value = '';
            clearPreviewUrl();
            box.innerHTML = original;
            actions.classList.add('hidden');
            error.textContent = '';
        });

        // آپلود با AJAX (بدون رفرش صفحه)
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            if (busy) return;

            if (!input.files[0]) {
                error.textContent = 'ابتدا یک عکس انتخاب کن.';
                return;
            }

            error.textContent = '';
            status.replaceChildren();

            // مهم: FormData باید قبل از setBusy ساخته شود، چون setBusy ورودی فایل را disabled می‌کند
            // و فیلد disabled داخل FormData نمی‌آید.
            const data = new FormData(form);   // شامل فایل و _token
            setBusy(true);

            const xhr = new XMLHttpRequest();
            xhr.open('POST', form.action, true);
            xhr.setRequestHeader('Accept', 'application/json');
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

            xhr.upload.addEventListener('progress', function (ev) {
                if (!ev.lengthComputable) return;
                const percent = Math.round((ev.loaded / ev.total) * 100);
                bar.style.width = percent + '%';
                barText.textContent = percent + '%';
            });

            xhr.onload = function () {
                setBusy(false);

                let res = {};
                try { res = JSON.parse(xhr.responseText); } catch (_) {}

                if (xhr.status >= 200 && xhr.status < 300 && res.url) {
                    const img = document.createElement('img');
                    img.alt = 'عکس پروفایل';
                    img.src = res.url;
                    box.replaceChildren(img);

                    original = box.innerHTML;
                    clearPreviewUrl();
                    input.value = '';
                    actions.classList.add('hidden');
                    deleteForm.classList.remove('hidden');
                    updateHeaderAvatars(res.url);
                    showStatus('success', res.message || 'عکس پروفایل به‌روز شد.');
                } else if (xhr.status === 422) {
                    error.textContent = (res.errors && res.errors.avatar && res.errors.avatar[0])
                        || res.message || 'عکس انتخاب‌شده معتبر نیست.';
                } else if (xhr.status === 419) {
                    error.textContent = 'نشست شما منقضی شده است؛ صفحه را رفرش کن.';
                } else {
                    error.textContent = 'خطا در آپلود، دوباره تلاش کن.';
                }
            };

            xhr.onerror = function () {
                setBusy(false);
                error.textContent = 'خطا در اتصال به سرور.';
            };

            xhr.send(data);
        });
    })();
