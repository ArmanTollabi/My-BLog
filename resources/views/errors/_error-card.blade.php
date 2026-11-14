{{--
    کارت مشترک صفحات خطا
    ورودی: $code, $title, $message, $icon (اختیاری)
--}}
<section dir="rtl" class="max-w-2xl mx-auto px-6 py-20 md:py-28">

    <div class="glass rounded-3xl p-8 md:p-12 text-center relative overflow-hidden">

        {{-- درخشش پس‌زمینه --}}
        <div class="pointer-events-none absolute -top-24 left-1/2 h-64 w-64 -translate-x-1/2 rounded-full
                    bg-violet-500/30 blur-3xl" aria-hidden="true"></div>

        {{-- آیکون --}}
        <div class="relative mx-auto mb-6 flex h-20 w-20 items-center justify-center rounded-full
                    border border-white/20 bg-white/10 shadow-lg shadow-violet-500/30">
            {!! $icon !!}
        </div>

        {{-- کد خطا --}}
        <p dir="ltr" class="relative text-7xl md:text-8xl font-extrabold gradient-text leading-none">
            {{ $code }}
        </p>

        <h1 class="relative mt-6 text-2xl md:text-3xl font-bold text-white">
            {{ $title }}
        </h1>

        <p class="relative mt-3 text-white/60 leading-8">
            {{ $message }}
        </p>

        {{-- دکمه‌ها --}}
        <div class="relative mt-8 flex flex-wrap items-center justify-center gap-3">
            <a href="{{ route('posts.index') }}" class="btn-create">
                بازگشت به صفحه‌ی اصلی
            </a>

            <button type="button" onclick="history.length > 1 ? history.back() : location.href='{{ route('posts.index') }}'"
                    class="btn-glass">
                <span aria-hidden="true">→</span> صفحه‌ی قبلی
            </button>
        </div>

    </div>

</section>
