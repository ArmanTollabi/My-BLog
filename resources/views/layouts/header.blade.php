<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@hasSection('title')@yield('title') | @endif MyBlog</title>
    @stack('head')
    @vite('resources/css/app.css')
    @vite('resources/js/app.js')

</head>
{{-- صفحه‌های ورود/ثبت‌نام/بازیابی رمز با @section('hide_header') هدر را پنهان می‌کنند (head و @vite بالاتر و بیرون از شرط‌اند) --}}
@unless(View::hasSection('hide_header'))
    <section class="glass border-b border-white/10">
        <div class="hdr-wrap max-w-7xl mx-auto px-6 pt-5 pb-5">

            {{-- ===== نوار بالا: لوگو + نوتیفیکیشن + پنل کاربری ===== --}}
            <div class="hdr-row flex flex-wrap items-center justify-between gap-x-4 gap-y-3">

                {{-- لوگو: کلیک = رفتن به صفحه اصلی (ms-12: فاصله‌ی شروع برای دکمه‌ی ثابت سایدبار که در RTL سمت راست است) --}}
                <a href="{{ route('home') }}" class="brand ms-12 min-[1400px]:ms-0"
                   aria-label="MyBlog — صفحه اصلی">
                <span class="brand-mark">
                    <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                         stroke-width="1.8" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10"/>
                    </svg>
                </span>
                    <span class="brand-name">MyBlog</span>
                </a>

                {{-- ===== منوی ناوبری: Home + Posts (کشویی) ===== --}}
                @unless(request()->routeIs('dashboard', 'dashboard.*', 'admin.*'))
                    <nav class="site-nav order-3 basis-full md:order-none md:basis-auto" aria-label="منوی اصلی">

                        <a href="{{ route('home') }}"
                           class="nav-link {{ request()->routeIs('home') ? 'is-active' : '' }}"
                           @if(request()->routeIs('home')) aria-current="page" @endif>
                            <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                 stroke-width="1.8" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25"/>
                            </svg>
                            خانه
                        </a>

                        <div class="nav-dd {{ request()->routeIs('posts.*', 'categories.show') ? 'is-active' : '' }}" id="posts-nav">
                            <a href="{{ route('posts.index') }}" class="nav-link">
                                <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                     stroke-width="1.8" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"/>
                                </svg>
                                پست‌ها
                            </a>

                            <button type="button" class="nav-caret" id="posts-nav-toggle"
                                    aria-label="دسته‌بندی‌ها" aria-haspopup="true" aria-expanded="false" aria-controls="posts-nav-panel">
                                <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                     stroke-width="2" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5"/>
                                </svg>
                            </button>

                            <div class="nav-panel" id="posts-nav-panel">
                                <div class="nav-panel-box">
                                    <a href="{{ route('posts.index') }}"
                                       class="nav-sub nav-sub-all {{ request()->routeIs('posts.index') && ! request()->filled('category') ? 'is-active' : '' }}">
                                        <span>همه‌ی پست‌ها</span>
                                    </a>

                                    @forelse($navCategories as $navCategory)
                                        <a href="{{ route('categories.show', $navCategory) }}"
                                           class="nav-sub {{ request()->routeIs('categories.show') && (int) $navActiveCategoryId === $navCategory->id ? 'is-active' : '' }}">
                                            <span class="truncate">{{ $navCategory->title }}</span>
                                        </a>
                                    @empty
                                        <p class="px-3 py-2 text-sm text-white/50">هنوز دسته‌ای پست ندارد.</p>
                                    @endforelse
                                </div>
                            </div>
                        </div>

                    </nav>
                @endunless

                {{-- relative: هر دو کشو (اعلان‌ها و منوی کاربر) به همین گروه می‌چسبند، نه به دکمه‌ی خودشان.
                     چون گروه کنار لبه‌ی صفحه است، کشو با inline-end هم‌لبه می‌شود و به داخل صفحه باز می‌شود (نه بیرون) --}}
                <div class="hdr-actions relative flex items-center gap-3">

                    {{-- جستجوی سراسری (پنجره‌ی جستجو: layouts/_live-search) --}}
                    <button type="button" class="btn-icon" data-open-search aria-label="جستجو (کلید /)" title="جستجو ( / )">
                        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                             stroke-width="1.8" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/>
                        </svg>
                    </button>

                    @auth
                        @php
                            // $notifications و $unreadCount را HeaderComposer می‌فرستد
                            $user = auth()->user();
                        @endphp

                        {{-- نوتیفیکیشن --}}
                        <div>

                            <button
                                type="button"
                                id="notification-button"
                                class="btn-icon relative"
                                aria-label="اعلان‌ها"
                                aria-haspopup="true"
                                aria-expanded="false"
                            >
                                🔔

                                @if($unreadCount > 0)
                                    <span
                                        class="absolute -top-1 -end-1
                                           bg-rose-500 text-white
                                           text-xs font-bold
                                           w-5 h-5 rounded-full
                                           flex items-center justify-center
                                           ring-2 ring-[#120a33]"
                                    >
                                    {{ $unreadCount }}
                                </span>
                                @endif
                            </button>

                            <div
                                id="notification-dropdown"
                                class="hidden absolute end-0 top-full mt-3 w-80 max-w-[calc(100vw-2rem)]
                                   bg-white border border-gray-200
                                   rounded-xl shadow-lg z-50"
                            >

                                <div class="px-4 py-3 border-b border-gray-200">
                                    <h3 class="font-bold text-gray-900">
                                        اعلان‌ها
                                    </h3>
                                </div>

                                <div class="max-h-96 overflow-y-auto">

                                    @forelse($notifications as $notification)

                                        <a
                                            href="{{ route('notifications.read', $notification) }}"
                                            class="block px-4 py-3 border-b border-gray-100 transition
                                               {{ $notification->read_at === null
                                                    ? 'bg-indigo-50 hover:bg-indigo-100'
                                                    : 'bg-white hover:bg-gray-50' }}"
                                        >
                                            <p class="text-sm
                                                  {{ $notification->read_at === null
                                                       ? 'font-semibold text-gray-900'
                                                       : 'text-gray-600' }}">
                                                {{ $notification->data['message'] }}
                                            </p>

                                            <p class="text-xs text-gray-400 mt-1">
                                                {{ $notification->created_at_with_hours_fa }}
                                            </p>
                                        </a>

                                    @empty

                                        <p class="px-4 py-6 text-center text-gray-400">
                                            هنوز اعلانی ندارید.
                                        </p>

                                    @endforelse

                                </div>

                            </div>
                        </div>

                        {{-- پنل کاربری (دراپ‌داون) --}}
                        <div>

                            <button
                                type="button"
                                id="user-menu-button"
                                class="user-chip"
                                aria-haspopup="menu"
                                aria-expanded="false"
                                aria-controls="user-dropdown"
                            >
                            <span class="user-avatar">
                                @if($user->avatar)
                                    <img src="{{ Storage::url($user->avatar) }}" alt="">
                                @else
                                    {{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}
                                @endif
                            </span>

                                <span class="hidden sm:block max-w-32 truncate">{{ $user->name }}</span>

                                <svg class="user-chevron w-4 h-4 text-white/70" xmlns="http://www.w3.org/2000/svg" fill="none"
                                     viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5"/>
                                </svg>
                            </button>

                            <div id="user-dropdown" class="menu-panel hidden" role="menu" aria-label="منوی کاربر">

                                {{-- اطلاعات کوتاه کاربر --}}
                                <div class="menu-user">
                                <span class="user-avatar is-lg">
                                    @if($user->avatar)
                                        <img src="{{ Storage::url($user->avatar) }}" alt="">
                                    @else
                                        {{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}
                                    @endif
                                </span>

                                    <div>
                                        <strong>{{ $user->name }}</strong>
                                        <span>{{ $user->email }}</span>
                                    </div>
                                </div>
                                {{-- داشبورد --}}
                                @if($user->isAdmin())
                                    <a href="{{ route('admin.dashboard') }}" class="menu-item" role="menuitem">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-user-shield preview-icon"><path d="M10 15H6a4 4 0 0 0-4 4v2"/><path d="M22 17.5c0 2.499-1.75 3.749-3.83 4.474a.5.5 0 0 1-.335-.005c-2.085-.72-3.835-1.97-3.835-4.47V14a.5.5 0 0 1 .5-.499c1 0 2.25-.6 3.12-1.36a.6.6 0 0 1 .76-.001c.875.765 2.12 1.36 3.12 1.36a.5.5 0 0 1 .5.5z"/><circle cx="9" cy="7" r="4"/>
                                        </svg>
                                        پنل مدیریت
                                    </a>


                                @endif
                                <a href="{{ route('dashboard') }}" class="menu-item" role="menuitem">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-layout-dashboard preview-icon"><rect width="7" height="9" x="3" y="3" rx="1"/><rect width="7" height="5" x="14" y="3" rx="1"/><rect width="7" height="9" x="14" y="12" rx="1"/><rect width="7" height="5" x="3" y="16" rx="1"/>
                                    </svg>
                                    داشبورد
                                </a>


                                {{-- کاربران --}}
                                @can('isAdmin',$user)
                                    <a href="{{ route('admin.users.index') }}" class="menu-item" role="menuitem">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-user preview-icon"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>
                                        </svg>
                                        کاربران
                                    </a>
                                @endcan
                                {{-- خروج (با پاپ‌آپ تایید شیشه‌ای اگر <x-confirm-modal /> نصب باشد) --}}
                                <form action="{{ route('logout') }}" method="POST"
                                      onsubmit="return confirm('مطمئنی می‌خوای خارج بشی؟')">
                                    @csrf

                                    <button type="submit" class="menu-item is-danger" role="menuitem">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-log-out preview-icon"><path d="m16 17 5-5-5-5"/><path d="M21 12H9"/><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                                        </svg>
                                        خروج از حساب
                                    </button>
                                </form>

                            </div>
                        </div>
                    @endauth

                    {{-- مهمان --}}
                    @guest
                        {{-- موبایل: فقط یک دکمه (ثبت‌نام داخل صفحه‌ی ورود هست). دسکتاپ: دو دکمه --}}
                        <div class="sm:hidden">
                            <a href="{{ route('login') }}" class="btn-create btn-sm">
                                ورود / ثبت‌نام
                            </a>
                        </div>

                        <div class="hidden sm:flex items-center gap-3">
                            <a href="{{ route('login') }}" class="btn-glass">
                                ورود
                            </a>

                            <a href="{{ route('register') }}" class="btn-create">
                                ثبت‌نام
                            </a>
                        </div>
                    @endguest

                </div>
            </div>

        </div>
    </section>
@endunless

@include('layouts._live-search')
