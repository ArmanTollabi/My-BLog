@extends('dashboard.layout')

@section('title', 'پست‌های شما')
@section('dash-title', 'پست‌های شما')
@section('dash-subtitle', 'همه‌ی پست‌هایی که نوشته‌اید، با هر وضعیت (پیش‌نویس، زمان‌بندی‌شده، منتشرشده و بایگانی‌شده).')

@section('dash-content')

    @if($posts->isNotEmpty())
        <div class="grid gap-5 sm:grid-cols-2 2xl:grid-cols-3">
            @foreach($posts as $post)
                @include('dashboard._post-card', ['post' => $post, 'showStatus' => true])
            @endforeach
        </div>

        @if($posts->hasPages())
            <div class="mt-6">{{ $posts->links() }}</div>
        @endif
    @else
        <div class="glass rounded-3xl px-6 py-14 text-center">
            <p class="text-lg font-bold text-white">هنوز پستی ننوشته‌اید.</p>
            <p class="mt-2 text-sm text-white/55">پست‌هایی که بنویسید اینجا نمایش داده می‌شوند.</p>

            @can('create', \App\Models\Post::class)
                <a href="{{ route('posts.create') }}" class="btn-create mt-6">+ نوشتن پست جدید</a>
            @endcan
        </div>
    @endif

@endsection
