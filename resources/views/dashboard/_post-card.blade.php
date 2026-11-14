{{-- کارت پست برای داشبورد (پست‌های شما / بوکمارک‌ها): عکس، تیتر، توضیحات؛ کل کارت لینک به posts.show.
     $showStatus = true → نشانک وضعیت (پیش‌نویس/زمان‌بندی‌شده/...) هم نشان داده می‌شود (فقط «پست‌های شما») --}}
<a href="{{ route('posts.show', $post) }}"
   class="group glass flex flex-col overflow-hidden rounded-3xl transition hover:-translate-y-1 hover:bg-white/5">

    <div class="relative aspect-[16/10] overflow-hidden">
        @if($post->image)
            <img src="{{ Storage::url($post->image) }}" alt="{{ $post->title }}" loading="lazy"
                 class="h-full w-full object-cover transition duration-500 group-hover:scale-105">
        @else
            <div class="relative flex h-full w-full items-center justify-center overflow-hidden
                        bg-gradient-to-br from-violet-600/60 via-fuchsia-500/40 to-cyan-500/50">
                <div class="absolute -top-8 -start-8 h-32 w-32 rounded-full bg-white/15 blur-2xl"></div>
                <div class="absolute -bottom-10 -end-6 h-40 w-40 rounded-full bg-cyan-300/20 blur-2xl"></div>
                <svg class="relative h-12 w-12 text-white/70" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                     stroke-width="1.4" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909M3.75 21h16.5A1.5 1.5 0 0 0 21.75 19.5v-15A1.5 1.5 0 0 0 20.25 3H3.75A1.5 1.5 0 0 0 2.25 4.5v15A1.5 1.5 0 0 0 3.75 21Zm10.5-11.25h.008v.008h-.008V9.75Z"/>
                </svg>
            </div>
        @endif

        @if(! empty($showStatus))
            <x-post-status :post="$post" class="absolute end-3 top-3 backdrop-blur" />
        @endif

        @if($post->category)
            <span class="absolute start-3 top-3 rounded-full border border-white/25 bg-black/35 px-3 py-1 text-xs font-semibold text-white backdrop-blur">
                {{ $post->category->title }}
            </span>
        @endif
    </div>

    <div class="flex flex-1 flex-col p-5">
        <h2 dir="auto" class="line-clamp-2 text-lg font-bold text-white transition group-hover:text-violet-300">{{ $post->title }}</h2>

        <p dir="auto" class="mt-2 line-clamp-3 text-sm leading-7 text-white/60">{{ $post->description }}</p>

        <div class="mt-auto flex items-center justify-between gap-3 pt-4 text-xs text-white/45">
            {{-- تاریخ شمسی: منتشرشده‌ها = تاریخ انتشار؛ زمان‌بندی‌شده = زمان انتشار؛ بقیه = تاریخ ساخت --}}
            <span>
                @if(! empty($showStatus) && $post->effective_status === \App\Enums\PostStatus::Scheduled)
                    انتشار: {{ $post->published_at_with_hours_fa }}
                @else
                    {{ $post->published_at_fa ?? $post->created_at_fa }}
                @endif
            </span>

            @isset($post->likes_count)
                <span class="inline-flex items-center gap-1.5 {{ $post->likes_count ? 'text-rose-300' : '' }}">
                    <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="1.8"
                         stroke="currentColor" fill="{{ $post->likes_count ? 'currentColor' : 'none' }}" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z"/>
                    </svg>
                    <span class="font-semibold tabular-nums">{{ number_format($post->likes_count) }}</span>
                </span>
            @endisset
        </div>
    </div>
</a>
