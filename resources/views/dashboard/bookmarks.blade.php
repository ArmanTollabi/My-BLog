@extends('dashboard.layout')

@section('title', 'بوکمارک‌ها')
@section('dash-title', 'بوکمارک‌ها')
@section('dash-subtitle', 'پست‌هایی که برای خواندن بعدی ذخیره کرده‌اید.')

@section('dash-content')

    @if($posts->isNotEmpty())
        <div class="grid gap-5 sm:grid-cols-2 2xl:grid-cols-3">
            @foreach($posts as $post)
                @include('dashboard._post-card', ['post' => $post])
            @endforeach
        </div>

        @if($posts->hasPages())
            <div class="mt-6">{{ $posts->links() }}</div>
        @endif
    @else
        <div class="glass rounded-3xl px-6 py-14 text-center">
            <div class="mx-auto grid h-16 w-16 place-items-center rounded-3xl bg-cyan-500/20 text-cyan-200">
                <svg class="h-8 w-8" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                     stroke-width="1.6" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M17.593 3.322c1.1.128 1.907 1.077 1.907 2.185V21L12 17.25 4.5 21V5.507c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0 1 11.186 0Z"/>
                </svg>
            </div>
            <p class="mt-5 text-lg font-bold text-white">هنوز پستی را بوکمارک نکرده‌اید.</p>
            <p class="mt-2 text-sm text-white/55">در صفحه‌ی هر پست روی «ذخیره» بزنید تا اینجا جمع شود.</p>
            <a href="{{ route('posts.index') }}" class="btn-create mt-6">مشاهده‌ی پست‌ها</a>
        </div>
    @endif

@endsection
