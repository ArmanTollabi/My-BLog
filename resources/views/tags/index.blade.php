@extends('layout')

@section('title', 'Tags')

@section('content')

    <div class="max-w-6xl mx-auto px-6 py-8">

        {{-- هدر صفحه --}}
        <div class="flex flex-wrap items-center justify-between gap-4 mb-8">
            <div>
                <h1 class="text-3xl font-extrabold text-white">
                    Tags
                </h1>

                <p class="text-white/60 mt-1">
                    Manage your tags
                </p>
            </div>
           @can('create_cat',App\Models\Category::class)
            <a href="{{ route('tags.create') }}" class="glass-btn-primary">
                + Create Tag
            </a>
           @endcan
        </div>


        {{-- لیست تگ‌ها --}}
        <div class="glass rounded-3xl overflow-hidden">

            @forelse($tags as $tag)

                <div class="flex flex-wrap items-center justify-between gap-3 px-6 py-4
                            border-b border-white/10 last:border-b-0
                            hover:bg-white/5 transition">

                    <div class="flex items-center gap-4">

                        {{-- آیکن تگ --}}
                        <span class="grid place-items-center w-11 h-11 rounded-2xl text-lg font-bold text-white
                                     border border-white/25
                                     bg-gradient-to-br from-violet-500/60 to-cyan-500/40">
                            {{ strtoupper(mb_substr($tag->title, 0, 1)) }}
                        </span>

                        <div>
                            <a
                                href="{{ route('tags.show', $tag) }}"
                                class="font-semibold text-white hover:text-violet-300 transition"
                            >
                                {{ $tag->title }}
                            </a>

                            <p class="mt-1">
                                <span class="inline-block px-2.5 py-0.5 rounded-full text-xs
                                             bg-white/10 border border-white/15 text-white/70">
                                    {{ $tag->posts_count }} posts
                                </span>
                            </p>
                        </div>
                    </div>


                    <div class="flex items-center gap-2">

                        <a
                            href="{{ route('tags.show', $tag) }}"
                            class="px-4 py-2 text-sm font-semibold text-violet-200 rounded-xl
                                   border border-transparent
                                   hover:bg-violet-500/20 hover:border-violet-300/30 transition"
                        >
                            Show Posts
                        </a>

                        <form
                            action="{{ route('tags.destroy', $tag) }}"
                            method="POST"
                            onsubmit="return confirm('Are you sure you want to delete this tag?')"
                        >
                            @csrf
                            @method('DELETE')

                            <button
                                type="submit"
                                class="px-4 py-2 text-sm font-semibold text-rose-300 rounded-xl cursor-pointer
                                       border border-transparent
                                       hover:bg-rose-500/20 hover:border-rose-300/30 transition"
                            >
                                Delete
                            </button>
                        </form>

                    </div>

                </div>

            @empty

                <div class="p-10 text-center text-white/60">
                    No tags found.
                </div>

            @endforelse

        </div>

    </div>

@endsection
