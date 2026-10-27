@extends('layout')

@section('title', 'home')

@section('content')

    <style>
        .carousel-track { scrollbar-width: none; scroll-snap-type: x mandatory; }
        .carousel-track::-webkit-scrollbar { display: none; }
        .carousel-card { scroll-snap-align: start; }
        .carousel-track { scroll-padding-inline: 1rem; overscroll-behavior-x: contain; }
        @media (min-width: 640px) { .carousel-track { scroll-padding-inline: 0; } }
        .carousel-track { cursor: grab; }
        .carousel-track.is-dragging { cursor: grabbing; scroll-snap-type: none; scroll-behavior: auto; user-select: none; }
        .carousel-track.is-dragging a { pointer-events: none; }
    </style>

    <section class="max-w-6xl mx-auto px-4 sm:px-6 py-8 sm:py-12">

        @forelse($sections as $section)
            @php($category = $section['category'])

            <div class="mb-10 sm:mb-14" data-carousel>

                {{-- تیتر کتگوری (سمت چپ) + دکمه‌های کاروسل --}}
                <div dir="ltr" class="flex items-center justify-between gap-3 sm:gap-4 mb-4 sm:mb-5">
                    <h2 class="min-w-0 flex-1 truncate text-xl sm:text-2xl md:text-3xl font-bold text-left">
                        <a href="{{ route('categories.show', $category) }}">
                            <span class="gradient-text">{{ $category->title }}</span>
                        </a>
                    </h2>

                    <div class="flex shrink-0 items-center gap-2">
                        {{-- «همه‌ی پست‌ها»: سمت راست، کنار دکمه‌های کاروسل --}}
                        <a href="{{ route('categories.show', $category) }}"
                           class="group me-1 inline-flex items-center gap-1.5 rounded-full border border-white/20 bg-white/10
                                  py-1 ps-3 pe-1.5 text-xs font-semibold text-violet-200 transition
                                  hover:border-cyan-300/60 hover:bg-white/20 hover:text-white"
                           title="مشاهده‌ی همه‌ی پست‌های {{ $category->title }}">
                            <span class="hidden sm:inline">همه‌ی پست‌ها</span>
                            <span class="grid h-6 w-6 place-items-center rounded-full bg-gradient-to-br from-violet-500/70 to-cyan-500/60
                                         transition-transform duration-300 group-hover:translate-x-1">
                                <svg class="h-3.5 w-3.5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none"
                                     viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/>
                                </svg>
                            </span>
                        </a>

                        <button type="button" class="btn-icon max-sm:!hidden" data-prev aria-label="Previous">
                            <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                 stroke-width="1.8" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5"/>
                            </svg>
                        </button>
                        <button type="button" class="btn-icon max-sm:!hidden" data-next aria-label="Next">
                            <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                 stroke-width="1.8" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/>
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- کاروسل: هر پست یک کارت --}}
                <div dir="ltr" data-track class="carousel-track -mx-4 flex gap-4 overflow-x-auto px-4 pb-3 sm:mx-0 sm:gap-5 sm:px-0">
                    @foreach($section['posts'] as $post)
                        <a href="{{ route('posts.show', $post) }}" draggable="false"
                           class="carousel-card glass rounded-3xl overflow-hidden shrink-0 w-[72vw] max-w-72 sm:w-72
                                  transition hover:bg-white/5 hover:-translate-y-1">

                            @if($post->image)
                                <img src="{{ Storage::url($post->image) }}" alt="{{ $post->title }}"
                                     loading="lazy" draggable="false" class="w-full h-40 object-cover">
                            @else
                                <div class="w-full h-40 flex items-center justify-center
                bg-gradient-to-br from-violet-500/40 via-fuchsia-500/25 to-cyan-400/30">
                                    <svg ...آیکن عکس... />
                                </div>
                            @endif

                            <div dir="auto" class="p-5">
                                <h3 class="text-lg font-bold text-white truncate">{{ $post->title }}</h3>
                                <p class="mt-2 text-sm text-white/60 break-words">
                                    {{ \Illuminate\Support\Str::limit(strip_tags((string) $post->description), 30) }}
                                </p>
                            </div>
                        </a>
                    @endforeach
                </div>

            </div>

        @empty

            <div class="glass rounded-3xl text-center text-white/60 py-14 px-6">
                هنوز پستی ثبت نشده.
            </div>

        @endforelse

    </section>

@endsection

@push('scripts')
    <script>
        document.querySelectorAll('[data-carousel]').forEach(function (box) {
            const track = box.querySelector('[data-track]');
            const step = function () { return Math.max(track.clientWidth * 0.8, 280); };

            box.querySelector('[data-prev]').addEventListener('click', function () {
                track.scrollBy({left: -step(), behavior: 'smooth'});
            });
            box.querySelector('[data-next]').addEventListener('click', function () {
                track.scrollBy({left: step(), behavior: 'smooth'});
            });

            // کشیدن با موس (روی لمس‌صفحه اسکرول بومی کار می‌کند)
            let down = false, moved = false, startX = 0, startLeft = 0;

            track.addEventListener('mousedown', function (e) {
                if (e.button !== 0) return;
                down = true; moved = false;
                startX = e.pageX; startLeft = track.scrollLeft;
            });

            window.addEventListener('mousemove', function (e) {
                if (!down) return;
                const dx = e.pageX - startX;
                if (!moved && Math.abs(dx) > 5) {
                    moved = true;
                    track.classList.add('is-dragging');
                }
                if (moved) track.scrollLeft = startLeft - dx;
            });

            window.addEventListener('mouseup', function () {
                if (!down) return;
                down = false;
                // pointer-events روی لینک‌ها تا بعد از رها کردن می‌ماند، تا کلیک اشتباهی ثبت نشود
                setTimeout(function () { track.classList.remove('is-dragging'); }, 0);
            });
        });
    </script>
@endpush
