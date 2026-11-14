
@extends('layout')

@section('title', 'خانه')

@section('content')

    <x-seo title="خانه" description="وبلاگ فارسی؛ تازه‌ترین پست‌ها و مقاله‌ها به تفکیک دسته‌بندی." />

    <section class="home-section max-w-6xl mx-auto px-4 sm:px-6 py-8 sm:py-12">

        {{-- جستجوی سریع: همان پنجره‌ی جستجوی هدر را باز می‌کند --}}
        <button type="button" class="home-search" data-open-search aria-label="جستجو در پست‌ها">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/>
            </svg>
            <span>در پست‌ها جستجو کنید…</span>
            <kbd>/</kbd>
        </button>

        @forelse($sections as $section)

            @php($category = $section['category'])

            <div class="mb-12 sm:mb-16" data-carousel>

                {{-- عنوان دسته‌بندی و کنترل‌های کاروسل --}}
                <div class="section-heading">

                    <h2 class="section-title">
                        <a href="{{ route('categories.show', $category) }}">
                            {{ $category->title }}
                        </a>
                    </h2>

                    <div class="section-controls">

                        <a href="{{ route('categories.show', $category) }}"
                           class="all-posts-link"
                           title="مشاهده‌ی همه‌ی پست‌های {{ $category->title }}">

                            <span>همه‌ی پست‌ها</span>

                            <span class="all-posts-icon">
                            <svg class="w-3.5 h-3.5" xmlns="http://www.w3.org/2000/svg"
                                 fill="none" viewBox="0 0 24 24"
                                 stroke-width="2.2" stroke="currentColor"
                                 aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M10.5 4.5 3 12m0 0 7.5 7.5M3 12h18"/>
                            </svg>
                        </span>

                        </a>

                        <button type="button"
                                class="carousel-control max-sm:!hidden"
                                data-prev
                                aria-label="پست‌های قبلی">
                            <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg"
                                 fill="none" viewBox="0 0 24 24"
                                 stroke-width="1.8" stroke="currentColor"
                                 aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="m8.25 4.5 7.5 7.5-7.5 7.5"/>
                            </svg>



                        </button>

                        <button type="button"
                                class="carousel-control max-sm:!hidden"
                                data-next
                                aria-label="پست‌های بعدی">

                            <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg"
                                 fill="none" viewBox="0 0 24 24"
                                 stroke-width="1.8" stroke="currentColor"
                                 aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="m15.75 19.5-7.5-7.5 7.5-7.5"/>
                            </svg>

                        </button>

                    </div>

                </div>

                {{-- کاروسل پست‌ها --}}
                <div data-track
                     class="carousel-track">

                    @foreach($section['posts'] as $post)

                        <a href="{{ route('posts.show', $post) }}"
                           draggable="false"
                           class="carousel-card"
                           aria-label="مشاهده‌ی پست {{ $post->title }}">

                            {{-- تصویر پست --}}
                            <div class="card-image-wrapper">

                                @if($post->image)

                                    <img src="{{ $post->image_thumb_url }}"
                                         alt="{{ $post->title }}"
                                         width="640" height="400"
                                         loading="lazy" decoding="async"
                                         draggable="false"
                                         class="card-image">

                                    <div class="card-image-overlay"></div>

                                @else

                                    <div class="card-placeholder">

                                        <svg xmlns="http://www.w3.org/2000/svg"
                                             fill="none" viewBox="0 0 24 24"
                                             stroke-width="1.4" stroke="currentColor"
                                             aria-hidden="true">

                                            <path stroke-linecap="round"
                                                  stroke-linejoin="round"
                                                  d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909M3.75 21h16.5A1.5 1.5 0 0 0 21.75 19.5v-15A1.5 1.5 0 0 0 20.25 3H3.75A1.5 1.5 0 0 0 2.25 4.5v15A1.5 1.5 0 0 0 3.75 21Zm10.5-11.25h.008v.008h-.008V9.75Z"/>

                                        </svg>

                                    </div>

                                @endif

                            </div>

                            {{-- اطلاعات پست --}}
                            <div class="card-content">

                                <h3 class="card-title" dir="auto">
                                    {{ $post->title }}
                                </h3>

                                <p class="card-description" dir="auto">
                                    {{ \Illuminate\Support\Str::limit(strip_tags((string) $post->description), 100) }}
                                </p>

                                <div class="card-footer">

                                <span class="card-read-more">

                                    ادامه‌ی مطلب

                                    <svg class="w-3.5 h-3.5"
                                         xmlns="http://www.w3.org/2000/svg"
                                         fill="none" viewBox="0 0 24 24"
                                         stroke-width="2" stroke="currentColor"
                                         aria-hidden="true">

                                        <path stroke-linecap="round"
                                              stroke-linejoin="round"
                                              d="M10.5 4.5 3 12m0 0 7.5 7.5M3 12h18"/>

                                    </svg>

                                </span>

                                    {{-- تعداد لایک --}}
                                    <span class="card-likes"
                                          title="{{ number_format($post->likes_count) }} لایک">

                                    <svg xmlns="http://www.w3.org/2000/svg"
                                         viewBox="0 0 24 24"
                                         stroke-width="1.8"
                                         stroke="currentColor"
                                         fill="{{ $post->likes_count ? 'currentColor' : 'none' }}"
                                         aria-hidden="true">

                                        <path stroke-linecap="round"
                                              stroke-linejoin="round"
                                              d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z"/>

                                    </svg>

                                    <span class="font-semibold tabular-nums">
                                        {{ number_format($post->likes_count) }}
                                    </span>

                                </span>

                                </div>

                            </div>

                        </a>

                    @endforeach

                </div>

            </div>

        @empty

            <div class="empty-posts">

                <div class="empty-icon">

                    <svg xmlns="http://www.w3.org/2000/svg"
                         fill="none" viewBox="0 0 24 24"
                         stroke-width="1.5" stroke="currentColor"
                         aria-hidden="true">

                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              d="M12 6v12m6-6H6"/>

                    </svg>

                </div>

                <h3>هنوز مطلبی منتشر نشده است</h3>

                <p>به‌زودی مطالب جدیدی در این بخش قرار می‌گیرند.</p>

            </div>

        @endforelse

    </section>

@endsection

@push('scripts')
@endpush
