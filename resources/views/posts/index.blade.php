@extends('layout')
@section('title','پست‌ها')
@section('content')
    <!--<x-alert type="success"/>
<x-alert type="error"/>--->
    <!-- Posts List -->
    @php
        // ---- فیلترهای فعال (یک بار تعریف می‌شود؛ هم نوار ابزار و هم حالت خالی از آن استفاده می‌کنند) ----
        $sort = in_array(request('sort'), ['popular', 'oldest'], true) ? request('sort') : 'latest';
        $activeFilters = [];
        if (request()->filled('search')) {
            $activeFilters[] = ['key' => 'search', 'kind' => 'جستجو', 'label' => request('search')];
        }
        if (request()->filled('category')) {
            $selectedCategory = $categories->firstWhere('id', (int) request('category'));
            if ($selectedCategory) {
                $activeFilters[] = ['key' => 'category', 'kind' => 'دسته', 'label' => $selectedCategory->title];
            }
        }
        if (request()->filled('tag')) {
            $selectedTag = $tags->firstWhere('id', (int) request('tag'));
            if ($selectedTag) {
                $activeFilters[] = ['key' => 'tag', 'kind' => 'تگ', 'label' => '#' . $selectedTag->title];
            }
        }
        // ساخت لینک با حفظ بقیه‌ی فیلترها (صفحه‌بندی همیشه به صفحه‌ی ۱ برمی‌گردد)
        $qs = fn (array $over = []) => route('posts.index', array_filter(
            array_merge(request()->only(['search', 'sort', 'category', 'tag']), $over),
            fn ($v) => $v !== null && $v !== '' && $v !== 'latest'
        ));
        $sorts = ['latest' => 'جدیدترین', 'popular' => 'محبوب‌ترین', 'oldest' => 'قدیمی‌ترین'];
    @endphp

    <x-seo title="پست‌ها"
           description="تازه‌ترین پست‌ها و مقاله‌های وبلاگ؛ جستجو، فیلتر بر اساس دسته‌بندی و تگ."
           :noindex="request()->hasAny(['search', 'sort', 'category', 'tag'])" />

    <section class="max-w-6xl mx-auto px-6 py-8 md:py-10">

        <x-breadcrumbs :items="array_filter([
            ['label' => 'خانه', 'url' => route('home')],
            isset($selectedCategory) ? ['label' => 'پست‌ها', 'url' => route('posts.index')] : ['label' => 'پست‌ها'],
            isset($selectedCategory) ? ['label' => $selectedCategory->title] : null,
        ])" />

        {{-- ===== عنوان صفحه ===== --}}
        <div class="pf-head">
            <div>
                <h1 class="text-3xl md:text-4xl font-bold text-white">پست‌ها</h1>
                <p class="mt-2 text-white/60">تازه‌ترین پست‌ها و مقاله‌ها را بخوانید.</p>
            </div>
            @auth
                @can('create', \App\Models\Post::class)
                    <a href="{{ route('posts.create') }}" class="btn-create">+ ایجاد پست</a>
                @endcan
            @endauth
        </div>

        {{-- ===== نوار جستجو، مرتب‌سازی و فیلتر ===== --}}
        <div class="pf-bar glass">

            <div class="pf-top">
                <form class="pf-search" action="{{ route('posts.index') }}" method="GET" role="search">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/>
                    </svg>
                    <input class="pf-input" type="search" name="search" value="{{ request('search') }}" maxlength="60"
                           placeholder="جستجو در عنوان و متن پست‌ها…" aria-label="جستجو">
                    @foreach(['sort', 'category', 'tag'] as $keep)
                        @if(request()->filled($keep)) <input type="hidden" name="{{ $keep }}" value="{{ request($keep) }}"> @endif
                    @endforeach
                    @if(request()->filled('search'))
                        <a class="pf-x" href="{{ $qs(['search' => null]) }}" aria-label="پاک کردن جستجو">✕</a>
                    @endif
                </form>

                <nav class="pf-seg" aria-label="مرتب‌سازی">
                    @foreach($sorts as $key => $label)
                        <a href="{{ $qs(['sort' => $key]) }}" class="{{ $sort === $key ? 'is-on' : '' }}"
                           @if($sort === $key) aria-current="true" @endif>{{ $label }}</a>
                    @endforeach
                </nav>
            </div>

            {{-- دسته‌بندی‌ها --}}
            <div class="pf-chips" role="group" aria-label="دسته‌بندی">
                <a href="{{ $qs(['category' => null]) }}" class="pf-chip {{ ! request()->filled('category') ? 'is-on' : '' }}">همه</a>
                @foreach($categories as $category)
                    <a href="{{ $qs(['category' => $category->id]) }}"
                       class="pf-chip {{ (int) request('category') === $category->id ? 'is-on' : '' }}">
                        {{ $category->title }}
                        @isset($category->posts_count) <small>{{ number_format($category->posts_count) }}</small> @endisset
                    </a>
                @endforeach
            </div>

            {{-- تگ‌ها --}}
            @if($tags->isNotEmpty())
                <details class="pf-tags" {{ request()->filled('tag') ? 'open' : '' }}>
                    <summary>
                        فیلتر بر اساس تگ
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5"/></svg>
                    </summary>
                    <div class="pf-chips">
                        @foreach($tags as $tag)
                            <a href="{{ $qs(['tag' => (int) request('tag') === $tag->id ? null : $tag->id]) }}"
                               class="pf-chip {{ (int) request('tag') === $tag->id ? 'is-on' : '' }}">#{{ $tag->title }}</a>
                        @endforeach
                    </div>
                </details>
            @endif

            {{-- خلاصه‌ی نتیجه + فیلترهای فعال --}}
            <div class="pf-sum" aria-live="polite">
                <span><b>{{ number_format($posts->total()) }}</b> پست پیدا شد</span>
                @foreach($activeFilters as $filter)
                    <a href="{{ $qs([$filter['key'] => null]) }}" class="pf-act" title="برداشتن این فیلتر">
                        <span class="t">{{ $filter['kind'] }}: {{ $filter['label'] }}</span><i aria-hidden="true">✕</i>
                    </a>
                @endforeach
                @if(count($activeFilters) || $sort !== 'latest')
                    <a href="{{ route('posts.index') }}" class="pf-reset">پاک کردن همه</a>
                @endif
            </div>
        </div>

        @if($posts->isNotEmpty())
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach($posts as $post)
                    <article class="glass-card group !p-0 overflow-hidden flex flex-col">

                        {{-- کاور: عکس پست یا طرح پیش‌فرض --}}
                        <a href="{{ route('posts.show', $post) }}" class="relative block aspect-[16/10] overflow-hidden">
                            @if($post->image)
                                <img src="{{ $post->image_thumb_url }}" alt="{{ $post->title }}" width="640" height="400" loading="lazy" decoding="async"
                                     class="w-full h-full object-cover transition duration-500 group-hover:scale-105">
                            @else
                                <div class="relative w-full h-full flex items-center justify-center overflow-hidden
                                            bg-gradient-to-br from-violet-600/60 via-fuchsia-500/40 to-cyan-500/50">
                                    <div class="absolute -top-8 -start-8 w-32 h-32 rounded-full bg-white/15 blur-2xl"></div>
                                    <div class="absolute -bottom-10 -end-6 w-40 h-40 rounded-full bg-cyan-300/20 blur-2xl"></div>
                                    <svg class="relative w-14 h-14 text-white/70" xmlns="http://www.w3.org/2000/svg"
                                         fill="none" viewBox="0 0 24 24" stroke-width="1.4" stroke="currentColor" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                              d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909M3.75 21h16.5A1.5 1.5 0 0 0 21.75 19.5v-15A1.5 1.5 0 0 0 20.25 3H3.75A1.5 1.5 0 0 0 2.25 4.5v15A1.5 1.5 0 0 0 3.75 21Zm10.5-11.25h.008v.008h-.008V9.75Z"/>
                                    </svg>
                                </div>
                            @endif

                            <div class="absolute inset-x-0 bottom-0 h-1/3 bg-gradient-to-t from-black/40 to-transparent"></div>

                            <span class="absolute top-3 start-3 rounded-full border border-white/25 bg-black/35 px-3 py-1
                                         text-xs font-semibold text-white backdrop-blur">
                                {{ $post->category->title ?? 'بدون دسته' }}
                            </span>
                        </a>

                        <div class="p-5 flex flex-col flex-1">

                            <h2 class="text-lg font-bold text-white group-hover:text-violet-300 transition line-clamp-2">
                                <a href="{{ route('posts.show', $post) }}">{{ $post->title }}</a>
                            </h2>

                            <p class="text-white/60 text-sm leading-7 mt-2 line-clamp-3">
                                {{ $post->description }}
                            </p>

                            @if($post->tags->isNotEmpty())

                                    <div class="flex flex-wrap gap-2 mt-3">
                                        @foreach($post->tags as $tag)
                                            <a href="{{ route('tags.show', $tag) }}"
                                               class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                      bg-purple-100 text-purple-700 hover:bg-purple-200 transition">
                                                #{{ $tag->title }}
                                            </a>
                                        @endforeach
                                    </div>
                            @endif

                            <div class="mt-auto flex items-center justify-between gap-3 border-t border-white/10 pt-4 text-xs text-white/45">
                                <div class="min-w-0">@include('posts._meta', ['post' => $post])</div>

                                <div class="flex shrink-0 items-center gap-1">

                                    {{-- تعداد لایک (فقط نمایشی) --}}
                                    <span class="inline-flex items-center gap-1.5 pe-1 {{ $post->likes_count ? 'text-rose-300' : '' }}"
                                          title="{{ $post->likes_count }} لایک">
                                        <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="1.8"
                                             stroke="currentColor" fill="{{ $post->likes_count ? 'currentColor' : 'none' }}" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                  d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z"/>
                                        </svg>
                                        <span class="font-semibold tabular-nums">{{ number_format($post->likes_count) }}</span>
                                    </span>

                                    {{-- بوکمارک (قابل کلیک) --}}
                                    <button type="button"
                                            class="card-save {{ $post->is_saved ? 'is-saved' : '' }}"
                                            data-toggle-btn data-kind="save"
                                            data-url="{{ route('posts.save', $post) }}"
                                            data-auth="{{ auth()->check() ? '1' : '0' }}"
                                            aria-pressed="{{ $post->is_saved ? 'true' : 'false' }}"
                                            aria-label="ذخیره‌ی پست"
                                            title="{{ $post->is_saved ? 'برداشتن از بوکمارک‌ها' : 'ذخیره در بوکمارک‌ها' }}">
                                        <svg class="h-[1.15rem] w-[1.15rem]" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                                             stroke-width="1.8" stroke="currentColor" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                  d="M17.593 3.322c1.1.128 1.907 1.077 1.907 2.185V21L12 17.25 4.5 21V5.507c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0 1 11.186 0Z"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            @canany(['update', 'delete'], $post)
                                <div class="flex flex-row items-center gap-3 mt-4 pt-4 border-t border-white/10">
                                    @can('update', $post)
                                        <a href="{{ route('posts.edit', $post) }}" class="btn-edit">ویرایش</a>
                                    @endcan

                                    @can('delete', $post)
                                        <form action="{{ route('posts.destroy', $post) }}" method="POST"
                                              data-confirm="مطمئنی می‌خوای این پست رو حذف کنی؟">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-delete">حذف</button>
                                        </form>
                                    @endcan
                                </div>
                            @endcanany

                        </div>
                    </article>
                @endforeach
            </div>
        @else
            {{-- ================= حالت «نتیجه‌ای پیدا نشد» ================= --}}

            <div class="glass rounded-3xl px-6 py-14 text-center" role="status">

                @if(count($activeFilters))

                    <div class="animate-float-soft mx-auto grid h-20 w-20 place-items-center rounded-3xl
                                    bg-violet-500/20 text-violet-200 shadow-lg shadow-violet-500/20">
                        <svg class="h-10 w-10" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                             stroke-width="1.6" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/>
                        </svg>
                    </div>

                    <h2 class="mt-6 text-2xl font-extrabold text-white">پستی پیدا نشد</h2>
                    <p class="mx-auto mt-2 max-w-md text-white/60">
                        هیچ پستی با این شرایط وجود ندارد. می‌توانی یکی از فیلترها را بردار یا عبارت دیگری را امتحان کنی.
                    </p>

                    {{-- فیلترهای فعال: با کلیک روی هر کدام، همان فیلتر برداشته می‌شود --}}
                    <div class="mt-6 flex flex-wrap items-center justify-center gap-2">
                        @foreach($activeFilters as $filter)
                            <a href="{{ $qs([$filter['key'] => null]) }}"
                               class="group inline-flex max-w-full items-center gap-2 rounded-full border border-white/20
                                          bg-white/10 py-1.5 ps-3 pe-2 text-sm text-white/85 transition
                                          hover:bg-rose-500/25 hover:text-white"
                               title="برداشتن این فیلتر">
                                <span class="text-white/50">{{ $filter['kind'] }}:</span>
                                <span class="truncate font-semibold">{{ $filter['label'] }}</span>
                                <span class="grid h-5 w-5 flex-none place-items-center rounded-full bg-white/15
                                                 text-xs leading-none group-hover:bg-rose-500/60" aria-hidden="true">✕</span>
                            </a>
                        @endforeach
                    </div>

                    <div class="mt-8 flex flex-wrap items-center justify-center gap-3">
                        <a href="{{ route('posts.index') }}" class="btn-create">
                            نمایش همه‌ی پست‌ها
                        </a>

                        @can('create',App\Models\Post::class)
                            <a href="{{ route('posts.create') }}" class="btn-glass">
                                + نوشتن پست جدید
                            </a>
                        @endcan

                    </div>

                @else

                    {{-- هنوز هیچ پستی در سایت وجود ندارد --}}
                    <div class="animate-float-soft mx-auto grid h-20 w-20 place-items-center rounded-3xl
                                    bg-cyan-500/20 text-cyan-200 shadow-lg shadow-cyan-500/20">
                        <svg class="h-10 w-10" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                             stroke-width="1.6" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125"/>
                        </svg>
                    </div>

                    <h2 class="mt-6 text-2xl font-extrabold text-white">هنوز هیچ پستی ثبت نشده</h2>
                    <p class="mx-auto mt-2 max-w-md text-white/60">
                        اینجا هنوز خالی است. اولین نفری باش که یک پست منتشر می‌کند!
                    </p>

                    @auth
                        <div class="mt-8">
                            <a href="{{ route('posts.create') }}" class="btn-create">
                                + نوشتن اولین پست
                            </a>
                        </div>
                    @endauth

                @endif
            </div>

        @endif

        <div class="mt-8">
            {{ $posts->links() }}
        </div>
    </section>

    <!-- دکمه‌ی بازگشت به بالا -->
    <button
        data-back-to-top
        type="button"
        aria-label="بازگشت به بالا"
        class="fixed bottom-6 end-6 hidden items-center justify-center
           w-12 h-12 rounded-full bg-indigo-600 text-white shadow-lg
           hover:bg-indigo-700 transition-all duration-300
           hover:scale-110"
    >
        ↑
    </button>


    {{-- پاپ‌آپ ورود برای مهمان‌ها --}}
    @include('posts._login-modal')

@endsection



<!-- Footer -->
