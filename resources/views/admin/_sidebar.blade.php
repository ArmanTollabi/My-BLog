{{-- FILE: resources/views/admin/_sidebar.blade.php --}}
@php
    $icons = [
        'chart' => 'M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z',
        'users' => 'M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z',
        'chat'  => 'M2.25 12.76c0 1.6 1.123 2.994 2.707 3.227 1.087.16 2.185.283 3.293.369V21l4.076-4.076a1.526 1.526 0 0 1 1.037-.443 48.282 48.282 0 0 0 5.68-.494c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0 0 12 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018Z',
        'trash' => 'm14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0',
        'doc'   => 'M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z',
        'home'  => 'm2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25',
    ];

    $pending = \App\Models\Comment::pending()->count();

    $links = [
        ['label' => 'آمار',          'icon' => 'chart', 'route' => 'admin.dashboard',      'active' => 'admin.dashboard'],
        ['label' => 'کاربران',       'icon' => 'users', 'route' => 'users.index',          'active' => 'users.*'],
        ['label' => 'کامنت‌ها',      'icon' => 'chat',  'route' => 'admin.comments.index', 'active' => 'admin.comments.index', 'badge' => $pending],
        ['label' => 'سطل زباله',     'icon' => 'trash', 'route' => 'admin.comments.trash', 'active' => 'admin.comments.trash'],
        ['label' => 'پست‌ها',        'icon' => 'doc',   'route' => 'admin.posts.index',    'active' => 'admin.posts.*'],
    ];
@endphp

{{-- دسکتاپ: ستون ثابت کنار صفحه | موبایل و تبلت: نوار افقی قابل اسکرول (بدون اسکرول‌بار، با محو شدن لبه‌ها) --}}
<style>
    .admin-tabs { scrollbar-width: none; scroll-snap-type: x proximity; -webkit-overflow-scrolling: touch; }
    .admin-tabs::-webkit-scrollbar { display: none; }
    .admin-tabs > a { scroll-snap-align: center; }
    @media (max-width: 1023.98px) {
        .admin-tabs-wrap::before, .admin-tabs-wrap::after {
            content: ''; position: absolute; top: 0; bottom: 0; width: 1.5rem; z-index: 1; pointer-events: none;
        }
        .admin-tabs-wrap::before { inset-inline-start: 0; background: linear-gradient(to right, rgb(11 7 32 / .55), transparent); }
        .admin-tabs-wrap::after  { inset-inline-end: 0;  background: linear-gradient(to left,  rgb(11 7 32 / .55), transparent); }
    }
</style>

<div class="admin-tabs-wrap glass relative rounded-2xl lg:rounded-3xl p-2 lg:p-3 lg:sticky lg:top-24"
     role="navigation" aria-label="Admin sections">

    <p class="hidden lg:block px-4 pt-2 pb-3 text-xs font-semibold tracking-wide text-white/40">پنل مدیریت</p>

    <div class="admin-tabs flex lg:flex-col gap-1 overflow-x-auto lg:overflow-visible px-2 lg:px-0">

        @foreach($links as $link)
            @php($current = request()->routeIs($link['active']))

            <a href="{{ route($link['route']) }}"
               class="flex flex-none items-center gap-2 lg:gap-3 rounded-xl lg:rounded-2xl px-3 lg:px-4 py-2 lg:py-2.5 text-sm font-semibold whitespace-nowrap transition
                      {{ $current ? 'bg-white/15 text-white shadow-inner' : 'text-white/65 hover:bg-white/10 hover:text-white' }}"
               @if($current) aria-current="page" @endif>

                <svg class="w-5 h-5 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                     stroke-width="1.8" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $icons[$link['icon']] }}"/>
                </svg>

                <span class="lg:flex-1">{{ $link['label'] }}</span>

                @if(($link['badge'] ?? 0) > 0)
                    <span class="rounded-full bg-amber-400/20 px-2 py-0.5 text-xs font-semibold text-amber-200">
                        {{ $link['badge'] }}
                    </span>
                @endif
            </a>
        @endforeach

        <span class="hidden lg:block my-2 mx-3 h-px bg-white/10"></span>
        <span class="lg:hidden flex-none self-center mx-1 h-5 w-px bg-white/15"></span>

        <a href="{{ route('posts.index') }}"
           class="flex flex-none items-center gap-2 lg:gap-3 rounded-xl lg:rounded-2xl px-3 lg:px-4 py-2 lg:py-2.5 text-sm font-semibold whitespace-nowrap
                  text-white/50 transition hover:bg-white/10 hover:text-white">
            <svg class="w-5 h-5 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                 stroke-width="1.8" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $icons['home'] }}"/>
            </svg>
            بازگشت به سایت
        </a>

    </div>
</div>

<script>
    // روی موبایل، تب فعال را به وسط نوار بیاور
    (function () {
        const active = document.querySelector('.admin-tabs a[aria-current="page"]');
        if (!active) return;
        const bar = active.parentElement;
        if (bar.scrollWidth <= bar.clientWidth) return;
        bar.scrollLeft = active.offsetLeft - (bar.clientWidth - active.offsetWidth) / 2;
    })();
</script>
