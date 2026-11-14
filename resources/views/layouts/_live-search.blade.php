{{-- FILE: resources/views/layouts/_live-search.blade.php
     جستجوی زنده (AJAX) سراسری سایت — پنجره‌ی جستجو (command palette).

     رفتار:
       • تا وقتی کاربر حداقل ۲ حرف ننوشته، هیچ درخواستی ارسال نمی‌شود.
       • بعد از ۲ حرف، ۱٫۵ ثانیه از آخرین کاراکتر صبر می‌کند (نوار باریک بالا نشان می‌دهد چقدر مانده)، بعد جستجو می‌کند.
       • Enter صبر را رد می‌کند و فوراً جستجو می‌کند.
       • حداکثر ۵ نتیجه + لینک «مشاهده‌ی همه‌ی نتایج»؛ اگر نتیجه‌ای نبود «چیزی پیدا نشد».
       • باز کردن: هر عنصری با data-open-search، کلید / ، یا Ctrl/⌘ + K.
     استایل: resources/css/search.css — رفتار: resources/js/live-search.js (در app.js import شده).
     این فایل یک‌بار از layouts/header صدا زده می‌شود (باید بیرون از <section class="glass"> باشد،
     چون backdrop-filter جلوی کار کردن position:fixed داخل را می‌گیرد). --}}


<div id="ls" class="ls-overlay" hidden
     data-search-url="{{ route('posts.search') }}" data-all-url="{{ route('posts.index') }}">
    <div class="ls-backdrop" data-ls-close></div>

    <div class="ls-box" role="dialog" aria-modal="true" aria-label="جستجو در پست‌ها">

        {{-- بدون JS هم کار می‌کند: به صفحه‌ی پست‌ها با پارامتر search می‌رود --}}
        <form id="ls-form" class="ls-row" role="search" action="{{ route('posts.index') }}" method="GET">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/>
            </svg>

            <input id="ls-input" class="ls-input" type="text" inputmode="search" name="search" maxlength="60"
                   autocomplete="off" autocapitalize="off" spellcheck="false" enterkeyhint="search"
                   placeholder="جستجو در پست‌ها…" aria-controls="ls-body">

            <span id="ls-spin" class="ls-spin" hidden aria-hidden="true"></span>
            <button type="button" id="ls-clear" class="ls-btn" hidden aria-label="پاک کردن">✕</button>
            <button type="button" class="ls-btn" data-ls-close aria-label="بستن">Esc</button>
        </form>

        <div id="ls-wait" class="ls-wait" aria-hidden="true"></div>
        <div id="ls-body" class="ls-body" aria-live="polite"></div>
    </div>
</div>
