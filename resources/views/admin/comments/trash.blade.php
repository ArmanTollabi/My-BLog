{{-- FILE: resources/views/admin/comments/trash.blade.php --}}
@extends('admin.layout')

@section('title', 'Comments Trash')

@section('admin-content')

    <section>

        <div class="mb-6">
            <h1 class="text-3xl md:text-4xl font-bold gradient-text">سطل زباله‌ی کامنت‌ها</h1>
            <p class="text-white/55 mt-2">کامنت‌های حذف‌شده را بازیابی کن یا برای همیشه از دیتابیس پاک کن</p>
        </div>

        @include('admin.comments.tabs', ['counts' => $counts, 'active' => 'trash'])

        @forelse($comments as $comment)

            <article class="glass rounded-3xl p-5 mb-4">

                <div class="flex items-start gap-4">

                    <span class="user-avatar is-lg shrink-0">
                        @if($comment->user?->avatar)
                            <img src="{{ Storage::url($comment->user->avatar) }}" alt="">
                        @else
                            {{ mb_strtoupper(mb_substr($comment->user?->name ?? '?', 0, 1)) }}
                        @endif
                    </span>

                    <div class="min-w-0 flex-1">

                        <div class="flex flex-wrap items-center gap-x-3 gap-y-1">
                            <h2 class="font-bold text-white">{{ $comment->user?->name ?? 'کاربر حذف‌شده' }}</h2>

                            <span class="inline-block rounded-full border !border-rose-400/40 bg-rose-400/15
                                         px-2.5 py-0.5 text-xs text-rose-200">در سطل زباله</span>

                            @if($comment->parent_id)
                                <span class="inline-block rounded-full border border-white/15 bg-white/10
                                             px-2.5 py-0.5 text-xs text-white/70">ریپلای</span>
                            @endif
                        </div>

                        <p class="text-sm text-white/55 mt-1">
                            ثبت: {{ $comment->created_at->format('Y-m-d H:i') }}
                            · حذف: {{ $comment->deleted_at->format('Y-m-d H:i') }}
                            · در پست
                            @if($comment->post)
                                <a href="{{ route('posts.show', $comment->post) }}"
                                   class="text-violet-300 hover:underline">{{ $comment->post->title }}</a>
                            @else
                                <span>حذف‌شده</span>
                            @endif
                        </p>

                        <p class="mt-3 text-white/85 break-words">{{ $comment->body }}</p>
                    </div>
                </div>

                <div class="flex flex-wrap items-center justify-end gap-2 mt-4">

                    <form action="{{ route('admin.comments.restore', $comment) }}" method="POST">
                        @csrf @method('PATCH')
                        <button type="submit" class="btn-update btn-sm">بازیابی</button>
                    </form>

                    <form action="{{ route('admin.comments.force', $comment) }}" method="POST"
                          onsubmit="return confirm('این کامنت (و ریپلای‌هایش) برای همیشه از دیتابیس حذف می‌شود. ادامه می‌دهی؟')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn-delete btn-sm">حذف همیشگی</button>
                    </form>

                </div>

            </article>

        @empty

            <div class="glass rounded-3xl text-center text-white/60 py-14 px-6">
                سطل زباله خالی است.
            </div>

        @endforelse

        <div class="mt-8">
            {{ $comments->links() }}
        </div>

    </section>

@endsection
