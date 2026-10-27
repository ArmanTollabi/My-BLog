@extends('layout')

@section('title', 'داشبورد')

@section('content')

    <div dir="rtl" class="max-w-6xl mx-auto px-6 py-10">

        {{-- عنوان --}}
        <div class="mb-8">
            <h1 class="text-3xl font-extrabold text-white">داشبورد من</h1>
            <p class="mt-1 text-white/60">
                سلام {{ $user->name }}! اینجا می‌توانید حساب کاربری‌تان را ببینید و مدیریت کنید.
            </p>
        </div>

        <div class="grid gap-6 lg:grid-cols-3">

            {{-- ===== ستون پروفایل ===== --}}
            <div class="glass rounded-3xl p-6 sm:p-8 text-center self-start">

                {{-- عکس پروفایل: کلیک روی آن = انتخاب عکس --}}
                <form id="avatar-form" action="{{ route('dashboard.avatar') }}" method="POST"
                      enctype="multipart/form-data">
                    @csrf

                    <label for="avatar-input"
                           class="group relative mx-auto block w-fit cursor-pointer rounded-full"
                           title="تغییر عکس پروفایل">

                        <span id="avatar-box" class="user-avatar is-xl">
                            @if($user->avatar)
                                <img src="{{ Storage::url($user->avatar) }}" alt="عکس پروفایل">
                            @else
                                {{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}
                            @endif
                        </span>

                        {{-- لایه‌ی دوربین هنگام hover --}}
                        <span class="absolute inset-0 grid place-items-center rounded-full bg-black/50 text-white
                                     opacity-0 transition group-hover:opacity-100 group-focus-within:opacity-100">
                            <svg class="w-7 h-7" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                 stroke-width="1.8" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M6.827 6.175A2.31 2.31 0 0 1 5.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 0 0-1.134-.175 2.31 2.31 0 0 1-1.64-1.055l-.822-1.316a2.192 2.192 0 0 0-1.736-1.039 48.774 48.774 0 0 0-5.232 0 2.192 2.192 0 0 0-1.736 1.039l-.821 1.316Z"/>
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M16.5 12.75a4.5 4.5 0 1 1-9 0 4.5 4.5 0 0 1 9 0Z"/>
                            </svg>
                        </span>
                    </label>

                    <input id="avatar-input" type="file" name="avatar"
                           accept="image/png,image/jpeg,image/webp" class="sr-only">

                    {{-- دکمه‌های ذخیره / انصراف (بعد از انتخاب عکس نمایش داده می‌شوند) --}}
                    <div id="avatar-actions" class="hidden mt-4 flex items-center justify-center gap-2">
                        <button type="submit" class="btn-update btn-sm">ذخیره عکس</button>
                        <button type="button" id="avatar-reset" class="btn-cancel btn-sm">انصراف</button>
                    </div>

                    {{-- نوار پیشرفت آپلود --}}
                    <div id="avatar-progress" class="hidden mt-4">
                        <div class="h-2 w-full overflow-hidden rounded-full bg-white/15">
                            <div id="avatar-progress-bar"
                                 class="h-2 rounded-full bg-gradient-to-r from-violet-500 to-cyan-400 transition-all"
                                 style="width: 0%"></div>
                        </div>
                        <p id="avatar-progress-text" class="mt-2 text-xs text-white/60">0%</p>
                    </div>

                    <p id="avatar-client-error" class="mt-3 text-xs text-rose-300 empty:hidden"></p>
                    @error('avatar')
                    <p class="mt-3 text-xs text-rose-300">{{ $message }}</p>
                    @enderror

                    <p class="mt-3 text-xs text-white/45">JPG، PNG یا WebP — حداکثر ۲ مگابایت</p>
                </form>

                {{-- پیام موفقیت آپلود (با JS پر می‌شود) --}}
                <div id="avatar-status" class="mt-3 empty:hidden"></div>

                <form id="avatar-delete-form" action="{{ route('dashboard.avatar.destroy') }}" method="POST"
                      class="mt-3 {{ $user->avatar ? '' : 'hidden' }}"
                      onsubmit="return confirm('مطمئنی می‌خوای عکس پروفایلت رو حذف کنی؟')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-delete btn-sm">حذف عکس</button>
                </form>

                {{-- مشخصات --}}
                <h2 class="mt-6 text-xl font-extrabold text-white">{{ $user->name }}</h2>
                <p dir="ltr" class="mt-1 text-sm text-white/60 break-all">{{ $user->email }}</p>

                <p class="mt-3 inline-block rounded-full border border-white/15 bg-white/10 px-3 py-1 text-xs text-white/65">
                    عضو از {{ $user->created_at?->format('Y/m/d') }}
                </p>

                {{-- آمار --}}
                <div class="mt-6 grid grid-cols-2 gap-3 border-t border-white/10 pt-6">
                    <div class="rounded-2xl border border-white/10 bg-white/5 px-3 py-3">
                        <b class="block text-2xl font-extrabold text-white">{{ $stats['posts'] }}</b>
                        <span class="text-xs text-white/55">پست</span>
                    </div>
                    <div class="rounded-2xl border border-white/10 bg-white/5 px-3 py-3">
                        <b class="block text-2xl font-extrabold text-white">{{ $stats['comments'] }}</b>
                        <span class="text-xs text-white/55">کامنت</span>
                    </div>
                </div>
            </div>


            {{-- ===== ستون فرم‌ها ===== --}}
            <div class="space-y-6 lg:col-span-2">

                {{-- اطلاعات حساب --}}
                <form action="{{ route('dashboard.profile') }}" method="POST"
                      class="glass rounded-3xl p-6 sm:p-8 space-y-5">
                    @csrf
                    @method('PUT')

                    <div>
                        <h2 class="text-xl font-bold text-white">اطلاعات حساب</h2>
                        <p class="mt-1 text-sm text-white/55">نام و ایمیل خود را ویرایش کنید.</p>
                    </div>

                    <div>
                        <label for="name" class="block text-sm font-bold mb-2 text-white">نام</label>
                        <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}"
                               required autocomplete="name"
                               class="glass-input @error('name') !border-rose-400/70 @enderror">
                        @error('name')
                        <p class="mt-2 text-xs text-rose-300">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- ایمیل فقط نمایشی است: name ندارد، پس اصلاً ارسال نمی‌شود --}}
                    <div>
                        <label for="email" class="block text-sm font-bold mb-2 text-white">ایمیل</label>
                        <input type="email" id="email" value="{{ $user->email }}"
                               readonly aria-readonly="true" tabindex="-1" dir="ltr"
                               class="glass-input text-left cursor-not-allowed opacity-70 select-all">
                        <p class="mt-2 text-xs text-white/45">ایمیل حساب قابل تغییر نیست.</p>
                    </div>

                    <button type="submit" class="btn-update">ذخیره تغییرات</button>
                </form>

                {{-- تغییر رمز عبور --}}
                <form action="{{ route('dashboard.password') }}" method="POST"
                      class="glass rounded-3xl p-6 sm:p-8 space-y-5">
                    @csrf
                    @method('PUT')

                    <div>
                        <h2 class="text-xl font-bold text-white">تغییر رمز عبور</h2>
                        <p class="mt-1 text-sm text-white/55">برای امنیت بیشتر، رمز قوی و منحصربه‌فرد انتخاب کنید.</p>
                    </div>

                    <div>
                        <label for="current_password" class="block text-sm font-bold mb-2 text-white">رمز فعلی</label>
                        <input type="password" id="current_password" name="current_password"
                               dir="ltr" required autocomplete="current-password"
                               class="glass-input text-left @error('current_password') !border-rose-400/70 @enderror">
                        @error('current_password')
                        <p class="mt-2 text-xs text-rose-300">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid gap-5 sm:grid-cols-2">
                        <div>
                            <label for="new_password" class="block text-sm font-bold mb-2 text-white">رمز جدید</label>
                            <input type="password" id="new_password" name="password"
                                   dir="ltr" required autocomplete="new-password"
                                   class="glass-input text-left @error('password') !border-rose-400/70 @enderror">
                            @error('password')
                            <p class="mt-2 text-xs text-rose-300">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="new_password_confirmation" class="block text-sm font-bold mb-2 text-white">
                                تکرار رمز جدید
                            </label>
                            <input type="password" id="new_password_confirmation" name="password_confirmation"
                                   dir="ltr" required autocomplete="new-password"
                                   class="glass-input text-left">
                        </div>
                    </div>

                    <button type="submit" class="btn-update">تغییر رمز عبور</button>
                </form>

                {{-- آخرین پست‌های من --}}
                <div class="glass rounded-3xl p-6 sm:p-8">
                    <div class="mb-4 flex items-center justify-between gap-3">
                        <h2 class="text-xl font-bold text-white">آخرین پست‌های من</h2>
                        <a href="{{ route('posts.index') }}" class="btn-glass btn-sm">همه پست‌ها</a>
                    </div>

                    @forelse($posts as $post)
                        <a href="{{ route('posts.show', $post) }}"
                           class="flex items-center justify-between gap-3 rounded-2xl border border-transparent px-4 py-3
                                  transition hover:border-white/15 hover:bg-white/10">
                            <span class="truncate font-semibold text-white">{{ $post->title }}</span>
                            <span class="shrink-0 text-xs text-white/50">{{ $post->created_at->format('Y/m/d') }}</span>
                        </a>
                    @empty
                        <p class="py-6 text-center text-white/55">هنوز پستی منتشر نکرده‌اید.</p>
                    @endforelse
                </div>
                {{-- آخرین کامنت های من --}}
                <div class="glass rounded-3xl p-6 sm:p-8">
                    <div class="mb-4 flex items-center justify-between gap-3">
                        <h2 class="text-xl font-bold text-white">آخرین کامنتهای من</h2>
                    </div>

                    @forelse($user->comments()->latest()->get() as $comment)
                        <a href="{{ route('posts.show', $comment->id) }}"
                           class="flex items-center justify-between gap-3 rounded-2xl border border-transparent px-4 py-3
                                  transition hover:border-white/15 hover:bg-white/10">
                            <span class="truncate font-semibold text-white">{{$comment->body}}</span>
                            <span class="truncate font-semibold text-white hover:">  در {{ $comment->post->title }}</span>
                            @if($comment->status =='approved')
                            <span class="shrink-0 text-xs text-white/50">{{ $comment->created_at->format('Y/m/d H:i') }}</span>
                            @endif
                            @if($comment->status == 'pending')
                                <span class="shrink-0 text-xs text-white/50">منتظر تایید کامنت توسط ادمین باشید</span>
                            @endif
                        </a>
                    @empty
                        <p class="py-6 text-center text-white/55">هنوز کامنتی نگذاشته اید.</p>
                    @endforelse
                </div>


            </div>
        </div>
    </div>

    <script>
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
                document.querySelectorAll('header .user-avatar').forEach(function (el) {
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
    </script>

@endsection
