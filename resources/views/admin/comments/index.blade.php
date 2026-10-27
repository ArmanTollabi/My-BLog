{{-- FILE: resources/views/admin/comments/index.blade.php --}}
@extends('admin.layout')

@section('title', 'Comments')

@section('admin-content')

    <section>

        <div class="mb-6">
            <h1 class="text-3xl md:text-4xl font-bold gradient-text">مدیریت کامنت‌ها</h1>
            <p class="text-white/55 mt-2">همه‌ی کامنت‌ها با وضعیت، نویسنده و پست مربوطه</p>
        </div>

        @include('admin.comments.tabs', ['counts' => $counts, 'active' => $status ?: 'all'])

        {{-- جستجو --}}
        <form action="{{ route('admin.comments.index') }}" method="GET" class="mb-8 flex flex-wrap items-center gap-3">
            @if($status)
                <input type="hidden" name="status" value="{{ $status }}">
            @endif

            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="جستجو در متن کامنت یا نام کاربر..."
                   aria-label="Search comments" class="glass-input h-12 min-w-56 flex-1">

            <button type="submit" class="btn-create !h-12">Search</button>
        </form>

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

                            @if($comment->isApproved())
                                <span class="inline-block rounded-full border !border-emerald-400/40 bg-emerald-400/15
                                             px-2.5 py-0.5 text-xs text-emerald-200">تایید شده</span>
                            @else
                                <span class="inline-block rounded-full border !border-amber-400/40 bg-amber-400/15
                                             px-2.5 py-0.5 text-xs text-amber-200">در انتظار تایید</span>
                            @endif

                            @if($comment->parent_id)
                                <span class="inline-block rounded-full border border-white/15 bg-white/10
                                             px-2.5 py-0.5 text-xs text-white/70">ریپلای</span>
                            @endif
                        </div>

                        <p class="text-sm text-white/55 mt-1">
                            {{ $comment->created_at->format('Y-m-d H:i') }}
                            ·
                            در پست
                            @if($comment->post)
                                <a href="{{ route('posts.show', $comment->post) }}#comment-{{ $comment->id }}"
                                   class="text-violet-300 hover:underline">{{ $comment->post->title }}</a>
                            @else
                                <span>حذف‌شده</span>
                            @endif
                        </p>

                        <p class="mt-3 text-white/85 break-words">{{ $comment->body }}</p>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="flex flex-wrap items-center justify-end gap-2 mt-4">

                    @if($comment->isApproved())
                        <form action="{{ route('admin.comments.unapprove', $comment) }}" method="POST">
                            @csrf @method('PATCH')
                            <button type="submit" class="btn-edit btn-sm">لغو تایید</button>
                        </form>
                    @else
                        <form action="{{ route('admin.comments.approve', $comment) }}" method="POST">
                            @csrf @method('PATCH')
                            <button type="submit" class="btn-create btn-sm">تایید</button>
                        </form>
                    @endif

                    <form action="{{ route('admin.comments.destroy', $comment) }}" method="POST"
                          onsubmit="return confirm('این کامنت به سطل زباله منتقل شود؟')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn-delete btn-sm">انتقال به سطل زباله</button>
                    </form>

                </div>

            </article>

        @empty

            <div class="glass rounded-3xl text-center text-white/60 py-14 px-6">
                کامنتی پیدا نشد.
            </div>

        @endforelse

        <div class="mt-8">
            {{ $comments->links() }}
        </div>

    </section>

@endsection
