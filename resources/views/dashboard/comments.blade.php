@extends('dashboard.layout')

@section('title', 'کامنت‌های شما')
@section('dash-title', 'کامنت‌های شما')
@section('dash-subtitle', 'وضعیت هر کامنت و پستی که زیرش نظر گذاشته‌اید.')

@section('dash-content')

    {{-- کامنت‌های شما --}}
    <div class="glass rounded-3xl p-5 sm:p-8">

        <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="text-xl font-bold text-white">کامنت‌های شما</h2>
            </div>

            <div class="flex items-center gap-2 text-xs">
                <span class="inline-flex items-center gap-1.5 rounded-full border border-emerald-400/30 bg-emerald-400/10 px-3 py-1 text-emerald-200">
                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-400"></span>
                    تایید شده: {{ $commentCounts[\App\Enums\CommentStatus::Approved->value] ?? 0 }}
                </span>
                <span class="inline-flex items-center gap-1.5 rounded-full border border-amber-400/30 bg-amber-400/10 px-3 py-1 text-amber-200">
                    <span class="h-1.5 w-1.5 rounded-full bg-amber-400"></span>
                    در انتظار: {{ $commentCounts[\App\Enums\CommentStatus::Pending->value] ?? 0 }}
                </span>
            </div>
        </div>

        <div class="space-y-3">
            @forelse($comments as $comment)
                @php($approved = $comment->isApproved())

                <article class="group relative rounded-2xl border border-white/10 border-s-4 bg-white/5 p-4 transition
                                hover:-translate-y-0.5 hover:bg-white/10
                                {{ $approved ? 'border-s-emerald-400/70' : 'border-s-amber-400/70' }}">

                    <div class="flex flex-wrap items-center justify-between gap-2">
                        @if($approved)
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-400/15 px-2.5 py-1 text-xs font-semibold text-emerald-200">
                                <svg class="h-3.5 w-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                     stroke-width="2.2" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/>
                                </svg>
                                تایید شده
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-400/15 px-2.5 py-1 text-xs font-semibold text-amber-200">
                                <svg class="h-3.5 w-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                     stroke-width="2" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                                </svg>
                                در انتظار تایید
                            </span>
                        @endif

                        <span class="text-xs text-white/45">{{ $comment->created_at_with_hours_fa }}</span>
                    </div>

                    <p dir="auto" class="mt-3 break-words leading-7 text-white/90 line-clamp-3">
                        {{ $comment->body }}
                    </p>

                    <div class="mt-3 flex items-center justify-between gap-3 text-sm">
                        @if($comment->post)
                            {{-- لینک کل کارت (stretched link) به پستی که زیرش کامنت گذاشته‌اید --}}
                            <a href="{{ route('posts.show', $comment->post) }}#comment-{{ $comment->id }}"
                               class="inline-flex min-w-0 items-center gap-2 font-semibold text-violet-300 transition
                                      group-hover:text-cyan-300 after:absolute after:inset-0 after:rounded-2xl">
                                <span class="shrink-0 text-white/45">در پست:</span>
                                <span class="truncate">{{ $comment->post->title }}</span>
                                <svg class="h-4 w-4 shrink-0 rotate-180 transition group-hover:-translate-x-1" xmlns="http://www.w3.org/2000/svg"
                                     fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/>
                                </svg>
                            </a>
                        @else
                            <span class="text-white/45">پست حذف شده است.</span>
                        @endif
                    </div>

                    @unless($approved)
                        <p class="mt-2 text-xs text-amber-200/70">
                            تا زمانی که ادمین تایید نکند، این کامنت فقط برای خودتان قابل مشاهده است.
                        </p>
                    @endunless
                </article>
            @empty
                <div class="rounded-2xl border border-dashed border-white/15 py-10 text-center text-white/55">
                    هنوز کامنتی نگذاشته‌اید.
                </div>
            @endforelse
        </div>

        @if($comments->hasPages())
            <div class="mt-5">
                {{ $comments->links() }}
            </div>
        @endif
    </div>

@endsection
