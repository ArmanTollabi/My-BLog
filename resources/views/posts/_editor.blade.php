{{--
    ادیتور متن پست (Quill). در create و edit با:
    @include('posts._editor', ['initial' => $html])
    $initial باید HTML پاک‌سازی‌شده باشد.
--}}
<div class="mb-8">
    <label class="block text-sm font-semibold text-gray-700 mb-2">متن پست</label>

    <div class="rte" id="rte">
        <div id="editor">{!! $initial !!}</div>
    </div>
    <p id="rte-status" class="rte-status" role="status"></p>

    <input type="hidden" name="content" id="content">

    @error('content')
    <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
    @enderror
</div>

@push('scripts')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.snow.css">
    <script src="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.min.js"></script>
    <script>
        (function () {
            const uploadUrl = @json(route('posts.upload-image'));
            const csrf = @json(csrf_token());
            const statusEl = document.getElementById('rte-status');
            const rte = document.getElementById('rte');
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

            // جهت هر بلوک خودکار (فارسی راست‌چین، انگلیسی چپ‌چین)
            function autoDir() {
                quill.root.querySelectorAll('p,h1,h2,h3,li,blockquote').forEach(el => el.setAttribute('dir', 'auto'));
            }
            quill.on('text-change', autoDir);
            autoDir();

            // ---------- آپلود عکس ----------
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
                    fd.append('_token', csrf);
                    const res = await fetch(uploadUrl, {method: 'POST', body: fd, headers: {'Accept': 'application/json'}});
                    const data = await res.json().catch(() => ({}));
                    if (!res.ok || !data.url) {
                        throw new Error(data.errors?.image?.[0] || data.message || 'آپلود ناموفق بود.');
                    }
                    quill.insertEmbed(range.index, 'image', data.url, 'user');
                    quill.setSelection(range.index + 1, 0, 'silent');
                    setStatus('');
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

            // ---------- ارسال فرم ----------
            const form = document.getElementById('content').closest('form');
            form.addEventListener('submit', function (e) {
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
                document.getElementById('content').value = quill.root.innerHTML;
            });
        })();
    </script>
@endpush
