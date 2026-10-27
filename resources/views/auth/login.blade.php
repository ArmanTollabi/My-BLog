@extends('layout')

@section('title', 'ورود')

@section('content')

    <div class="min-h-[70vh] flex items-center justify-center py-8">
        <div class="w-full max-w-md">

            {{-- آیکن و عنوان --}}
            <div class="text-center mb-8">
                <div class="mx-auto mb-5 grid place-items-center w-16 h-16 rounded-3xl text-white
                            border border-white/25 shadow-lg shadow-violet-500/30
                            bg-gradient-to-br from-violet-500/70 to-cyan-500/50">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V7.5a4.5 4.5 0 10-9 0v3m-1.5 0h12a1.5 1.5 0 011.5 1.5v7.5a1.5 1.5 0 01-1.5 1.5H6a1.5 1.5 0 01-1.5-1.5V12A1.5 1.5 0 016 10.5z"/>
                    </svg>
                </div>

                <h1 class="text-3xl font-extrabold text-white">ورود به حساب کاربری</h1>
                <p class="mt-2 text-white/60">برای ادامه، اطلاعات حساب خود را وارد کنید.</p>
            </div>

            <form id="login-form" action="{{ route('login') }}" method="POST"
                  class="glass rounded-3xl p-8 sm:p-10 space-y-6" novalidate>
                @csrf

                {{-- پیام وضعیت (مثلاً بعد از بازیابی رمز) --}}
                @if (session('status'))
                    <div class="rounded-2xl px-4 py-3 text-sm text-emerald-200
                                bg-emerald-500/15 border border-emerald-300/30" role="status">
                        {{ session('status') }}
                    </div>
                @endif

                {{-- ایمیل --}}
                <div>
                    <label for="email" class="block text-sm font-bold mb-2 text-white">ایمیل</label>

                    <div class="relative">
                        <span class="pointer-events-none absolute inset-y-0 left-4 flex items-center text-white/45">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0l-9.75 6.75L2.25 6.75"/>
                            </svg>
                        </span>

                        <input type="email" id="email" name="email" value="{{ old('email') }}"
                               dir="ltr" autocomplete="email" autofocus required
                               placeholder="name@example.com"
                               @error('email') aria-invalid="true" aria-describedby="email-error" @enderror
                               class="glass-input pl-12 text-left
                                      @error('email') !border-rose-400/70 @enderror">
                    </div>

                    @error('email')
                    <p id="email-error" class="mt-2 flex items-center gap-1.5 text-xs text-rose-300">
                        <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-4a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 6zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"/>
                        </svg>
                        {{ $message }}
                    </p>
                    @enderror
                </div>

                {{-- رمز عبور --}}
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label for="password" class="block text-sm font-bold text-white">رمز عبور</label>

                        <a href="{{ route('password.request') }}"
                           class="text-xs font-medium text-violet-300 hover:text-violet-200 transition">
                            رمز عبور را فراموش کرده‌اید؟
                        </a>
                    </div>

                    <div class="relative">
                        <span class="pointer-events-none absolute inset-y-0 left-4 flex items-center text-white/45">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.03 5.91c-.62-.1-1.28.05-1.73.5L11.25 16.5H9v2.25H6.75V21H3v-2.25l6.84-6.84c.45-.45.6-1.1.5-1.73A6 6 0 1121.75 8.25z"/>
                            </svg>
                        </span>

                        <input type="password" id="password" name="password"
                               dir="ltr" autocomplete="current-password" required
                               placeholder="••••••••"
                               @error('password') aria-invalid="true" aria-describedby="password-error" @enderror
                               class="glass-input pl-12 pr-12 text-left
                                      @error('password') !border-rose-400/70 @enderror">

                        {{-- نمایش/مخفی کردن رمز --}}
                        <button type="button" id="toggle-password"
                                class="absolute inset-y-0 right-3 my-auto h-9 w-9 grid place-items-center rounded-xl
                                       text-white/55 hover:text-white hover:bg-white/10 transition cursor-pointer"
                                aria-label="نمایش رمز عبور" aria-pressed="false">
                            <svg id="eye-open" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.04 12.32a1.01 1.01 0 010-.64C3.42 7.51 7.36 4.5 12 4.5c4.64 0 8.58 3.01 9.96 7.18.07.21.07.43 0 .64C20.58 16.49 16.64 19.5 12 19.5c-4.64 0-8.58-3.01-9.96-7.18z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <svg id="eye-closed" class="w-5 h-5 hidden" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.22A10.48 10.48 0 001.93 12c1.29 4.06 5.07 7.5 10.07 7.5 1.99 0 3.84-.55 5.42-1.5M6.23 6.23A10.45 10.45 0 0112 4.5c5 0 8.78 3.44 10.07 7.5a10.52 10.52 0 01-4.29 5.77M6.23 6.23L3 3m3.23 3.23l3.65 3.65m7.89 7.89L21 21m-3.23-3.23l-3.65-3.65m0 0a3 3 0 10-4.24-4.24m4.24 4.24L9.88 9.88"/>
                            </svg>
                        </button>
                    </div>

                    @error('password')
                    <p id="password-error" class="mt-2 flex items-center gap-1.5 text-xs text-rose-300">
                        <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-4a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 6zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"/>
                        </svg>
                        {{ $message }}
                    </p>
                    @enderror
                </div>

                {{-- مرا به خاطر بسپار --}}
                <label class="flex items-center gap-2.5 text-sm text-white/70 cursor-pointer select-none w-fit">
                    <input type="checkbox" name="remember" value="1"
                           class="w-4 h-4 rounded accent-violet-500 cursor-pointer"
                        {{ old('remember') ? 'checked' : '' }}>
                    من را به خاطر بسپار
                </label>

                {{-- دکمه ورود --}}
                <button type="submit" id="login-btn"
                        class="glass-btn-primary w-full justify-center py-3.5 text-base disabled:opacity-70 disabled:cursor-wait">
                    <svg id="login-spinner" class="hidden w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-90" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                    </svg>
                    <span id="login-label">ورود</span>
                </button>

                {{-- ثبت‌نام --}}
                <div class="pt-5 border-t border-white/10 text-center text-sm text-white/70">
                    حساب کاربری ندارید؟
                    <a href="{{ route('register') }}"
                       class="font-bold text-violet-300 hover:text-violet-200 transition">
                        ثبت‌نام کنید
                    </a>
                </div>
            </form>

        </div>
    </div>

    <script>
        (function () {
            const pwd = document.getElementById('password');
            const toggle = document.getElementById('toggle-password');
            const open = document.getElementById('eye-open');
            const closed = document.getElementById('eye-closed');
            const form = document.getElementById('login-form');
            const btn = document.getElementById('login-btn');
            const spinner = document.getElementById('login-spinner');
            const label = document.getElementById('login-label');

            // نمایش / مخفی کردن رمز عبور
            toggle.addEventListener('click', function () {
                const show = pwd.type === 'password';
                pwd.type = show ? 'text' : 'password';
                open.classList.toggle('hidden', show);
                closed.classList.toggle('hidden', !show);
                toggle.setAttribute('aria-pressed', show ? 'true' : 'false');
                toggle.setAttribute('aria-label', show ? 'مخفی کردن رمز عبور' : 'نمایش رمز عبور');
                pwd.focus();
            });

            // جلوگیری از ارسال دوباره + نمایش حالت «در حال ورود»
            form.addEventListener('submit', function () {
                btn.disabled = true;
                spinner.classList.remove('hidden');
                label.textContent = 'در حال ورود…';
            });

            // اگر کاربر با دکمه برگشت مرورگر برگشت، دکمه دوباره فعال شود
            window.addEventListener('pageshow', function () {
                btn.disabled = false;
                spinner.classList.add('hidden');
                label.textContent = 'ورود';
            });
        })();
    </script>

@endsection
