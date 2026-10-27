@extends('layout')

@section('title', 'ثبت‌نام')

@section('content')

    <div class="min-h-[70vh] flex items-center justify-center py-8">
        <div class="w-full max-w-md">

            {{-- آیکن و عنوان --}}
            <div class="text-center mb-8">
                <div class="mx-auto mb-5 grid place-items-center w-16 h-16 rounded-3xl text-white
                            border border-white/25 shadow-lg shadow-violet-500/30
                            bg-gradient-to-br from-violet-500/70 to-cyan-500/50">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zM3 19.235v-.11a6.375 6.375 0 0112.75 0v.109A12.318 12.318 0 019.374 21c-2.331 0-4.512-.645-6.374-1.766z"/>
                    </svg>
                </div>

                <h1 class="text-3xl font-extrabold text-white">ساخت حساب کاربری</h1>
                <p class="mt-2 text-white/60">در چند ثانیه حساب خود را بسازید.</p>
            </div>

            <form id="register-form" action="{{ route('register') }}" method="POST"
                  class="glass rounded-3xl p-8 sm:p-10 space-y-6" novalidate>
                @csrf

                {{-- نام --}}
                <div>
                    <label for="name" class="block text-sm font-bold mb-2 text-white">نام</label>

                    <div class="relative">
                        <span class="pointer-events-none absolute inset-y-0 right-4 flex items-center text-white/45">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/>
                            </svg>
                        </span>

                        <input type="text" id="name" name="name" required value="{{ old('name') }}"
                               autocomplete="name" autofocus
                               placeholder="نام شما"
                               @error('name') aria-invalid="true" aria-describedby="name-error" @enderror
                               class="glass-input pr-12
                                      @error('name') !border-rose-400/70 @enderror">
                    </div>

                    @error('name')
                    <p id="name-error" class="mt-2 flex items-center gap-1.5 text-xs text-rose-300">
                        <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-4a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 6zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"/>
                        </svg>
                        {{ $message }}
                    </p>
                    @enderror
                </div>

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
                               dir="ltr" autocomplete="email" required
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
                    <label for="password" class="block text-sm font-bold mb-2 text-white">رمز عبور</label>

                    <div class="relative">
                        <span class="pointer-events-none absolute inset-y-0 left-4 flex items-center text-white/45">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V7.5a4.5 4.5 0 10-9 0v3m-1.5 0h12a1.5 1.5 0 011.5 1.5v7.5a1.5 1.5 0 01-1.5 1.5H6a1.5 1.5 0 01-1.5-1.5V12A1.5 1.5 0 016 10.5z"/>
                            </svg>
                        </span>

                        <input type="password" id="password" name="password"
                               dir="ltr" autocomplete="new-password" required
                               placeholder="••••••••"
                               aria-describedby="password-hint{{ $errors->has('password') ? ' password-error' : '' }}"
                               @error('password') aria-invalid="true" @enderror
                               class="glass-input pl-12 pr-12 text-left
                                      @error('password') !border-rose-400/70 @enderror">

                        <button type="button" data-toggle-password="password"
                                class="absolute inset-y-0 right-3 my-auto h-9 w-9 grid place-items-center rounded-xl
                                       text-white/55 hover:text-white hover:bg-white/10 transition cursor-pointer"
                                aria-label="نمایش رمز عبور" aria-pressed="false">
                            <svg data-eye-open class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.04 12.32a1.01 1.01 0 010-.64C3.42 7.51 7.36 4.5 12 4.5c4.64 0 8.58 3.01 9.96 7.18.07.21.07.43 0 .64C20.58 16.49 16.64 19.5 12 19.5c-4.64 0-8.58-3.01-9.96-7.18z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <svg data-eye-closed class="w-5 h-5 hidden" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.22A10.48 10.48 0 001.93 12c1.29 4.06 5.07 7.5 10.07 7.5 1.99 0 3.84-.55 5.42-1.5M6.23 6.23A10.45 10.45 0 0112 4.5c5 0 8.78 3.44 10.07 7.5a10.52 10.52 0 01-4.29 5.77M6.23 6.23L3 3m3.23 3.23l3.65 3.65m7.89 7.89L21 21m-3.23-3.23l-3.65-3.65m0 0a3 3 0 10-4.24-4.24m4.24 4.24L9.88 9.88"/>
                            </svg>
                        </button>
                    </div>

                    {{-- نوار قدرت رمز --}}
                    <div class="mt-3" aria-live="polite">
                        <div class="flex gap-1.5" id="strength-bars">
                            <span class="h-1.5 flex-1 rounded-full bg-white/15 transition-colors"></span>
                            <span class="h-1.5 flex-1 rounded-full bg-white/15 transition-colors"></span>
                            <span class="h-1.5 flex-1 rounded-full bg-white/15 transition-colors"></span>
                            <span class="h-1.5 flex-1 rounded-full bg-white/15 transition-colors"></span>
                        </div>
                        <p id="password-hint" class="mt-2 text-xs text-white/50">
                            حداقل ۸ کاراکتر؛ ترکیب حروف، عدد و نماد رمز را قوی‌تر می‌کند.
                            <span id="strength-label" class="font-bold"></span>
                        </p>
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

                {{-- تکرار رمز عبور --}}
                <div>
                    <label for="password_confirmation" class="block text-sm font-bold mb-2 text-white">تکرار رمز عبور</label>

                    <div class="relative">
                        <span class="pointer-events-none absolute inset-y-0 left-4 flex items-center text-white/45">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/>
                            </svg>
                        </span>

                        <input type="password" id="password_confirmation" name="password_confirmation"
                               dir="ltr" autocomplete="new-password" required
                               placeholder="••••••••"
                               aria-describedby="match-message"
                               class="glass-input pl-12 pr-12 text-left">

                        <button type="button" data-toggle-password="password_confirmation"
                                class="absolute inset-y-0 right-3 my-auto h-9 w-9 grid place-items-center rounded-xl
                                       text-white/55 hover:text-white hover:bg-white/10 transition cursor-pointer"
                                aria-label="نمایش تکرار رمز عبور" aria-pressed="false">
                            <svg data-eye-open class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.04 12.32a1.01 1.01 0 010-.64C3.42 7.51 7.36 4.5 12 4.5c4.64 0 8.58 3.01 9.96 7.18.07.21.07.43 0 .64C20.58 16.49 16.64 19.5 12 19.5c-4.64 0-8.58-3.01-9.96-7.18z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <svg data-eye-closed class="w-5 h-5 hidden" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.22A10.48 10.48 0 001.93 12c1.29 4.06 5.07 7.5 10.07 7.5 1.99 0 3.84-.55 5.42-1.5M6.23 6.23A10.45 10.45 0 0112 4.5c5 0 8.78 3.44 10.07 7.5a10.52 10.52 0 01-4.29 5.77M6.23 6.23L3 3m3.23 3.23l3.65 3.65m7.89 7.89L21 21m-3.23-3.23l-3.65-3.65m0 0a3 3 0 10-4.24-4.24m4.24 4.24L9.88 9.88"/>
                            </svg>
                        </button>
                    </div>

                    <p id="match-message" class="mt-2 text-xs min-h-4" aria-live="polite"></p>
                </div>

                {{-- دکمه ثبت‌نام --}}
                <button type="submit" id="register-btn"
                        class="glass-btn-primary w-full justify-center py-3.5 text-base disabled:opacity-70 disabled:cursor-wait">
                    <svg id="register-spinner" class="hidden w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-90" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                    </svg>
                    <span id="register-label">ثبت‌نام</span>
                </button>

                {{-- ورود --}}
                <div class="pt-5 border-t border-white/10 text-center text-sm text-white/70">
                    قبلاً ثبت‌نام کرده‌اید؟
                    <a href="{{ route('login') }}"
                       class="font-bold text-violet-300 hover:text-violet-200 transition">
                        وارد شوید
                    </a>
                </div>
            </form>

        </div>
    </div>

    <script>
        (function () {
            const pwd = document.getElementById('password');
            const confirmInput = document.getElementById('password_confirmation');
            const bars = document.querySelectorAll('#strength-bars span');
            const strengthLabel = document.getElementById('strength-label');
            const matchMsg = document.getElementById('match-message');
            const form = document.getElementById('register-form');
            const btn = document.getElementById('register-btn');
            const spinner = document.getElementById('register-spinner');
            const label = document.getElementById('register-label');

            // نمایش / مخفی کردن رمز (برای هر دو فیلد)
            document.querySelectorAll('[data-toggle-password]').forEach(function (toggle) {
                const input = document.getElementById(toggle.dataset.togglePassword);
                const open = toggle.querySelector('[data-eye-open]');
                const closed = toggle.querySelector('[data-eye-closed]');

                toggle.addEventListener('click', function () {
                    const show = input.type === 'password';
                    input.type = show ? 'text' : 'password';
                    open.classList.toggle('hidden', show);
                    closed.classList.toggle('hidden', !show);
                    toggle.setAttribute('aria-pressed', show ? 'true' : 'false');
                    input.focus();
                });
            });

            // قدرت رمز عبور
            const levels = [
                {text: 'ضعیف',  bar: 'bg-rose-400',    txt: 'text-rose-300'},
                {text: 'متوسط', bar: 'bg-amber-400',   txt: 'text-amber-300'},
                {text: 'خوب',   bar: 'bg-sky-400',     txt: 'text-sky-300'},
                {text: 'قوی',   bar: 'bg-emerald-400', txt: 'text-emerald-300'}
            ];
            const allBar = levels.map(l => l.bar).concat('bg-white/15');
            const allTxt = levels.map(l => l.txt);

            function score(v) {
                if (!v) return 0;
                let s = 0;
                if (v.length >= 8) s++;
                if (/[a-z]/.test(v) && /[A-Z]/.test(v)) s++;
                if (/\d/.test(v)) s++;
                if (/[^A-Za-z0-9]/.test(v)) s++;
                if (v.length < 6) s = Math.min(s, 1);
                return Math.max(s, 1);
            }

            function updateStrength() {
                const v = pwd.value;
                const s = v ? score(v) : 0;
                const level = levels[s - 1];

                bars.forEach(function (bar, i) {
                    bar.classList.remove.apply(bar.classList, allBar);
                    bar.classList.add(i < s ? level.bar : 'bg-white/15');
                });

                strengthLabel.classList.remove.apply(strengthLabel.classList, allTxt);
                strengthLabel.textContent = s ? '— ' + level.text : '';
                if (s) strengthLabel.classList.add(level.txt);
            }

            // تطابق رمز و تکرار آن
            function updateMatch() {
                matchMsg.classList.remove('text-emerald-300', 'text-rose-300');
                if (!confirmInput.value) { matchMsg.textContent = ''; return; }

                if (confirmInput.value === pwd.value) {
                    matchMsg.textContent = '✓ رمزها یکسان هستند.';
                    matchMsg.classList.add('text-emerald-300');
                } else {
                    matchMsg.textContent = 'رمزها یکسان نیستند.';
                    matchMsg.classList.add('text-rose-300');
                }
            }

            pwd.addEventListener('input', function () { updateStrength(); updateMatch(); });
            confirmInput.addEventListener('input', updateMatch);

            // جلوگیری از ارسال دوباره
            form.addEventListener('submit', function () {
                btn.disabled = true;
                spinner.classList.remove('hidden');
                label.textContent = 'در حال ساخت حساب…';
            });

            window.addEventListener('pageshow', function () {
                btn.disabled = false;
                spinner.classList.add('hidden');
                label.textContent = 'ثبت‌نام';
            });
        })();
    </script>

@endsection
