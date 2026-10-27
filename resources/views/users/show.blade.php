@extends('layout')

@section('title', 'show-user')

@section('content')

    <section class="max-w-3xl mx-auto px-6 py-12">

        {{-- بازگشت --}}
        <a href="{{ route('users.index') }}" class="btn-glass btn-sm">
            ← Back to Users
        </a>

        {{-- کارت کاربر --}}
        <div class="glass rounded-3xl p-6 md:p-10 mt-6">

            {{-- عکس + نام --}}
            <div class="flex flex-col items-center text-center sm:flex-row sm:text-left gap-6 pb-8
                        border-b border-white/10">

                <span class="user-avatar is-xl">
                    @if($user->avatar)
                        <img src="{{ Storage::url($user->avatar) }}" alt="{{ $user->name }}">
                    @else
                        {{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}
                    @endif
                </span>

                <div class="min-w-0">
                    <h1 class="text-3xl font-extrabold text-white break-words">
                        {{ $user->name }}
                    </h1>

                    @if($user->created_at)
                        <p class="mt-2 inline-block rounded-full border border-white/15 bg-white/10
                                  px-3 py-1 text-xs text-white/65">
                            Member since {{ $user->created_at->format('Y-m-d') }}
                        </p>
                    @endif
                </div>
            </div>

            {{-- اطلاعات (فقط نمایشی و غیرقابل تغییر) --}}
            <dl class="mt-8 space-y-6">

                <div>
                    <dt class="block text-sm font-semibold text-white/70 mb-2">Name</dt>
                    <dd class="glass-input cursor-default select-text break-words">
                        {{ $user->name }}
                    </dd>
                </div>

                <div>
                    <dt class="block text-sm font-semibold text-white/70 mb-2">Email</dt>
                    <dd dir="ltr" class="glass-input cursor-default select-text break-all text-left">
                        {{ $user->email }}
                    </dd>
                </div>

                <div>
                    <dt class="block text-sm font-semibold text-white/70 mb-2">Profile photo</dt>
                    <dd class="glass-input cursor-default break-all">
                        @if($user->avatar)
                            <span dir="ltr" class="block text-left text-white/80">{{ $user->avatar }}</span>
                        @else
                            <span class="text-white/45">No photo uploaded.</span>
                        @endif
                    </dd>
                </div>

            </dl>

            {{-- دکمه‌ها --}}
            @canany(['update_user', 'delete_user'], $user)
                <div class="mt-8 flex flex-wrap items-center gap-3 border-t border-white/10 pt-6">

                    @can('update_user', $user)
                        <a href="{{ route('users.edit', $user) }}" class="btn-edit">
                            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                 stroke-width="1.8" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Z"/>
                            </svg>
                            ویرایش
                        </a>
                    @endcan

                    @can('delete_user', $user)
                        <form action="{{ route('users.destroy', $user) }}"
                              method="POST"
                              onsubmit="return confirm('مطمئنی می‌خوای این کاربر رو حذف کنی؟')">
                            @csrf
                            @method('DELETE')

                            <button type="submit" class="btn-delete">
                                <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                     stroke-width="1.8" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/>
                                </svg>
                                حذف
                            </button>
                        </form>
                    @endcan

                </div>
            @endcanany

        </div>

    </section>

@endsection
