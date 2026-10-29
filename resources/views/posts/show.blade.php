@extends('layout')
@section('title','Posts')
@section('content')

    <section class="max-w-5xl mx-auto px-6 py-12">
        <a href="{{ route('posts.index') }}" class="btn-glass">
            ← Back to Posts
        </a>
        <div class="space-y-5 mt-5"> <!-- Post 1 -->
            <article class="bg-white border border-gray-200 rounded-xl p-6 hover:border-indigo-300 hover:shadow-md transition">
                <div class="flex flex-col gap-4">
                    <div>
                        @if ($post->image)
                            <img src="{{ Storage::url($post->image) }}" alt="{{ $post->title }}"
                                 class="w-full h-64 object-cover rounded-xl mb-6">
                        @endif
                        <span
                            class="inline-block text-xs font-semibold bg-indigo-100 text-indigo-700 px-3 py-1 rounded-full">
                            {{$post->category->title ?? ''}}
                        </span>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900">
                        {{$post->title}}
                    </h2>
                    {{-- متن پست: HTML پاک‌سازی‌شده‌ی ادیتور (برای پست‌های قدیمی: متن ساده با خط‌شکن) --}}
                    <div class="rich-content">
                        {!! $post->body_html !!}
                    </div>
                    <p class="text-gray-500 leading-7">
                        Author: {{$post->user->name}}
                    </p>

                    {{-- لایک: فقط در همین صفحه قابل کلیک است --}}
                    <div class="flex items-center gap-3">
                        <button type="button" id="like-btn"
                                class="like-btn {{ $liked ? 'is-liked' : '' }}"
                                data-url="{{ route('posts.like', $post) }}"
                                data-auth="{{ auth()->check() ? '1' : '0' }}"
                                aria-pressed="{{ $liked ? 'true' : 'false' }}"
                                aria-label="Like">
                            <svg class="like-heart" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="1.8"
                                 stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z"/>
                            </svg>
                            <span id="like-count" class="like-count">{{ number_format($post->likes_count) }}</span>
                        </button>
                        <span id="like-error" class="text-xs text-rose-300 empty:hidden" role="status"></span>
                    </div>
                    @if($post->tags->isNotEmpty())
                        <div class="flex flex-wrap items-center gap-2 mt-4">
                            @foreach($post->tags as $tag)
                                <a
                                    href="{{ route('tags.show', $tag) }}"
                                    class="inline-flex items-center px-3 py-1.5
                       rounded-full text-sm font-medium
                       bg-purple-100 text-purple-700
                       hover:bg-purple-200 transition"
                                >
                                    #{{ $tag->title }}
                                </a>
                            @endforeach
                        </div>
                    @endif
                    @auth

                        <form
                            action="{{ route('comments.store', $post) }}"
                            method="POST"
                            class="mt-8"
                        >

                            @csrf

                            <label
                                for="body"
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                Add a comment
                            </label>

                            <textarea
                                name="body"
                                id="body"
                                rows="4"
                                placeholder="Write your comment..."
                                class="w-full px-4 py-3 border border-gray-300
                   rounded-lg resize-none
                   focus:outline-none focus:ring-2
                   focus:ring-indigo-500"
                            >{{ old('body') }}</textarea>

                            @error('body')
                            <p class="text-red-500 text-sm mt-2">
                                {{ $message }}
                            </p>
                            @enderror

                            <button type="submit" class="btn-create mt-3">
                                Post Comment
                            </button>

                        </form>
                    @else

                        <p class="mt-8 text-gray-500">
                            برای ثبت کامنت باید وارد حساب کاربری خود شوید.
                        </p>

                    @endauth
                    <section class="mt-12">

                        <h2 class="text-2xl font-bold text-gray-900 mb-6">
                            Comments
                        </h2>

                        @forelse($comments as $comment)

                            {{-- هر کامنت (و ریپلای‌هایش) در قالب comments/_item رندر می‌شود --}}
                            @include('comments._item', ['comment' => $comment, 'isReply' => false])

                        @empty

                            <p class="text-gray-400">
                                هنوز کامنتی برای این پست ثبت نشده.
                            </p>

                        @endforelse
                        <div class="mt-6">
                            {{$comments->links()}}
                        </div>

                    </section>
                    <div class="flex flex-row items-center gap-3 mt-2">

                        @can('update', $post)
                            <a href="{{ route('posts.edit', $post) }}"
                               class="btn-edit">
                                ویرایش
                            </a>
                        @endcan

                        @can('delete', $post)
                            <form action="{{ route('posts.destroy', $post) }}"
                                  method="POST"
                                  onsubmit="return confirm('مطمئنی می‌خوای این پست رو حذف کنی؟')">
                                @csrf
                                @method('DELETE')

                                <button type="submit"
                                        class="btn-delete">
                                    حذف
                                </button>
                            </form>
                        @endcan

                    </div>
                </div>
            </article>
        </div>
    </section>

    <style>
        /* ---------- دکمه‌ی لایک ---------- */
        .like-btn {
            display: inline-flex; align-items: center; gap: .55rem;
            padding: .5rem 1.1rem; border-radius: 9999px; cursor: pointer;
            color: rgb(244 241 255 / .75);
            background: rgb(255 255 255 / .08);
            border: 1px solid rgb(255 255 255 / .22);
            transition: background .2s, color .2s, border-color .2s, transform .2s;
        }
        .like-btn:hover { background: rgb(255 255 255 / .16); color: #fff; transform: translateY(-1px); }
        .like-btn:disabled { opacity: .7; cursor: progress; }
        .like-btn:focus-visible { outline: 2px solid rgb(167 139 250 / .9); outline-offset: 3px; }
        .like-heart { width: 1.4rem; height: 1.4rem; fill: none; transition: fill .2s, transform .2s; }
        .like-count { font-weight: 700; min-width: 1ch; font-variant-numeric: tabular-nums; }
        .like-btn.is-liked { color: #fb7185; background: rgb(244 63 94 / .14); border-color: rgb(244 63 94 / .45); }
        .like-btn.is-liked .like-heart { fill: currentColor; }
        .like-btn.pop .like-heart { animation: like-pop .45s cubic-bezier(.2, .9, .3, 1.6); }
        @keyframes like-pop { 0% { transform: scale(1); } 40% { transform: scale(1.45); } 100% { transform: scale(1); } }

        /* ---------- پاپ‌آپ ورود ---------- */
        .login-modal { position: fixed; inset: 0; z-index: 100; display: grid; place-items: center; padding: 1rem; }
        .login-modal[hidden] { display: none; }
        .login-modal-backdrop { position: absolute; inset: 0; background: rgb(8 4 24 / .65); backdrop-filter: blur(6px); animation: lm-fade .2s ease; }
        .login-modal-card {
            position: relative; width: 100%; max-width: 26rem; padding: 2.25rem 1.75rem 1.75rem;
            border-radius: 1.75rem; text-align: center; background: rgb(22 12 54 / .92);
            box-shadow: 0 30px 80px rgb(0 0 0 / .6); animation: lm-pop .28s cubic-bezier(.2, .9, .3, 1.2);
        }
        .login-modal-close {
            position: absolute; top: .9rem; inset-inline-end: .9rem; display: grid; place-items: center;
            width: 2.2rem; height: 2.2rem; border-radius: 9999px; color: rgb(244 241 255 / .7);
            background: rgb(255 255 255 / .08); transition: background .2s, color .2s;
        }
        .login-modal-close:hover { background: rgb(255 255 255 / .2); color: #fff; }
        .login-modal-icon {
            display: grid; place-items: center; width: 4.5rem; height: 4.5rem; margin: 0 auto; border-radius: 1.4rem; color: #fff;
            background: linear-gradient(135deg, #f43f5e, #8b5cf6); box-shadow: 0 12px 35px rgb(244 63 94 / .45);
            animation: float-soft 4s ease-in-out infinite;
        }
        @keyframes lm-fade { from { opacity: 0; } }
        @keyframes lm-pop { from { opacity: 0; transform: translateY(14px) scale(.94); } }
    </style>

    {{-- پاپ‌آپ «برای لایک باید وارد شوید» (فقط مهمان‌ها) --}}
    @guest
        <div id="login-modal" class="login-modal" role="dialog" aria-modal="true" aria-labelledby="login-modal-title" hidden>
            <div class="login-modal-backdrop" data-close></div>

            <div class="login-modal-card glass">
                <button type="button" class="login-modal-close" data-close aria-label="Close">
                    <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                         stroke-width="2" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/>
                    </svg>
                </button>

                <div class="login-modal-icon" aria-hidden="true">
                    <svg class="w-9 h-9" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M11.645 20.91l-.007-.003-.022-.012a15.247 15.247 0 0 1-.383-.218 25.18 25.18 0 0 1-4.244-3.17C4.688 15.36 2.25 12.174 2.25 8.25 2.25 5.322 4.714 3 7.688 3A5.5 5.5 0 0 1 12 5.052 5.5 5.5 0 0 1 16.313 3c2.973 0 5.437 2.322 5.437 5.25 0 3.925-2.438 7.111-4.739 9.256a25.175 25.175 0 0 1-4.244 3.17 15.247 15.247 0 0 1-.383.219l-.022.012-.007.004-.003.001a.752.752 0 0 1-.704 0l-.003-.001Z"/>
                    </svg>
                </div>

                <h2 id="login-modal-title" class="mt-5 text-2xl font-extrabold text-white">این پست را دوست داشتید؟</h2>
                <p class="mt-2 text-white/65 leading-7">
                    برای لایک کردن باید وارد حساب کاربری‌تان شوید یا ثبت‌نام کنید. فقط چند ثانیه طول می‌کشد!
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

    <script>
        // حالت ویرایش: متن و دکمه‌های همان کامنت پنهان و فرم ویرایش نمایش داده می‌شود
        function toggleEdit(id, editing) {
            document.getElementById('comment-view-' + id).classList.toggle('hidden', editing);
            document.getElementById('comment-actions-' + id).classList.toggle('hidden', editing);
            document.getElementById('comment-edit-' + id).classList.toggle('hidden', !editing);
        }

        function editComment(id) {
            toggleEdit(id, true);
        }

        function cancelEdit(id) {
            toggleEdit(id, false);
        }

        function replyToComment(id) {
            document.getElementById('reply-form-' + id).classList.toggle('hidden');
        }
    </script>

@endsection

@push('scripts')
    <script>
        (function () {
            const btn = document.getElementById('like-btn');
            if (!btn) return;

            const countEl = document.getElementById('like-count');
            const errorEl = document.getElementById('like-error');
            const modal = document.getElementById('login-modal');
            const csrf = @json(csrf_token());
            let busy = false;
            let lastFocus = null;

            // ---------- پاپ‌آپ ----------
            function openModal() {
                if (!modal) { window.location.href = @json(route('login')); return; }
                lastFocus = document.activeElement;
                modal.hidden = false;
                document.body.style.overflow = 'hidden';
                const first = modal.querySelector('a.btn-create');
                if (first) first.focus();
            }
            function closeModal() {
                if (!modal || modal.hidden) return;
                modal.hidden = true;
                document.body.style.overflow = '';
                if (lastFocus) lastFocus.focus();
            }
            if (modal) {
                modal.addEventListener('click', function (e) {
                    if (e.target.closest('[data-close]')) closeModal();
                });
                document.addEventListener('keydown', function (e) {
                    if (e.key === 'Escape') closeModal();
                });
            }

            // ---------- لایک ----------
            function render(liked, count) {
                btn.classList.toggle('is-liked', liked);
                btn.setAttribute('aria-pressed', liked ? 'true' : 'false');
                countEl.textContent = Number(count).toLocaleString('en-US');
                btn.classList.remove('pop');
                void btn.offsetWidth;            // ری‌استارت انیمیشن
                if (liked) btn.classList.add('pop');
            }

            btn.addEventListener('click', async function () {
                if (btn.dataset.auth !== '1') { openModal(); return; }   // مهمان
                if (busy) return;

                busy = true;
                btn.disabled = true;
                errorEl.textContent = '';

                try {
                    const res = await fetch(btn.dataset.url, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrf,
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                    });

                    if (res.status === 401) { btn.dataset.auth = '0'; openModal(); return; }
                    if (res.status === 419) { errorEl.textContent = 'نشست شما منقضی شده است؛ صفحه را رفرش کن.'; return; }
                    if (!res.ok) throw new Error('bad status');

                    const data = await res.json();
                    render(data.liked, data.count);
                } catch (e) {
                    errorEl.textContent = 'خطا در ثبت لایک، دوباره تلاش کن.';
                } finally {
                    busy = false;
                    btn.disabled = false;
                }
            });
        })();
    </script>
@endpush
