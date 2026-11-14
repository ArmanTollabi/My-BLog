@extends('layout')

@section('title', '#' . $tag->title)

@section('content')

    <x-seo :title="'#' . $tag->title" :description="'پست‌های دارای تگ ' . $tag->title" />

    <div class="max-w-6xl mx-auto px-6 py-8">

        <x-breadcrumbs :items="[
            ['label' => 'خانه', 'url' => route('home')],
            ['label' => 'تگ‌ها', 'url' => route('tags.index')],
            ['label' => '#' . $tag->title],
        ]" />

        <div class="mb-8">
            <h1 class="text-3xl font-extrabold text-white">#{{ $tag->title }}</h1>

            <p class="text-white/60 mt-2">
                پست‌های دارای این تگ
                <span class="inline-block ms-2 px-2.5 py-0.5 rounded-full text-xs
                             bg-white/10 border border-white/15 text-white/70">
                    {{ number_format($posts->count()) }} پست
                </span>
            </p>
        </div>

        @if($posts->isNotEmpty())
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach($posts as $post)
                    @include('posts._card', ['post' => $post])
                @endforeach
            </div>
        @else
            <div class="glass rounded-3xl p-10 text-center text-white/60">
                هنوز پستی با این تگ منتشر نشده است.
            </div>
        @endif

    </div>

@endsection
