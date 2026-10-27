@extends('layout')

@section('title', $category->title)

@section('content')

    <div class="max-w-6xl mx-auto px-6 py-8">

        <div class="mb-8">

            <a
                href="{{ route('categories.index') }}"
                class="inline-flex items-center gap-1 px-4 py-1.5 text-sm font-semibold
                       text-violet-200 rounded-full
                       bg-white/10 border border-white/20
                       hover:bg-white/20 transition"
            >
                ← Back to Categories
            </a>

            <h1 class="text-3xl font-extrabold text-white mt-5">
                {{ $category->title }}
            </h1>

            <p class="text-white/60 mt-1">
                Posts in this category
                <span class="inline-block ms-2 px-2.5 py-0.5 rounded-full text-xs
                             bg-white/10 border border-white/15 text-white/70">
                    {{ $category->posts->count() }} posts
                </span>
            </p>

        </div>

        @if($category->posts->isNotEmpty())

            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">

                @foreach($category->posts as $post)

                    <a href="{{ route('posts.show', $post) }}"
                       class="glass-card group !p-0 overflow-hidden flex flex-col">

                        {{-- کاور: عکس پست یا طرح پیش‌فرض --}}
                        <div class="relative aspect-[16/10] overflow-hidden">

                            @if($post->image)
                                <img src="{{ Storage::url($post->image) }}"
                                     alt="{{ $post->title }}"
                                     loading="lazy"
                                     class="w-full h-full object-cover transition duration-500 group-hover:scale-105">
                            @else
                                <div class="w-full h-full flex items-center justify-center
                                            bg-gradient-to-br from-violet-600/60 via-fuchsia-500/40 to-cyan-500/50">
                                    <div class="absolute -top-8 -start-8 w-32 h-32 rounded-full bg-white/15 blur-2xl"></div>
                                    <div class="absolute -bottom-10 -end-6 w-40 h-40 rounded-full bg-cyan-300/20 blur-2xl"></div>

                                    <svg class="relative w-14 h-14 text-white/70" xmlns="http://www.w3.org/2000/svg"
                                         fill="none" viewBox="0 0 24 24" stroke-width="1.4" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                              d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909M3.75 21h16.5A1.5 1.5 0 0 0 21.75 19.5v-15A1.5 1.5 0 0 0 20.25 3H3.75A1.5 1.5 0 0 0 2.25 4.5v15A1.5 1.5 0 0 0 3.75 21Zm10.5-11.25h.008v.008h-.008V9.75Z"/>
                                    </svg>
                                </div>
                            @endif

                            <div class="absolute inset-x-0 bottom-0 h-1/3 bg-gradient-to-t from-black/40 to-transparent"></div>
                        </div>

                        <div class="p-5 flex flex-col flex-1">

                            <h2 class="text-lg font-bold text-white group-hover:text-violet-300 transition line-clamp-2">
                                {{ $post->title }}
                            </h2>

                            <p class="text-white/60 text-sm leading-7 mt-2 line-clamp-3">
                                {{ $post->description }}
                            </p>

                            <p class="text-xs text-white/45 mt-auto pt-4">
                                By {{ $post->user->name ?? '—' }}
                                · {{ $post->created_at->format('Y/m/d') }}
                            </p>

                        </div>

                    </a>

                @endforeach

            </div>

        @else

            <div class="glass rounded-3xl p-10 text-center text-white/60">
                No posts found in this category.
            </div>

        @endif

    </div>

@endsection
