@extends('layout')

@section('title', 'Categories')

@section('content')

    <div class="max-w-5xl mx-auto px-6 py-10">

        {{-- Header --}}
        <div class="flex flex-wrap items-center justify-between gap-4 mb-8">

            <div>
                <h1 class="text-3xl font-extrabold text-white">
                    Categories
                </h1>

                <p class="mt-2 text-white/60">
                    post categories.
                </p>
            </div>
            @can('create_cat',App\Models\Category::class)

            <a href="{{ route('categories.create') }}" class="glass-btn-primary">
                + Create Category
            </a>
          @endcan

        </div>


        {{-- Categories --}}
        <div class="glass rounded-3xl overflow-hidden">

            @forelse($categories as $category)

                <div class="flex flex-wrap items-center justify-between gap-3
                            px-6 py-4
                            border-b border-white/10 last:border-b-0
                            hover:bg-white/5 transition">

                    <div class="flex items-center gap-4">

                        {{-- حرف اول عنوان --}}
                        <div class="w-11 h-11 rounded-2xl
                                    flex items-center justify-center
                                    text-lg font-bold text-white
                                    border border-white/25
                                    bg-gradient-to-br from-violet-500/60 to-cyan-500/40">
                            {{ strtoupper(mb_substr($category->title, 0, 1)) }}
                        </div>

                        <div>
                            <p class="font-semibold text-white">
                                {{ $category->title }}
                            </p>

                            <p class="mt-1">
                                <span class="inline-block px-2.5 py-0.5 rounded-full text-xs
                                             bg-white/10 border border-white/15 text-white/70">
                                    {{ $category->posts_count ?? $category->posts()->count() }}
                                    posts
                                </span>
                            </p>
                        </div>

                    </div>

                    <div class="flex items-center gap-2">
                        <a
                            href="{{ route('categories.show', $category) }}"
                            class="px-4 py-2 text-sm font-semibold text-violet-200 rounded-xl
                                   border border-transparent
                                   hover:bg-violet-500/20 hover:border-violet-300/30 transition"
                        >
                            Show Posts
                        </a>

                        {{-- Delete --}}
                        @can('isAdmin',$user)
                        <form
                            action="{{ route('categories.destroy', $category) }}"
                            method="POST"
                            onsubmit="return confirm('Are you sure you want to delete this category?')"
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
                        @endcan
                    </div>
                </div>
            @empty

                <div class="px-6 py-12 text-center">

                    <p class="text-white/60">
                        No categories have been created yet.
                    </p>

                    @can('isAdmin',App\Models\User::class)
                        <a
                            href="{{ route('categories.create') }}"
                            class="inline-block mt-4 font-semibold
                               text-violet-300 hover:text-violet-200 transition"
                        >
                            Create your first category
                        </a>
                    @endcan



                </div>

            @endforelse

        </div>

    </div>

@endsection
