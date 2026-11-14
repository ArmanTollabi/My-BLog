// آپلود عکس پروفایل با نوار پیشرفت (ایجاد/ویرایش کاربر توسط ادمین).
// مارکاپ: #upload-box[data-avatar-upload][data-upload-url] + ids: avatar-input, upload-placeholder, avatar-preview,
//          progress-wrapper, progress-bar, progress-text, upload-success, avatar_path
import { csrfToken } from './lib/csrf';

const uploadBox = document.querySelector('[data-avatar-upload]');

if (uploadBox) {
    const avatarInput = document.getElementById('avatar-input');
    const uploadPlaceholder = document.getElementById('upload-placeholder');
    const avatarPreview = document.getElementById('avatar-preview');
    const progressWrapper = document.getElementById('progress-wrapper');
    const progressBar = document.getElementById('progress-bar');
    const progressText = document.getElementById('progress-text');
    const uploadSuccess = document.getElementById('upload-success');
    const avatarPathInput = document.getElementById('avatar_path');

    avatarInput.addEventListener('change', function () {
        const file = this.files[0];
        if (!file) return;

        // پیش‌نمایش فوری (قبل از تمام شدن آپلود)
        const reader = new FileReader();
        reader.onload = function (e) {
            avatarPreview.src = e.target.result;
            avatarPreview.classList.remove('hidden');
            uploadPlaceholder?.classList.add('hidden');
        };
        reader.readAsDataURL(file);

        // ریست وضعیت قبلی
        uploadSuccess.classList.add('hidden');
        uploadBox.classList.remove('border-emerald-400');
        uploadBox.classList.add('border-white/30');
        progressWrapper.classList.remove('hidden');
        progressBar.style.width = '0%';
        progressText.textContent = '0%';
        progressText.classList.remove('text-rose-300');

        const formData = new FormData();
        formData.append('avatar', file);
        formData.append('_token', csrfToken());

        // XMLHttpRequest چون درصد پیشرفت را گزارش می‌دهد
        const xhr = new XMLHttpRequest();
        xhr.open('POST', uploadBox.dataset.uploadUrl, true);
        xhr.setRequestHeader('Accept', 'application/json');
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

        xhr.upload.addEventListener('progress', function (e) {
            if (!e.lengthComputable) return;
            const percent = Math.round((e.loaded / e.total) * 100);
            progressBar.style.width = percent + '%';
            progressText.textContent = percent + '%';
        });

        xhr.onload = function () {
            if (xhr.status === 200) {
                // مسیر عکس در input مخفی ذخیره می‌شود تا با فرم اصلی ارسال شود
                avatarPathInput.value = JSON.parse(xhr.responseText).path;
                progressWrapper.classList.add('hidden');
                uploadSuccess.classList.remove('hidden');
                uploadBox.classList.remove('border-white/30');
                uploadBox.classList.add('border-emerald-400');
            } else {
                progressText.textContent = 'خطا در آپلود، دوباره تلاش کن.';
                progressText.classList.add('text-rose-300');
            }
        };
        xhr.onerror = function () {
            progressText.textContent = 'خطا در اتصال به سرور.';
            progressText.classList.add('text-rose-300');
        };

        xhr.send(formData);
    });
}
