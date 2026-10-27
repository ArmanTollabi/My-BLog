{{-- FILE: resources/views/admin/posts/index.blade.php --}}
@extends('admin.layout')

@section('title', 'Posts Admin')

@section('admin-content')

    <section>

        <div class="mb-6">
            <h1 class="text-3xl md:text-4xl font-bold gradient-text">مدیریت پست‌ها</h1>
            <p class="text-white/55 mt-2">لیست مختصر همه‌ی پست‌ها</p>
        </div>

        <form action="{{ route('admin.posts.index') }}" method="GET" class="mb-8 flex flex-wrap items-center gap-3">
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="جستجو در عنوان پست‌ها..." aria-label="Search posts" class="glass-input h-12 min-w-56 flex-1">
            <button type="submit" class="btn-create !h-12">Search</button>
        </form>

        @forelse($posts as $post)

            <article class="glass rounded-3xl p-5 mb-4">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">

                    <div class="min-w-0">
                        <h2 class="text-lg font-bold text-white truncate">
                            <a href="{{ route('posts.show', $post) }}" class="hover:underline">{{ $post->title }}</a>
                        </h2>

                        <div class="flex flex-wrap items-center gap-2 mt-2 text-xs">
                            @if($post->category)
                                <span class="rounded-full border border-white/15 bg-white/10 px-2.5 py-0.5 text-white/75">
                                    {{ $post->category->title }}
                                </span>
                            @endif
                            <span class="text-white/55">{{ $post->user?->name ?? 'کاربر حذف‌شده' }}</span>
                            <span class="text-white/35">·</span>
                            <span class="text-white/55">{{ $post->created_at->format('Y-m-d') }}</span>
                            <span class="text-white/35">·</span>
                            <span class="text-white/55">{{ $post->comments_count }} کامنت</span>
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center gap-2">
                        <a href="{{ route('posts.show', $post) }}" class="btn-glass btn-sm">نمایش</a>

                        @can('update', $post)
                            <a href="{{ route('posts.edit', $post) }}" class="btn-edit btn-sm">ویرایش</a>
                        @endcan

                        @can('delete', $post)
                            <form action="{{ route('posts.destroy', $post) }}" method="POST"
                                  onsubmit="return confirm('مطمئنی می‌خوای این پست رو حذف کنی؟')">
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
