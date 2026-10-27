@php
    // مسیر آیکن‌ها (Heroicons) — برای آیتم جدید فقط به آرایه‌ی $menu پایین اضافه کنید
    $icons = [
        'folder' => 'M2.25 12.75V12A2.25 2.25 0 0 1 4.5 9.75h15A2.25 2.25 0 0 1 21.75 12v.75m-8.69-6.44-2.12-2.12a1.5 1.5 0 0 0-1.061-.44H4.5A2.25 2.25 0 0 0 2.25 6v12a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9a2.25 2.25 0 0 0-2.25-2.25h-5.379a1.5 1.5 0 0 1-1.06-.44Z',
        'hash'   => 'M5.25 8.25h15m-16.5 7.5h15m-1.8-13.5-3.9 19.5m-2.1-19.5-3.9 19.5',
        'plus'   => 'M12 4.5v15m7.5-7.5h-15',
        'list'   => 'M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75Zm0 5.25h.007v.008H3.75V12Zm0 5.25h.007v.008H3.75v-.008Z',
        'chevron'=> 'm19.5 8.25-7.5 7.5-7.5-7.5',
        'bars'   => 'M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5',
        'x'      => 'M6 18 18 6M6 6l12 12',
        'chat'   => 'M2.25 12.76c0 1.6 1.123 2.994 2.707 3.227 1.087.16 2.185.283 3.293.369V21l4.076-4.076a1.526 1.526 0 0 1 1.037-.443 48.282 48.282 0 0 0 5.68-.494c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0 0 12 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018Z',
        'trash'  => 'm14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0',
        'shield' => 'M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z',
        'users' => 'M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z',
        'addUser' => 'M2 21a8 8 0 0 1 13.292-6M15 8a5 5 0 1 1-10 0 5 5 0 0 1 10 0ZM19 16v6M22 19h-6',
    ];

    $svg = fn (string $name, string $class = 'w-5 h-5') =>
        '<svg class="' . $class . '" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" '
        . 'stroke-width="1.8" stroke="currentColor" aria-hidden="true">'
        . '<path stroke-linecap="round" stroke-linejoin="round" d="' . $icons[$name] . '"/></svg>';

    // ساختار منو: 'active' الگوی route برای باز بودن خودکار گروه، 'route' برای هر لینک
    $menu = [
        [
            'label'  => 'Categories',
            'icon'   => 'folder',
            'active' => 'categories.*',
            'items'  => [
                ['label' => 'Create Category', 'route' => 'categories.create', 'icon' => 'plus'],
                ['label' => 'View Categories', 'route' => 'categories.index',  'icon' => 'list'],
            ],
        ],
        [
            'label'  => 'Tags',
            'icon'   => 'hash',
            'active' => 'tags.*',
            'items'  => [
                ['label' => 'Create Tag', 'route' => 'tags.create', 'icon' => 'plus'],
                ['label' => 'View Tags',  'route' => 'tags.index',  'icon' => 'list'],
            ],
        ],
        [
            'label'  => 'Users',
            'icon'   => 'users',
            'active' => 'users.*',
            'items'  => [
                ['label' => 'Create User', 'route' => 'users.create', 'icon' => 'addUser'],
                ['label' => 'View Users',  'route' => 'users.index',  'icon' => 'users'],
            ],
        ],
        [
            'label'  => 'Comments',
            'icon'   => 'chat',
            'active' => 'admin.comments.*',
            'admin'  => true, // فقط برای ادمین نمایش داده می‌شود
            'items'  => [
                ['label' => 'All Comments', 'route' => 'admin.comments.index', 'icon' => 'list'],
                ['label' => 'Trash',        'route' => 'admin.comments.trash', 'icon' => 'trash'],
            ],
        ],
    ];

    $isAdmin = auth()->user()?->role === 'admin';
@endphp

{{-- دکمه باز کردن منو (بالاتر از هدر) --}}
<button type="button" id="sidebar-button" class="btn-icon sidebar-toggle"
        aria-label="Open menu" aria-controls="sidebar" aria-expanded="false">
    {!! $svg('bars', 'w-6 h-6') !!}
</button>

{{-- پس‌زمینه تار --}}
<div id="sidebar-overlay" class="sidebar-overlay" aria-hidden="true"></div>

<aside id="sidebar" class="sidebar" aria-label="Sidebar" aria-hidden="true">

    {{-- Header --}}
    <div class="sidebar-head">
        <span class="sidebar-logo">{!! $svg('shield', 'w-6 h-6') !!}</span>

        <h2 class="sidebar-title">
            Admin Panel
            <small>Manage your content</small>
        </h2>

        <button type="button" id="sidebar-close" class="sidebar-close" aria-label="Close menu">
            {!! $svg('x', 'w-5 h-5') !!}
        </button>
    </div>

    {{-- Navigation --}}
    <nav class="sidebar-nav" aria-label="Admin navigation">

        @foreach ($menu as $i => $group)
            @continue(($group['admin'] ?? false) && ! $isAdmin)
            @php($open = request()->routeIs($group['active']))

            <div class="sb-group {{ $open ? 'is-open' : '' }}">

                <button type="button"
                        class="sb-link {{ $open ? 'has-active' : '' }}"
                        data-accordion
                        aria-expanded="{{ $open ? 'true' : 'false' }}"
                        aria-controls="sb-sub-{{ $i }}">
                    {!! $svg($group['icon']) !!}
                    <span class="flex-1">{{ $group['label'] }}</span>
                    {!! $svg('chevron', 'w-4 h-4 sb-chevron') !!}
                </button>

                <div id="sb-sub-{{ $i }}" class="sb-sub">
                    <div class="sb-sub-inner">
                        @foreach ($group['items'] as $item)
                            @php($current = request()->routeIs($item['route']))

                            <a href="{{ route($item['route']) }}"
                               class="sb-link sb-sublink {{ $current ? 'is-active' : '' }}"
                               @if ($current) aria-current="page" @endif>
                                {!! $svg($item['icon'], 'w-4 h-4') !!}
                                {{ $item['label'] }}
                            </a>
                        @endforeach
                    </div>
                </div>

            </div>
        @endforeach

    </nav>

</aside>

<script>
    (function () {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebar-overlay');
        const toggle = document.getElementById('sidebar-button');
        const closeBtn = document.getElementById('sidebar-close');

        if (!sidebar || !overlay || !toggle) return;

        function setOpen(open) {
            sidebar.classList.toggle('is-open', open);
            overlay.classList.toggle('is-open', open);
            sidebar.setAttribute('aria-hidden', open ? 'false' : 'true');
            toggle.setAttribute('aria-expanded', open ? 'true' : 'false');

            // مدیریت فوکوس برای کاربران کیبورد
            (open ? closeBtn : toggle).focus({preventScroll: true});
        }

        toggle.addEventListener('click', function () { setOpen(true); });
        closeBtn.addEventListener('click', function () { setOpen(false); });
        overlay.addEventListener('click', function () { setOpen(false); });

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && sidebar.classList.contains('is-open')) setOpen(false);
        });

        // یک handler برای همه‌ی زیرمنوها (به‌جای کد جدا برای هر منو)
        sidebar.addEventListener('click', function (e) {
            const btn = e.target.closest('[data-accordion]');
            if (!btn) return;

            const group = btn.closest('.sb-group');
            const open = group.classList.toggle('is-open');
            btn.setAttribute('aria-expanded', open ? 'true' : 'false');
        });
    })();
</script>
