<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title')</title>

    @vite('resources/css/app.css')
    @vite('resources/js/app.js')
    @if(Route::currentRouteName() === 'posts.index')
        @vite('resources/js/custom.js')
    @endif
</head>
<section class="glass border-b border-white/10">
    <div class="max-w-7xl mx-auto px-6 pt-5 pb-10 md:pb-12">

        {{-- ===== نوار بالا: لوگو + نوتیفیکیشن + پنل کاربری ===== --}}
        @php
            // کتگوری‌هایی که حداقل یک پست دارند (برای زیرمنوی Posts)
            $navCategories = \App\Models\Category::has('posts')->withCount('posts')->orderBy('title')->get();
            $routeCategory = request()->route('category');
            $routeCategoryId = $routeCategory instanceof \App\Models\Category ? $routeCategory->id : $routeCategory;
        @endphp

        <div class="flex flex-wrap items-center justify-between gap-x-4 gap-y-3">

            {{-- لوگو: کلیک = رفتن به /posts (فاصله‌ی چپ برای دکمه‌ی ثابت سایدبار) --}}
            <a href="{{ route('home') }}" class="brand ml-12 min-[1400px]:ml-0"
               aria-label="MyBlog — Posts">
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
            <nav class="site-nav order-3 basis-full md:order-none md:basis-auto" aria-label="Main">

                <a href="{{ route('home') }}"
                   class="nav-link {{ request()->routeIs('home') ? 'is-active' : '' }}"
                   @if(request()->routeIs('home')) aria-current="page" @endif>
                    <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                         stroke-width="1.8" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25"/>
                    </svg>
                    Home
                </a>

                <div class="nav-dd {{ request()->routeIs('posts.*', 'categories.show') ? 'is-active' : '' }}" id="posts-nav">
                    <a href="{{ route('posts.index') }}" class="nav-link">
                        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                             stroke-width="1.8" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"/>
                        </svg>
                        Posts
                    </a>

                    <button type="button" class="nav-caret" id="posts-nav-toggle"
                            aria-label="Categories" aria-haspopup="true" aria-expanded="false" aria-controls="posts-nav-panel">
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
                                   class="nav-sub {{ request()->routeIs('categories.show') && (int) $routeCategoryId === $navCategory->id ? 'is-active' : '' }}">
                                    <span class="truncate">{{ $navCategory->title }}</span>
                                </a>
                            @empty
                                <p class="px-3 py-2 text-sm text-white/50">هنوز کتگوری‌ای پست ندارد.</p>
                            @endforelse
                        </div>
                    </div>
                </div>

            </nav>

            <div class="flex items-center gap-3">

                @auth
                    @php
                        $user = auth()->user();
                        $notifications = $user->notifications()->latest()->get();
                        $unreadCount = $user->unreadNotifications()->count();
                    @endphp

                    {{-- نوتیفیکیشن --}}
                    <div class="relative">

                        <button
                            type="button"
                            id="notification-button"
                            class="btn-icon"
                            aria-label="Notifications"
                            aria-haspopup="true"
                            aria-expanded="false"
                        >
                            🔔

                            @if($unreadCount > 0)
                                <span
                                    class="absolute -top-1 -right-1
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
                            class="hidden absolute right-0 mt-3 w-80 max-w-[calc(100vw-2rem)]
                                   bg-white border border-gray-200
                                   rounded-xl shadow-lg z-50"
                        >

                            <div class="px-4 py-3 border-b border-gray-200">
                                <h3 class="font-bold text-gray-900">
                                    Notifications
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
                                            {{ $notification->created_at->format('Y-m-d H:i') }}
                                        </p>
                                    </a>

                                @empty

                                    <p class="px-4 py-6 text-center text-gray-400">
                                        No notifications yet.
                                    </p>

                                @endforelse

                            </div>

                        </div>
                    </div>

                    {{-- پنل کاربری (دراپ‌داون) --}}
                    <div class="relative">

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

                        <div id="user-dropdown" class="menu-panel hidden" role="menu" aria-label="User menu">

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
                            @if(auth()->user()->role === 'admin')
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
                                <a href="{{ route('users.index') }}" class="menu-item" role="menuitem">
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
                    <a href="{{ route('login') }}" class="btn-glass">
                        Login
                    </a>

                    <a href="{{ route('register') }}" class="btn-create">
                        Register
                    </a>
                @endguest

            </div>
        </div>

        {{-- ===== عنوان صفحه ===== --}}
        <div class="mt-10 flex flex-col md:flex-row md:items-end md:justify-between gap-6">

            <div>
                <h1 class="text-3xl md:text-4xl font-bold text-white">
                    @if(request()->routeIs('users.*'))
                        Users
                    @else
                        @if(! request()->routeIs('home'))
                        Latest Posts
                        @endif
                    @endif
                </h1>
                @if(!  (request()->routeIs('home') ||  request()->routeIs('users.*')))
                <p class="mt-2 text-white/60">
                    Explore the latest posts and articles.
                </p>
                @endif
            </div>

            @auth
                @if(request()->routeIs('users.*'))
                    @if(Route::currentRouteName() !== 'users.create')
                        <a href="{{ route('users.create') }}" class="btn-create self-start md:self-auto">
                            + Create User
                        </a>
                    @endif
                @else
                    @can('create', \App\Models\Post::class)
                        <a href="{{ route('posts.create') }}" class="btn-create self-start md:self-auto">
                            + Create Post
                        </a>
                    @endcan
                @endif
            @endauth

        </div>

    </div>
</section>
<script>
    // زیرمنوی Posts: با ماوس (هاور) باز می‌شود؛ روی موبایل/کیبورد با دکمه‌ی فلش
    (function () {
        const dd = document.getElementById('posts-nav');
        const toggle = document.getElementById('posts-nav-toggle');
        if (!dd || !toggle) return;

        function setOpen(open) {
            dd.classList.toggle('is-open', open);
            toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
        }

        toggle.addEventListener('click', function (e) {
            e.stopPropagation();
            setOpen(!dd.classList.contains('is-open'));
        });
        document.addEventListener('click', function (e) { if (!dd.contains(e.target)) setOpen(false); });
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && dd.classList.contains('is-open')) { setOpen(false); toggle.focus(); }
        });
    })();
</script>
<script>
    (function () {
        // نوتیفیکیشن و پنل کاربری: هر دو دراپ‌داون با یک منطق مشترک
        const pairs = [
            ['notification-button', 'notification-dropdown'],
            ['user-menu-button', 'user-dropdown']
        ].map(function (ids) {
            return {
                button: document.getElementById(ids[0]),
                panel: document.getElementById(ids[1])
            };
        }).filter(function (p) { return p.button && p.panel; });

        // برای مهمان‌ها این المان‌ها وجود ندارند
        if (!pairs.length) return;

        function setOpen(p, open) {
            p.panel.classList.toggle('hidden', !open);
            p.button.setAttribute('aria-expanded', open ? 'true' : 'false');
        }

        function closeAll(except) {
            pairs.forEach(function (p) { if (p !== except) setOpen(p, false); });
        }

        pairs.forEach(function (p) {
            p.button.addEventListener('click', function (e) {
                e.stopPropagation();
                const willOpen = p.panel.classList.contains('hidden');
                closeAll(p);            // با باز شدن یکی، دیگری بسته می‌شود
                setOpen(p, willOpen);
            });
        });

        // بستن با کلیک بیرون یا Esc
        document.addEventListener('click', function (e) {
            pairs.forEach(function (p) {
                if (!p.panel.contains(e.target)) setOpen(p, false);
            });
        });
        document.addEventListener('keydown', function (e) {
            if (e.key !== 'Escape') return;
            pairs.forEach(function (p) {
                if (!p.panel.classList.contains('hidden')) {
                    setOpen(p, false);
                    p.button.focus();
                }
            });
        });
    })();
</script>
