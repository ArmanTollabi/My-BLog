{{-- FILE: resources/views/posts/_login-modal.blade.php
     پاپ‌آپ «برای این کار باید وارد شوید» (فقط مهمان‌ها؛ برای لایک و ذخیره).
     باز کردن: document.dispatchEvent(new CustomEvent('open-login-modal', {detail: {kind: 'like' | 'save'}}))
     بستن: ×، «فعلاً نه»، کلیک بیرون، Esc   —   استایل: css/login-modal.css ، رفتار: js/login-modal.js --}}
@guest
    <div id="login-modal" class="login-modal" role="dialog" aria-modal="true" aria-labelledby="login-modal-title" data-kind="save" hidden>
        <div class="login-modal-backdrop" data-close></div>

        <div class="login-modal-card glass">
            <button type="button" class="login-modal-close" data-close aria-label="بستن">
                <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                     stroke-width="2" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/>
                </svg>
            </button>

            <div class="login-modal-icon" aria-hidden="true">
                <svg data-kind-icon="save" class="w-9 h-9" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M6.32 2.577a49.255 49.255 0 0 1 11.36 0c1.497.174 2.57 1.46 2.57 2.93V21a.75.75 0 0 1-1.085.67L12 18.089l-7.165 3.583A.75.75 0 0 1 3.75 21V5.507c0-1.47 1.073-2.756 2.57-2.93Z"/>
                </svg>
                <svg data-kind-icon="like" hidden class="w-9 h-9" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M11.645 20.91l-.007-.003-.022-.012a15.247 15.247 0 0 1-.383-.218 25.18 25.18 0 0 1-4.244-3.17C4.688 15.36 2.25 12.174 2.25 8.25 2.25 5.322 4.714 3 7.688 3A5.5 5.5 0 0 1 12 5.052 5.5 5.5 0 0 1 16.313 3c2.973 0 5.437 2.322 5.437 5.25 0 3.925-2.438 7.111-4.739 9.256a25.175 25.175 0 0 1-4.244 3.17 15.247 15.247 0 0 1-.383.219l-.022.012-.007.004-.003.001a.752.752 0 0 1-.704 0l-.003-.001Z"/>
                </svg>
            </div>

            {{-- عنوان و متن را login-modal.js بسته به نوع عمل عوض می‌کند --}}
            <h2 id="login-modal-title" class="mt-5 text-2xl font-extrabold text-white">پست را برای بعد نگه دارید</h2>
            <p id="login-modal-text" class="mt-2 text-white/65 leading-7">
                برای ذخیره‌ی پست در بوکمارک‌ها باید وارد حساب کاربری‌تان شوید یا ثبت‌نام کنید. فقط چند ثانیه طول می‌کشد!
            </p>

            <div class="mt-7 flex flex-col gap-3 sm:flex-row sm:justify-center">
                <a href="{{ route('login') }}" class="btn-create justify-center">ورود به حساب</a>
                <a href="{{ route('register') }}" class="btn-glass justify-center">ثبت‌نام</a>
            </div>

            <button type="button" data-close class="mt-5 text-sm text-white/50 transition hover:text-white">
                فعلاً نه، ممنون
            </button>
        </div>
    </div>
@endguest
