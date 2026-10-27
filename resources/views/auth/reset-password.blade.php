@extends('layout')

@section('title', 'Reset Password')

@section('content')

    <div class="min-h-[80vh] flex items-center justify-center px-4 py-8">

        <div class="w-full max-w-md">

            {{-- آیکن و عنوان --}}
            <div class="text-center mb-8">

                <div class="mx-auto mb-5 grid place-items-center w-16 h-16 rounded-3xl text-white
                            border border-white/25 shadow-lg shadow-violet-500/30
                            bg-gradient-to-br from-violet-500/70 to-cyan-500/50">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.03 5.91c-.62-.1-1.28.05-1.73.5L11.25 16.5H9v2.25H6.75V21H3v-2.25l6.84-6.84c.45-.45.6-1.1.5-1.73A6 6 0 1121.75 8.25z"/>
                    </svg>
                </div>

                <h1 class="text-3xl font-extrabold text-white">
                    Reset your password
                </h1>

                <p class="mt-2 text-white/60">
                    Enter your new password below.
                </p>

            </div>

            <form id="reset-form" action="{{ route('password.update') }}" method="POST"
                  class="glass rounded-3xl p-8 sm:p-10 space-y-6" novalidate>

                @csrf

                <input type="hidden" name="token" value="{{ $token }}">

                {{-- Email --}}
                <div>
                    <label for="email" class="block text-sm font-bold mb-2 text-white">
                        Email address
                    </label>

                    <div class="relative">
                        <span class="pointer-events-none absolute inset-y-0 left-4 flex items-center text-white/45">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0l-9.75 6.75L2.25 6.75"/>
                            </svg>
                        </span>

                        <input type="email" id="email" name="email"
                               value="{{ old('email', request('email')) }}"
                               dir="ltr" required autocomplete="email"
                               @if(old('email', request('email'))) readonly @endif
                               placeholder="example@email.com"
                               @error('email') aria-invalid="true" aria-describedby="email-error" @enderror
                               class="glass-input pl-12 text-left read-only:opacity-70 read-only:cursor-not-allowed
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

                {{-- New password --}}
                <div>
                    <label for="password" class="block text-sm font-bold mb-2 text-white">
                        New password
                    </label>

                    <div class="relative">
                        <span class="pointer-events-none absolute inset-y-0 left-4 flex items-center text-white/45">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V7.5a4.5 4.5 0 10-9 0v3m-1.5 0h12a1.5 1.5 0 011.5 1.5v7.5a1.5 1.5 0 01-1.5 1.5H6a1.5 1.5 0 01-1.5-1.5V12A1.5 1.5 0 016 10.5z"/>
                            </svg>
                        </span>

                        <input type="password" id="password" name="password"
                               dir="ltr" required autocomplete="new-password" autofocus
                               placeholder="Enter your new password"
                               aria-describedby="password-hint{{ $errors->has('password') ? ' password-error' : '' }}"
                               @error('password') aria-invalid="true" @enderror
                               class="glass-input pl-12 pr-12 text-left
                                      @error('password') !border-rose-400/70 @enderror">

                        <button type="button" data-toggle-password="password"
                                class="absolute inset-y-0 right-3 my-auto h-9 w-9 grid place-items-center rounded-xl
                                       text-white/55 hover:text-white hover:bg-white/10 transition cursor-pointer"
                                aria-label="Show password" aria-pressed="false">
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
                            At least 8 characters; mix letters, numbers and symbols for a stronger password.
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

                {{-- Confirm password --}}
                <div>
                    <label for="password_confirmation" class="block text-sm font-bold mb-2 text-white">
                        Confirm new password
                    </label>

                    <div class="relative">
                        <span class="pointer-events-none absolute inset-y-0 left-4 flex items-center text-white/45">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/>
                            </svg>
                        </span>

                        <input type="password" id="password_confirmation" name="password_confirmation"
                               dir="ltr" required autocomplete="new-password"
                               placeholder="Confirm your new password"
                               aria-describedby="match-message"
                               class="glass-input pl-12 pr-12 text-left">

                        <button type="button" data-toggle-password="password_confirmation"
                                class="absolute inset-y-0 right-3 my-auto h-9 w-9 grid place-items-center rounded-xl
                                       text-white/55 hover:text-white hover:bg-white/10 transition cursor-pointer"
                                aria-label="Show confirmation password" aria-pressed="false">
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

                {{-- Submit --}}
                <button type="submit" id="reset-btn"
                        class="glass-btn-primary w-full justify-center py-3.5 text-base disabled:opacity-70 disabled:cursor-wait">
                    <svg id="reset-spinner" class="hidden w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-90" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                    </svg>
                    <span id="reset-label">Reset Password</span>
                </button>

                {{-- Back to login --}}
                <div class="pt-5 border-t border-white/10 text-center">
                    <a href="{{ route('login') }}"
                       class="text-sm font-medium text-violet-300 hover:text-violet-200 transition">
                        ← Back to Login
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
            const form = document.getElementById('reset-form');
            const btn = document.getElementById('reset-btn');
            const spinner = document.getElementById('reset-spinner');
            const label = document.getElementById('reset-label');

            // Show / hide password (both fields)
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

            // Password strength (guidance only; the server still validates)
            const levels = [
                {text: 'Weak',   bar: 'bg-rose-400',    txt: 'text-rose-300'},
                {text: 'Fair',   bar: 'bg-amber-400',   txt: 'text-amber-300'},
                {text: 'Good',   bar: 'bg-sky-400',     txt: 'text-sky-300'},
                {text: 'Strong', bar: 'bg-emerald-400', txt: 'text-emerald-300'}
            ];
            const allBar = levels.map(function (l) { return l.bar; }).concat('bg-white/15');
            const allTxt = levels.map(function (l) { return l.txt; });

            function score(v) {
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

            // Match check
            function updateMatch() {
                matchMsg.classList.remove('text-emerald-300', 'text-rose-300');
                if (!confirmInput.value) { matchMsg.textContent = ''; return; }

                if (confirmInput.value === pwd.value) {
                    matchMsg.textContent = '✓ Passwords match.';
                    matchMsg.classList.add('text-emerald-300');
                } else {
                    matchMsg.textContent = 'Passwords do not match.';
                    matchMsg.classList.add('text-rose-300');
                }
            }

            pwd.addEventListener('input', function () { updateStrength(); updateMatch(); });
            confirmInput.addEventListener('input', updateMatch);

            // Prevent double submit
            form.addEventListener('submit', function () {
                btn.disabled = true;
                spinner.classList.remove('hidden');
                label.textContent = 'Resetting…';
            });

            window.addEventListener('pageshow', function () {
                btn.disabled = false;
                spinner.classList.add('hidden');
                label.textContent = 'Reset Password';
            });
        })();
    </script>

@endsection
