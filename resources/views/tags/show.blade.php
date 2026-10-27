@extends('layout')

@section('title', $tag->title)

@section('content')

    <div class="max-w-6xl mx-auto px-6 py-8">

        <div class="mb-8">

            <a
                href="{{ route('tags.index') }}"
                class="inline-flex items-center gap-1 px-4 py-1.5 text-sm font-semibold
                       text-violet-200 rounded-full
                       bg-white/10 border border-white/20
                       hover:bg-white/20 transition"
            >
                ← Back to Tags
            </a>

            <h1 class="text-3xl font-extrabold text-white mt-5">
                {{ $tag->name }}
            </h1>

            <p class="text-white/60 mt-1">
                Posts with this tag
                <span class="inline-block ms-2 px-2.5 py-0.5 rounded-full text-xs
                             bg-white/10 border border-white/15 text-white/70">
                    {{ $tag->posts->count() }} posts
                </span>
            </p>

        </div>


        @forelse($tag->posts as $post)

            <div class="glass-card mb-4">

                <a
                    href="{{ route('posts.show', $post) }}"
                    class="text-xl font-semibold text-white hover:text-violet-300 transition"
                >
                    {{ $post->title }}
                </a>

                <p class="text-sm text-white/50 mt-2">
                    By {{ $post->user->name }}
                </p>

                <p class="text-white/70 mt-3">
                    {{ $post->description }}
                </p>

            </div>

        @empty

            <div class="glass rounded-3xl p-10 text-center text-white/60">
                No posts found with this tag.
            </div>

        @endforelse

    </div>

@endsection
