@extends('layout')
@section('title','create-user')
@section('content')
    <section class="max-w-3xl mx-auto px-6 py-12"> <!-- Page Header -->
        <div class="mb-8">
            <h1 class="text-3xl md:text-4xl font-bold text-white">
                Create User
            </h1>
        </div>

        <!-- Errors -->
        @if($errors->any())
            @foreach($errors->all() as $error)

                <x-alert type="error">{{$error}}</x-alert>
            @endforeach
        @endif

        <!-- Form -->
        <form action="{{route('users.store')}}" method="POST" class="glass rounded-3xl p-6 md:p-8">
            @csrf

            <!-- Name -->
            <div class="mb-6">
                <label for="name" class="block text-sm font-semibold text-white mb-2">
                    نام:
                </label>
                <input type="text" id="name" name="name" placeholder="Enter name"
                       value="{{ old('name') }}" required autocomplete="off"
                       class="glass-input @error('name') !border-rose-400/70 @enderror">
            </div>

            <!-- Email -->
            <div class="mb-6">
                <label for="email" class="block text-sm font-semibold text-white mb-2">
                    ایمیل:
                </label>
                <input type="email" id="email" name="email" placeholder="Enter email"
                       value="{{ old('email') }}" required dir="ltr" autocomplete="off"
                       class="glass-input text-left @error('email') !border-rose-400/70 @enderror">
            </div>

            <!-- User role -->
            <div class="mb-6">
                <label for="role" class="block text-sm font-semibold text-white mb-2">
                    User role:
                </label>
                <select id="role" name="role" required
                        class="glass-input @error('role') !border-rose-400/70 @enderror">
                    <option value="subscriber" @selected(old('role', 'subscriber') === 'subscriber')>Subscriber</option>
                    <option value="admin" @selected(old('role') === 'admin')>Admin</option>
                </select>
                @error('role')
                <p class="text-rose-300 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Avatar -->
            <div class="mb-6">
                <label class="block text-sm font-bold text-white mb-2">آواتار</label>

                <div id="upload-box"
                     class="border-2 border-dashed border-white/30 rounded-2xl bg-white/5 p-6 text-center
                            transition-colors hover:bg-white/10">

                    <input type="file" id="avatar-input" accept="image/*" class="hidden">

                    <label for="avatar-input" id="upload-label" class="cursor-pointer block">
                        <div id="upload-placeholder"
                             class="{{ old('avatar_path') ? 'hidden' : '' }} text-white/55 text-sm">
                            <svg class="w-10 h-10 mx-auto mb-2 text-white/40" xmlns="http://www.w3.org/2000/svg"
                                 fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5"/>
                            </svg>
                            برای انتخاب آواتار کلیک کن
                        </div>

                        <img id="avatar-preview"
                             src="{{ old('avatar_path') ? Storage::url(old('avatar_path')) : '' }}"
                             alt="پیش‌نمایش"
                             class="{{ old('avatar_path') ? '' : 'hidden' }} w-32 h-32 object-cover mx-auto rounded-full
                                    border-2 border-white/40 shadow-lg shadow-violet-500/30 mb-3">
                    </label>

                    {{-- نوار پیشرفت --}}
                    <div id="progress-wrapper" class="hidden mt-4">
                        <div class="w-full bg-white/15 rounded-full h-2 overflow-hidden">
                            <div id="progress-bar"
                                 class="bg-gradient-to-r from-violet-500 to-cyan-400 h-2 rounded-full transition-all"
                                 style="width: 0%"></div>
                        </div>
                        <p id="progress-text" class="text-xs text-white/60 mt-2">0%</p>
                    </div>

                    {{-- پیام موفقیت --}}
                    <p id="upload-success" class="hidden text-emerald-300 text-sm font-semibold mt-3">
                        ✓ آپلود شد
                    </p>
                </div>

                {{-- این input مخفی، مسیر عکس آپلودشده رو نگه می‌داره تا همراه فرم اصلی ارسال بشه --}}
                <input type="hidden" name="avatar_path" id="avatar_path" value="{{ old('avatar_path') }}">

                @error('avatar')
                <p class="text-rose-300 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password -->
            <div class="mb-6">
                <label for="password" class="block text-sm font-semibold text-white mb-2">
                    رمز عبور:
                </label>
                <input type="password" id="password" name="password" placeholder="Enter Password"
                       required dir="ltr" autocomplete="new-password"
                       class="glass-input text-left @error('password') !border-rose-400/70 @enderror">
            </div>

            <!-- Password confirmation -->
            <div class="mb-8">
                <label for="password_confirmation" class="block text-sm font-semibold text-white mb-2">
                    تکرار رمز عبور
                </label>
                <input type="password" id="password_confirmation" name="password_confirmation"
                       placeholder="Repeat Password" required dir="ltr" autocomplete="new-password"
                       class="glass-input text-left">
            </div>

            <!-- Buttons -->
            <div class="flex justify-end gap-3">
                <a  href="{{route('users.index')}}" class="btn-cancel">
                    Cancel
                </a>
                <button type="submit" class="btn-create">
                    Create User
                </button>
            </div>
        </form>
    </section>

@endsection
@push('scripts')
    <script>
        const avatarInput = document.getElementById('avatar-input');
        const uploadBox = document.getElementById('upload-box');
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

            // نمایش پیش‌نمایش عکس فوراً (قبل از تمام شدن آپلود)
            const reader = new FileReader();
            reader.onload = function (e) {
                avatarPreview.src = e.target.result;
                avatarPreview.classList.remove('hidden');
                uploadPlaceholder.classList.add('hidden');
            };
            reader.readAsDataURL(file);

            // ریست کردن وضعیت قبلی
            uploadSuccess.classList.add('hidden');
            uploadBox.classList.remove('border-emerald-400');
            uploadBox.classList.add('border-white/30');
            progressWrapper.classList.remove('hidden');
            progressBar.style.width = '0%';
            progressText.textContent = '0%';
            progressText.classList.remove('text-rose-300');

            // ساخت داده فرم برای ارسال فقط همین فایل
            const formData = new FormData();
            formData.append('avatar', file);
            formData.append('_token', document.querySelector('input[name="_csrf"]')?.value
                || document.querySelector('meta[name="csrf-token"]')?.content
                || document.querySelector('input[name="_token"]').value);

            // استفاده از XMLHttpRequest به‌جای fetch، چون امکان گزارش درصد پیشرفت رو داره
            const xhr = new XMLHttpRequest();
            xhr.open('POST', '{{ route("users.upload-avatar") }}', true);

            // این بخش، درصد آپلود رو محاسبه و نمایش می‌ده
            xhr.upload.addEventListener('progress', function (e) {
                if (e.lengthComputable) {
                    const percent = Math.round((e.loaded / e.total) * 100);
                    progressBar.style.width = percent + '%';
                    progressText.textContent = percent + '%';
                }
            });

            xhr.onload = function () {
                if (xhr.status === 200) {
                    const response = JSON.parse(xhr.responseText);

                    // مسیر عکس رو داخل input مخفی ذخیره می‌کنیم تا با فرم اصلی ارسال بشه
                    avatarPathInput.value = response.path;

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
    </script>
@endpush
