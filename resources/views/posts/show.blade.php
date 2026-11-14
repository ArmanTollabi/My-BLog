@extends('layout')
@section('title', $post->title)
@section('content')

    <x-seo :title="$post->title" :description="$post->description" :image="$post->image_url" type="article"
           :published="$post->published_at ?? $post->created_at" :modified="$post->updated_at" :author="$post->user?->name" :noindex="! $post->isPublic()" />

    {{-- نوار پیشرفت مطالعه (رفتار: resources/js/reading-progress.js) --}}
    <div class="ps-progress" data-reading-progress aria-hidden="true"><span></span></div>

    <article class="ps">

        {{-- هشدار برای پست منتشرنشده (فقط نویسنده/ادمین به این صفحه می‌رسند) --}}
        @unless($post->isPublic())
            <div class="ps-wrap">
                <div class="mb-4 flex flex-wrap items-center gap-x-3 gap-y-1 rounded-2xl border border-amber-300/30 bg-amber-400/10 px-4 py-3 text-sm text-amber-100" role="status">
                    <x-post-status :post="$post" />
                    <span>
                        @if($post->effective_status === \App\Enums\PostStatus::Scheduled)
                            این پست در {{ $post->published_at_with_hours_fa }} منتشر می‌شود.
                        @else
                            این پست برای بازدیدکنندگان نمایش داده نمی‌شود؛ فقط تو و مدیران سایت آن را می‌بینید.
                        @endif
                    </span>
                    @can('update', $post)
                        <a href="{{ route('posts.edit', $post) }}" class="ms-auto font-bold text-violet-200 hover:underline">ویرایش و تغییر وضعیت</a>
                    @endcan
                </div>
            </div>
        @endunless

        {{-- ===== ۱) عنوان ===== --}}
        <div class="ps-wrap ps-head">
            <h1 class="ps-title">{{ $post->title }}</h1>

            {{-- ===== ۲) مسیر صفحه (breadcrumb) ===== --}}
            <x-breadcrumbs :items="array_filter([
                ['label' => 'خانه', 'url' => route('home')],
                ['label' => 'پست‌ها', 'url' => route('posts.index')],
                $post->category ? ['label' => $post->category->title, 'url' => route('categories.show', $post->category)] : null,
                ['label' => $post->title],
            ])" />
        </div>

        {{-- ===== ۳) تصویر شاخص ===== --}}
        @if($post->image)
            <figure class="ps-cover">
                <img src="{{ $post->image_url }}" alt="{{ $post->title }}" width="1200" height="750" fetchpriority="high" decoding="async">
            </figure>
        @endif

        <div class="ps-wrap ps-layout">

            {{-- ===== ستون کناری (دسکتاپ: سمت چپ و چسبان | موبایل: فهرست زیر اطلاعات، دسته‌ها آخر صفحه) ===== --}}
            <aside class="ps-side">

                {{-- فهرست مطالب: توسط resources/js/post-toc.js از h1..h3 متن ساخته و نمایش داده می‌شود --}}
                <details class="ps-toc ps-box glass" data-toc hidden>
                    <summary class="ps-box-title">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0ZM3.75 12h.007v.008H3.75V12Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm-.375 5.25h.007v.008H3.75v-.008Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z"/></svg>
                        فهرست مطالب
                    </summary>
                    <ol class="ps-toc-list" data-toc-list></ol>
                </details>

                @if($categories->isNotEmpty())
                    <nav class="ps-cats ps-box glass" aria-labelledby="ps-cats-title">
                        <h2 id="ps-cats-title" class="ps-box-title">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12.75V12A2.25 2.25 0 0 1 4.5 9.75h15A2.25 2.25 0 0 1 21.75 12v.75m-8.69-6.44-2.12-2.12a1.5 1.5 0 0 0-1.061-.44H4.5A2.25 2.25 0 0 0 2.25 6v12a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9a2.25 2.25 0 0 0-2.25-2.25h-5.379a1.5 1.5 0 0 1-1.06-.44Z"/></svg>
                            دسته‌بندی‌ها
                        </h2>
                        <ul class="ps-cats-list">
                            @foreach($categories as $cat)
                                <li>
                                    <a href="{{ route('categories.show', $cat) }}"
                                       class="{{ $cat->id === $post->category_id ? 'is-current' : '' }}"
                                       @if($cat->id === $post->category_id) aria-current="true" @endif>
                                        <span>{{ $cat->title }}</span>
                                        <span class="ps-cats-count">{{ \App\Support\Fa::number($cat->posts_count) }}</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </nav>
                @endif
            </aside>

            <div class="ps-main">

            {{-- ===== ۴) نویسنده، تاریخ، زمان مطالعه، بازدید ===== --}}
            <div class="ps-meta">
                @include('posts._meta', ['post' => $post, 'size' => 'lg'])

                <ul class="ps-facts" aria-label="اطلاعات پست">
                    <li title="زمان مطالعه">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                        {{ $post->reading_time_fa }} دقیقه مطالعه
                    </li>
                    <li title="تعداد بازدید">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/></svg>
                        {{ $post->views_fa }} بازدید
                    </li>
                    <li title="تعداد لایک">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z"/></svg>
                        {{ \App\Support\Fa::number($post->likes_count) }}
                    </li>
                </ul>
            </div>

            {{-- ===== ۵) متن پست (HTML پاک‌سازی‌شده‌ی ادیتور؛ پست‌های قدیمی: متن ساده با خط‌شکن) ===== --}}
            <div class="ps-body rich-content" data-reading-body>
                {!! $post->body_html !!}
            </div>

            @if($post->tags->isNotEmpty())
                <div class="ps-tags" aria-label="تگ‌ها">
                    @foreach($post->tags as $tag)
                        <a href="{{ route('tags.show', $tag) }}" class="ps-tag">#{{ $tag->title }}</a>
                    @endforeach
                </div>
            @endif

            {{-- لایک / ذخیره / مدیریت --}}
            <div class="ps-actions glass">
                <button type="button" data-toggle-btn data-kind="like"
                        class="like-btn {{ $liked ? 'is-liked' : '' }}"
                        data-url="{{ route('posts.like', $post) }}"
                        data-auth="{{ auth()->check() ? '1' : '0' }}"
                        aria-pressed="{{ $liked ? 'true' : 'false' }}" aria-label="لایک">
                    <svg class="like-heart" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z"/>
                    </svg>
                    <span data-like-count class="like-count">{{ number_format($post->likes_count) }}</span>
                </button>

                <button type="button" data-toggle-btn data-kind="save"
                        class="like-btn save-btn {{ $saved ? 'is-saved' : '' }}"
                        data-url="{{ route('posts.save', $post) }}"
                        data-auth="{{ auth()->check() ? '1' : '0' }}"
                        aria-pressed="{{ $saved ? 'true' : 'false' }}" aria-label="ذخیره">
                    <svg class="like-heart save-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M17.593 3.322c1.1.128 1.907 1.077 1.907 2.185V21L12 17.25 4.5 21V5.507c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0 1 11.186 0Z"/>
                    </svg>
                    <span class="like-count">ذخیره</span>
                </button>

                @canany(['update', 'delete'], $post)
                    <div class="ps-admin">
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

                <span data-toggle-error class="ps-error text-xs text-rose-300 empty:hidden" role="status"></span>
            </div>

            {{-- ===== ۶) دیدگاه‌ها ===== --}}
            <section class="ps-comments" aria-labelledby="comments-title">
                <h2 id="comments-title" class="ps-section-title">
                    دیدگاه‌ها <span class="ps-count">{{ \App\Support\Fa::number($post->approved_comments_count) }}</span>
                </h2>

                @auth
                    <form action="{{ route('comments.store', $post) }}" method="POST" class="ps-form glass">
                        @csrf
                        <label for="body" class="field-label">دیدگاه خود را بنویسید</label>
                        <textarea name="body" id="body" rows="4" required class="field"
                                  placeholder="نظر، سؤال یا تجربه‌ی خود را بنویسید…">{{ old('body') }}</textarea>
                        @error('body')
                            <p class="mt-2 text-sm text-rose-300">{{ $message }}</p>
                        @enderror
                        <button type="submit" class="btn-create mt-3">ثبت دیدگاه</button>
                    </form>
                @else
                    <p class="ps-login-hint glass">
                        برای ثبت دیدگاه باید
                        <a href="{{ route('login') }}" class="font-bold text-violet-300 hover:text-violet-200">وارد حساب کاربری</a>
                        خود شوید.
                    </p>
                @endauth

                <div class="mt-6">
                    @forelse($comments as $comment)
                        @include('comments._item', ['comment' => $comment, 'isReply' => false])
                    @empty
                        <p class="py-8 text-center text-white/45">هنوز دیدگاهی ثبت نشده؛ اولین نفر باش!</p>
                    @endforelse
                </div>

                <div class="mt-6">{{ $comments->links() }}</div>
            </section>

            </div>{{-- /.ps-main --}}
        </div>{{-- /.ps-layout --}}
    </article>

    @include('posts._login-modal')

@endsection
