@extends('admin.layout')

@section('title', 'Users')

@section('admin-content')

    <section class="max-w-5xl mx-auto px-6 py-12">

        {{-- جستجو و فیلتر (input / select / button مستقیماً داخل فرم، تا در یک ردیف قرار بگیرند) --}}
        <form action="{{ route('users.index') }}" method="GET" class="mb-8">

            <input
                type="text"
                name="search"
                value="{{ request('search') }}"
                placeholder="Search users..."
                aria-label="Search users"
                class="glass-input"
            >

            <select name="post_filter" aria-label="Filter by post count">
                <option value="">Post count</option>

                <option value="most" {{ request('post_filter') === 'most' ? 'selected' : '' }}>
                    Most posts
                </option>

                <option value="least" {{ request('post_filter') === 'least' ? 'selected' : '' }}>
                    Least posts
                </option>

                <option value="over_5" {{ request('post_filter') === 'over_5' ? 'selected' : '' }}>
                    More than 5
                </option>

                <option value="over_10" {{ request('post_filter') === 'over_10' ? 'selected' : '' }}>
                    More than 10
                </option>

                <option value="over_20" {{ request('post_filter') === 'over_20' ? 'selected' : '' }}>
                    More than 20
                </option>
            </select>

            <button type="submit" class="btn-create">
                Search
            </button>

        </form>

        @forelse($users as $user)

            <article class="glass rounded-3xl p-5 mb-4 transition hover:bg-white/5">

                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">

                    {{-- اطلاعات کاربر --}}
                    <div class="flex items-center gap-4 min-w-0">

                        {{-- Avatar --}}
                        <span class="user-avatar is-lg">
                            @if($user->avatar)
                                <img src="{{ Storage::url($user->avatar) }}" alt="">
                            @else
                                {{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}
                            @endif
                        </span>

                        <div class="min-w-0">
                            <h2 class="text-lg font-bold text-white truncate">
                                {{ $user->name }}
                            </h2>

                            <p dir="ltr" class="text-sm text-white/55 mt-0.5 truncate text-left">
                                {{ $user->email }}
                            </p>

                            @if(in_array(request('post_filter'), ['most', 'least', 'over_5', 'over_10', 'over_20']))
                                <p class="mt-2">
                                    <span class="inline-block rounded-full border border-white/15 bg-white/10
                                                 px-2.5 py-0.5 text-xs text-white/70">
                                        Posts: {{ $user->posts_count }}
                                    </span>
                                </p>
                            @endif
                        </div>

                    </div>

                    {{-- Actions --}}
                    <div class="flex flex-wrap items-center gap-2">

                        @can('isAdmin', $user)
                            <a href="{{ route('users.show', $user) }}" class="btn-glass btn-sm">
                                نمایش
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none"
                                     stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                     aria-hidden="true">
                                    <circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/>
                                </svg>
                            </a>
                        @endcan

                        @can('update_user', $user)
                            <a href="{{ route('users.edit', $user) }}" class="btn-edit btn-sm">
                                ویرایش
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none"
                                     stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                     aria-hidden="true">
                                    <path d="M21.174 6.812a1 1 0 0 0-3.986-3.987L3.842 16.174a2 2 0 0 0-.5.83l-1.321 4.352a.5.5 0 0 0 .623.622l4.353-1.32a2 2 0 0 0 .83-.497z"/><path d="m15 5 4 4"/>
                                </svg>
                            </a>
                        @endcan

                        @can('delete_user', $user)
                            <form action="{{ route('users.destroy', $user) }}"
                                  method="POST"
                                  onsubmit="return confirm('مطمئنی می‌خوای این کاربر رو حذف کنی؟')">
                                @csrf
                                @method('DELETE')

                                <button type="submit" class="btn-delete btn-sm">
                                    حذف
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none"
                                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                         aria-hidden="true">
                                        <path d="M10 11v6"/><path d="M14 11v6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"/><path d="M3 6h18"/><path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                                    </svg>
                                </button>
                            </form>
                        @endcan

                    </div>

                </div>

            </article>

        @empty

            @if(request('search'))
                <div class="glass rounded-3xl text-center py-14 px-6">
                    <p class="text-xl font-semibold text-white">
                        کاربری پیدا نشد
                    </p>

                    <p class="mt-2 text-white/60">
                        هیچ کاربری با عبارت
                        <span class="font-semibold text-white">"{{ request('search') }}"</span>
                        پیدا نشد.
                    </p>

                    <a href="{{ route('users.index') }}" class="btn-glass btn-sm mt-5">
                        پاک کردن جستجو
                    </a>
                </div>
            @else
                <div class="glass rounded-3xl text-center text-white/60 py-14 px-6">
                    هنوز هیچ کاربری ثبت نشده.
                </div>
            @endif

        @endforelse

        <div class="mt-8">
            {{ $users->links() }}
        </div>

    </section>

@endsection
