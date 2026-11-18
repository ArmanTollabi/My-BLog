@extends('layout')
@section('title','ایجاد کاربر')
@section('content')
    <section dir="rtl" class="max-w-3xl mx-auto px-6 py-12"> <!-- Page Header -->
        <div class="mb-8">
            <h1 class="text-3xl md:text-4xl font-bold text-white">
                ایجاد کاربر
            </h1>
        </div>

        <!-- Errors -->
        @if($errors->any())
            @foreach($errors->all() as $error)

                <x-alert type="error">{{$error}}</x-alert>
            @endforeach
        @endif

        <!-- Form -->
        <form action="{{route('admin.users.store')}}" method="POST" class="glass rounded-3xl p-6 md:p-8">
            @csrf

            <!-- Name -->
            <div class="mb-6">
                <label for="name" class="block text-sm font-semibold text-white mb-2">
                    نام:
                </label>
                <input type="text" id="name" name="name" placeholder="نام را وارد کنید"
                       value="{{ old('name') }}" required autocomplete="off"
                       class="glass-input @error('name') !border-rose-400/70 @enderror">
            </div>

            <!-- Email -->
            <div class="mb-6">
                <label for="email" class="block text-sm font-semibold text-white mb-2">
                    ایمیل:
                </label>
                <input type="email" id="email" name="email" placeholder="ایمیل را وارد کنید"
                       value="{{ old('email') }}" required dir="ltr" autocomplete="off"
                       class="glass-input text-left @error('email') !border-rose-400/70 @enderror">
            </div>

            <!-- User role -->
            <div class="mb-6">
                <label for="role" class="block text-sm font-semibold text-white mb-2">
                    نقش کاربر:
                </label>

                @can('edit_admin', App\Models\User::class)
                    {{-- فقط ادمین اصلی همه‌ی نقش‌ها را می‌بیند --}}
                    <select id="role" name="role" required
                            class="glass-input @error('role') !border-rose-400/70 @enderror">
                        <option value="">نقش کاربر را انتخاب کنید</option>
                        @foreach(\App\Enums\UserRole::cases() as $role)
                            <option value="{{ $role->value }}" @selected(old('role', \App\Enums\UserRole::Subscriber->value) === $role->value)>{{ $role->label() }}</option>
                        @endforeach
                    </select>
                @else
                    {{-- بقیه‌ی ادمین‌ها فقط «کاربر عادی» می‌سازند (سرور هم این را اجبار می‌کند) --}}
                    <p class="glass-input opacity-70 cursor-not-allowed">{{ \App\Enums\UserRole::Subscriber->label() }}</p>
                @endcan

                @error('role')
                <p class="text-rose-300 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Avatar -->
            <div class="mb-6">
                <label class="block text-sm font-bold text-white mb-2">آواتار</label>

                <div id="upload-box" data-avatar-upload data-upload-url="{{ route('admin.users.upload-avatar') }}"
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
                <input type="password" id="password" name="password" placeholder="رمز عبور را وارد کنید"
                       required dir="ltr" autocomplete="new-password"
                       class="glass-input text-left @error('password') !border-rose-400/70 @enderror">
            </div>

            <!-- Password confirmation -->
            <div class="mb-8">
                <label for="password_confirmation" class="block text-sm font-semibold text-white mb-2">
                    تکرار رمز عبور
                </label>
                <input type="password" id="password_confirmation" name="password_confirmation"
                       placeholder="رمز عبور را تکرار کنید" required dir="ltr" autocomplete="new-password"
                       class="glass-input text-left">
            </div>

            <!-- Buttons -->
            <div class="flex justify-start gap-3">
                <a  href="{{route('admin.users.index')}}" class="btn-cancel">
                    انصراف
                </a>
                <button type="submit" class="btn-create">
                    ایجاد کاربر
                </button>
            </div>
        </form>
    </section>

@endsection
