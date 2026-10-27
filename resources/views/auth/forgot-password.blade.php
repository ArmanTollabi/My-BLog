@extends('layout')

@section('title', 'Forgot Password')

@section('content')

    <div class="min-h-[80vh] flex items-center justify-center px-4 py-8">

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

                <h1 class="text-3xl font-extrabold text-white">
                    Forgot your password?
                </h1>

                <p class="mt-2 text-white/60">
                    Enter your email address and we'll send you a password reset link.
                </p>

            </div>

            <form id="forgot-form" action="{{ route('password.email') }}" method="POST"
                  class="glass rounded-3xl p-8 sm:p-10 space-y-6" novalidate>

                @csrf

                {{-- پیام موفقیت ارسال لینک --}}
                @if (session('status'))
                    <div class="flex items-start gap-3 rounded-2xl px-4 py-3 text-sm
                                text-emerald-200 bg-emerald-500/15 border border-emerald-300/30"
                         role="status">
                        <svg class="w-5 h-5 shrink-0 mt-0.5 text-emerald-300" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>{{ session('status') }}</span>
                    </div>
                @endif

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

                        <input type="email" id="email" name="email" value="{{ old('email') }}"
                               dir="ltr" required autofocus autocomplete="email"
                               placeholder="example@email.com"
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

                {{-- Submit --}}
                <button type="submit" id="forgot-btn"
                        class="glass-btn-primary w-full justify-center py-3.5 text-base disabled:opacity-70 disabled:cursor-wait">
                    <svg id="forgot-spinner" class="hidden w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-90" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                    </svg>
                    <span id="forgot-label">Send Password Reset Link</span>
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
            const form = document.getElementById('forgot-form');
            const btn = document.getElementById('forgot-btn');
            const spinner = document.getElementById('forgot-spinner');
            const label = document.getElementById('forgot-label');

            // جلوگیری از ارسال دوباره (هر ارسال یک ایمیل می‌فرستد)
            form.addEventListener('submit', function () {
                btn.disabled = true;
                spinner.classList.remove('hidden');
                label.textContent = 'Sending…';
            });

            // اگر کاربر با دکمه برگشت مرورگر برگشت، دکمه دوباره فعال شود
            window.addEventListener('pageshow', function () {
                btn.disabled = false;
                spinner.classList.add('hidden');
                label.textContent = 'Send Password Reset Link';
            });
        })();
    </script>

@endsection
