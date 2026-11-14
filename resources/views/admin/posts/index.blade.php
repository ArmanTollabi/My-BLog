{{-- FILE: resources/views/admin/posts/index.blade.php --}}
@extends('admin.layout')

@section('title', 'مدیریت پستها')

@section('admin-content')

    @php
        $tabs = [
            ['key' => null,        'label' => 'همه',          'count' => $counts['all']],
            ['key' => 'published', 'label' => 'منتشرشده',     'count' => $counts['published']],
            ['key' => 'draft',     'label' => 'پیش‌نویس',     'count' => $counts['draft']],
            ['key' => 'scheduled', 'label' => 'زمان‌بندی‌شده', 'count' => $counts['scheduled']],
            ['key' => 'archived',  'label' => 'بایگانی‌شده',   'count' => $counts['archived']],
        ];
    @endphp

    <section>

        <div class="mb-6 flex flex-wrap items-end justify-between gap-4">
            <div>
                <h1 class="text-3xl md:text-4xl font-bold gradient-text">مدیریت پست‌ها</h1>
                <p class="text-white/55 mt-2">همه‌ی پست‌ها با هر وضعیت انتشار</p>
            </div>
            <a href="{{ route('posts.create') }}" class="btn-create">+ پست جدید</a>
        </div>

        {{-- تب‌های وضعیت --}}
        <div class="mb-5 flex flex-wrap gap-2" role="navigation" aria-label="وضعیت پست‌ها">
            @foreach($tabs as $tab)
                @php($on = $status === $tab['key'])
                <a href="{{ route('admin.posts.index', array_filter(['status' => $tab['key'], 'search' => request('search')])) }}"
                   class="rounded-full border px-4 py-1.5 text-sm transition
                          {{ $on ? 'border-violet-300/60 bg-violet-500/30 text-white font-bold' : 'border-white/15 bg-white/5 text-white/70 hover:bg-white/10' }}"
                   @if($on) aria-current="page" @endif>
                    {{ $tab['label'] }}
                    <span class="ms-1 text-xs text-white/50">{{ \App\Support\Fa::number($tab['count']) }}</span>
                </a>
            @endforeach
        </div>

        <form action="{{ route('admin.posts.index') }}" method="GET" class="mb-8 flex flex-wrap items-center gap-3">
            @if($status)
                <input type="hidden" name="status" value="{{ $status }}">
            @endif
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="جستجو در عنوان پست‌ها..." aria-label="Search posts" class="glass-input h-12 min-w-56 flex-1">
            <button type="submit" class="btn-create !h-12">جستجو</button>
        </form>

        @forelse($posts as $post)

            <article class="glass rounded-3xl p-5 mb-4">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">

                    <div class="min-w-0">
                        <h2 class="flex items-center gap-3 text-lg font-bold text-white">
                            <a href="{{ route('posts.show', $post) }}" class="truncate hover:underline">{{ $post->title }}</a>
                            <x-post-status :post="$post" class="shrink-0" />
                        </h2>

                        <div class="flex flex-wrap items-center gap-2 mt-2 text-xs">
                            @if($post->category)
                                <span class="rounded-full border border-white/15 bg-white/10 px-2.5 py-0.5 text-white/75">
                                    {{ $post->category->title }}
                                </span>
                            @endif
                            <span class="text-white/55">{{ $post->user?->name ?? 'کاربر حذف‌شده' }}</span>
                            <span class="text-white/35">·</span>
                            <span class="text-white/55">ساخت: {{ $post->created_at_fa }}</span>

                            @if($post->effective_status === \App\Enums\PostStatus::Scheduled)
                                <span class="text-white/35">·</span>
                                <span class="text-sky-200">انتشار: {{ $post->published_at_with_hours_fa }}</span>
                            @elseif($post->effective_status === \App\Enums\PostStatus::Published && $post->published_at)
                                <span class="text-white/35">·</span>
                                <span class="text-white/55">منتشر شده: {{ $post->published_at_fa }}</span>
                            @endif

                            <span class="text-white/35">·</span>
                            <span class="text-white/55">{{ \App\Support\Fa::number($post->comments_count) }} کامنت</span>
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center gap-2">
                        <a href="{{ route('posts.show', $post) }}" class="btn-glass btn-sm">نمایش</a>

                        @can('update', $post)
                            <a href="{{ route('posts.edit', $post) }}" class="btn-edit btn-sm">ویرایش</a>
                        @endcan

                        @can('delete', $post)
                            <form action="{{ route('posts.destroy', $post) }}" method="POST"
                                  data-confirm="مطمئنی می‌خوای این پست رو حذف کنی؟">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-delete btn-sm">حذف</button>
                            </form>
                        @endcan
                    </div>

                </div>
            </article>

        @empty

            <div class="glass rounded-3xl text-center text-white/60 py-14 px-6">
                پستی پیدا نشد.
            </div>

        @endforelse

        <div class="mt-8">
            {{ $posts->links() }}
        </div>

    </section>

@endsection
