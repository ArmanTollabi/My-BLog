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
