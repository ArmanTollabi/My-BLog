@extends('layout')
@section('title','Posts')
@section('content')
    <!--<x-alert type="success"/>
<x-alert type="error"/>--->
    <!-- Posts List -->
    <section class="max-w-6xl mx-auto px-6 py-12">
        <form
            action="{{ route('posts.index') }}"
            method="GET"
            class="mb-6 flex flex-wrap gap-3"
        >

            {{-- Search --}}
            <input
                type="text"
                name="search"
                value="{{ request('search') }}"
                placeholder="Search posts..."
                class="px-4 py-2 border border-gray-300 rounded-lg"
            >

            {{-- Sort --}}
            <select
                name="sort"
                class="px-4 py-2 border border-gray-300 rounded-lg"
            >
                <option value="">
                    Sort...
                </option>
                <option value="latest"
                    {{ request('sort') === 'latest' ? 'selected' : '' }}>
                    Newest
                </option>

                <option value="oldest"
                    {{ request('sort') === 'oldest' ? 'selected' : '' }}>
                    Oldest
                </option>
            </select>

            {{-- Category --}}
            <select
                name="category"
                class="px-4 py-2 border border-gray-300 rounded-lg"
            >
                <option value="">
                    All Categories
                </option>

                @foreach($categories as $category)

                    <option
                        value="{{ $category->id }}"
                        {{ request('category') == $category->id ? 'selected' : '' }}
                    >
                        {{ $category->title }}
                    </option>

                @endforeach
            </select>
            <select
                name="tag"
                class="px-4 py-2 border border-gray-300 rounded-lg"
            >
                <option value="">
                    All
                </option>

                @foreach($tags as $tag)

                    <option
                        value="{{ $tag->id }}"
                        {{ request('tag') == $tag->id ? 'selected' : '' }}
                    >
                        {{ $tag->title }}
                    </option>

                @endforeach
            </select>

            <button
                type="submit"
                class=" glass-btn-primary px-5 py-2 bg-indigo-600 text-white rounded-lg"
            >
                Filter
            </button>

        </form>
        @if($posts->isNotEmpty())
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach($posts as $post)
                    <article class="glass-card group !p-0 overflow-hidden flex flex-col">

                        {{-- کاور: عکس پست یا طرح پیش‌فرض --}}
                        <a href="{{ route('posts.show', $post) }}" class="relative block aspect-[16/10] overflow-hidden">
                            @if($post->image)
                                <img src="{{ Storage::url($post->image) }}" alt="{{ $post->title }}" loading="lazy"
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
                                {{ $post->category->title ?? 'no category' }}
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
                                @can('isAdmin', $post->user)
                                    <div class="flex flex-wrap gap-2 mt-3">
                                        @foreach($post->tags as $tag)
                                            <a href="{{ route('tags.show', $tag) }}"
                                               class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                      bg-purple-100 text-purple-700 hover:bg-purple-200 transition">
                                                #{{ $tag->title }}
                                            </a>
                                        @endforeach
                                    </div>
                                @endcan
                            @endif

                            <p class="text-xs text-white/45 mt-auto pt-4">
                                نوشته {{ $post->user->name }} · {{ $post->created_at->format('Y-m-d') }}
                            </p>

                            @canany(['update', 'delete'], $post)
                                <div class="flex flex-row items-center gap-3 mt-4 pt-4 border-t border-white/10">
                                    @can('update', $post)
                                        <a href="{{ route('posts.edit', $post) }}" class="btn-edit">ویرایش</a>
                                    @endcan

                                    @can('delete', $post)
                                        <form action="{{ route('posts.destroy', $post) }}" method="POST"
                                              onsubmit="return confirm('مطمئنی می‌خوای این پست رو حذف کنی؟')">
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
            @php
                // فیلترهای فعال (هرکدام یک چیپ قابل‌حذف می‌شود)
                $activeFilters = [];

                if (request()->filled('search')) {
                    $activeFilters[] = ['key' => 'search', 'kind' => 'جستجو', 'label' => request('search')];
                }
                if (request()->filled('category')) {
                    $selectedCategory = $categories->firstWhere('id', (int) request('category'));
                    if ($selectedCategory) {
                        $activeFilters[] = ['key' => 'category', 'kind' => 'کتگوری', 'label' => $selectedCategory->title];
                    }
                }
                if (request()->filled('tag')) {
                    $selectedTag = $tags->firstWhere('id', (int) request('tag'));
                    if ($selectedTag) {
                        $activeFilters[] = ['key' => 'tag', 'kind' => 'تگ', 'label' => '#' . $selectedTag->title];
                    }
                }
            @endphp

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
                            <a href="{{ route('posts.index', request()->except($filter['key'], 'page')) }}"
                               class="group inline-flex max-w-full items-center gap-2 rounded-full border border-white/20
                                          bg-white/10 py-1.5 pl-3 pr-2 text-sm text-white/85 transition
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

    <!-- Back to Top Button -->
    <button
        id="backToTop"
        class="fixed bottom-6 right-6 hidden items-center justify-center
           w-12 h-12 rounded-full bg-indigo-600 text-white shadow-lg
           hover:bg-indigo-700 transition-all duration-300
           hover:scale-110"
    >
        ↑
    </button>

    <script>
        const backToTop = document.getElementById('backToTop');

        window.addEventListener('scroll', function () {

            if (window.scrollY > 950) {
                backToTop.classList.remove('hidden');
                backToTop.classList.add('flex');
            } else {
                backToTop.classList.add('hidden');
                backToTop.classList.remove('flex');
            }

        });

        backToTop.addEventListener('click', function () {

            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });

        });
    </script>
@endsection



<!-- Footer -->
