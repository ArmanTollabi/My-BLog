{{-- FILE: resources/views/panel/_sidebar.blade.php
     ناوبری مشترک پنل‌ها (استایل: resources/css/panel.css ، رفتار: resources/js/panel.js).

       دسکتاپ (lg و بالاتر) : ستون بلند شیشه‌ای چسبیده به لبه‌ی راست
       موبایل و تبلت        : نوار ناوبری پایین صفحه (۴ بخش اصلی + «بیشتر») و یک bottom sheet برای بقیه

     ورودی‌ها:
       $panelTitle → مثلاً «پنل مدیریت»
       $links      → [['label','short'?,'d','route','active','badge'?,'children'?], ...]  (children = زیرمنوی کشویی فقط در دسکتاپ: [['label','route','params'?,'d'? (آیکن SVG)], ...] — params مثلاً ['status'=>'draft'])   (d = مسیر SVG؛ short = برچسب کوتاه برای نوار پایین)
       $footLinks  → [['label','d','route'], ...]  (لینک‌های پایین: بازگشت به سایت و ...)
--}}
@php
    $u = auth()->user();
    $footLinks = $footLinks ?? [];
    $roleLabel = $u->isAdmin() ? 'مدیر سایت' : 'کاربر سایت';

    $iconLogout = 'M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9';
    $iconMore   = 'M6.75 12a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0ZM12.75 12a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0ZM18.75 12a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z';

    // موبایل: چهار بخش اول در نوار پایین، بقیه (و لینک‌های پایین) داخل «بیشتر»
    $barLinks  = array_slice($links, 0, 4);
    $moreLinks = array_slice($links, 4);
    $moreActive = collect($moreLinks)->contains(fn ($l) => request()->routeIs($l['active']));
@endphp

{{-- ================= دسکتاپ ================= --}}
<div class="pnl glass" role="navigation" aria-label="{{ $panelTitle }}">
    <div class="pnl-inner">

        <div class="pnl-user">
            <span class="user-avatar is-lg">
                @if($u->avatar)
                    <img src="{{ Storage::url($u->avatar) }}" alt="">
                @else
                    {{ mb_strtoupper(mb_substr($u->name, 0, 1)) }}
                @endif
            </span>
            <div>
                <p class="pnl-name">{{ $u->name }}</p>
                <p class="pnl-role">{{ $roleLabel }}</p>
            </div>
        </div>

        <p class="pnl-label">{{ $panelTitle }}</p>

        <div class="pnl-tabs">
            @foreach($links as $link)
                @php($current = request()->routeIs($link['active']))

                {{-- آیتم کشویی: با کلیک زیرمنو باز/بسته می‌شود (رفتار: panel.js) --}}
                @if(! empty($link['children']))
                    @php($subId = 'pnl-sub-' . $loop->index)
                    {{-- هر زیرمنو: ['label','route','params'?]. فعال = همان route و همان ?status (بدون status = «همه») --}}
                    @php($kids = collect($link['children'])->map(fn ($c) => $c + ['href' => route($c['route'], $c['params'] ?? []), 'on' => request()->routeIs($c['route']) && (request('status') ?: null) === ($c['params']['status'] ?? null)]))
                    {{-- والد باز و هایلایت: route خودش یا هر زیرمنوی فعال --}}
                    @php($current = $current || $kids->contains('on', true))
                    <div class="pnl-group {{ $current ? 'is-open' : '' }}" data-pnl-group>
                        <button type="button" class="pnl-link {{ $current ? 'is-active' : '' }}" data-pnl-toggle
                                aria-expanded="{{ $current ? 'true' : 'false' }}" aria-controls="{{ $subId }}">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $link['d'] }}"/>
                            </svg>
                            <span class="pnl-text">{{ $link['label'] }}</span>
                            <svg class="pnl-chevron" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5"/>
                            </svg>
                        </button>

                        <div class="pnl-sub" id="{{ $subId }}" @unless($current) inert @endunless>
                            <div class="pnl-sub-in">
                                @foreach($kids as $child)
                                    <a href="{{ $child['href'] }}"
                                       class="pnl-sublink {{ $child['on'] ? 'is-active' : '' }}"
                                       @if($child['on']) aria-current="page" @endif>
                                        @if(! empty($child['d']))
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $child['d'] }}"/>
                                            </svg>
                                        @endif
                                        <span>{{ $child['label'] }}</span>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @continue
                @endif

                <a href="{{ route($link['route']) }}" class="pnl-link {{ $current ? 'is-active' : '' }}"
                   @if($current) aria-current="page" @endif>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $link['d'] }}"/>
                    </svg>
                    <span class="pnl-text">{{ $link['label'] }}</span>
                    @if(($link['badge'] ?? 0) > 0)
                        <span class="pnl-badge">{{ $link['badge'] }}</span>
                    @endif
                </a>
            @endforeach

            <div class="pnl-sep"></div>

            @foreach($footLinks as $link)
                <a href="{{ route($link['route']) }}" class="pnl-link">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $link['d'] }}"/>
                    </svg>
                    <span class="pnl-text">{{ $link['label'] }}</span>
                </a>
            @endforeach

            <form action="{{ route('logout') }}" method="POST" class="pnl-form" data-confirm="مطمئنی می‌خوای خارج بشی؟">
                @csrf
                <button type="submit" class="pnl-link is-danger">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $iconLogout }}"/>
                    </svg>
                    <span class="pnl-text">خروج از حساب</span>
                </button>
            </form>
        </div>
    </div>
</div>

{{-- ================= موبایل: نوار پایین (عمداً بیرون از .pnl تا backdrop-filter مانع fixed نشود) ================= --}}
<div class="pbar" role="navigation" aria-label="{{ $panelTitle }}">
    <div class="pbar-in">

        @foreach($barLinks as $link)
            @php($current = request()->routeIs($link['active']))
            <a href="{{ route($link['route']) }}" class="pbar-item {{ $current ? 'is-active' : '' }}"
               @if($current) aria-current="page" @endif>
                <span class="pbar-ic">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $link['d'] }}"/>
                    </svg>
                    @if(($link['badge'] ?? 0) > 0)
                        <i class="pbar-badge">{{ $link['badge'] }}</i>
                    @endif
                </span>
                <span class="pbar-tx">{{ $link['short'] ?? $link['label'] }}</span>
            </a>
        @endforeach

        <button type="button" id="pbar-more" class="pbar-item {{ $moreActive ? 'is-active' : '' }}"
                aria-haspopup="dialog" aria-expanded="false" aria-controls="psheet">
            <span class="pbar-ic">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.4" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $iconMore }}"/>
                </svg>
            </span>
            <span class="pbar-tx">بیشتر</span>
        </button>

    </div>
</div>

<div id="psheet-ov" class="psheet-ov" aria-hidden="true"></div>

<div id="psheet" class="psheet" role="dialog" aria-modal="true" aria-label="{{ $panelTitle }}" aria-hidden="true">
    <div class="psheet-in">
        <span class="psheet-grip" aria-hidden="true"></span>

        <div class="pnl-user">
            <span class="user-avatar is-lg">
                @if($u->avatar)
                    <img src="{{ Storage::url($u->avatar) }}" alt="">
                @else
                    {{ mb_strtoupper(mb_substr($u->name, 0, 1)) }}
                @endif
            </span>
            <div>
                <p class="pnl-name">{{ $u->name }}</p>
                <p class="pnl-role">{{ $roleLabel }}</p>
            </div>
        </div>

        @if(count($moreLinks))
            <p class="psheet-title">{{ $panelTitle }}</p>
            @foreach($moreLinks as $link)
                @php($current = request()->routeIs($link['active']))
                <a href="{{ route($link['route']) }}" class="pnl-link {{ $current ? 'is-active' : '' }}"
                   @if($current) aria-current="page" @endif>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $link['d'] }}"/>
                    </svg>
                    <span class="pnl-text">{{ $link['label'] }}</span>
                    @if(($link['badge'] ?? 0) > 0)
                        <span class="pnl-badge">{{ $link['badge'] }}</span>
                    @endif
                </a>
            @endforeach
            <div class="pnl-sep"></div>
        @endif

        @foreach($footLinks as $link)
            <a href="{{ route($link['route']) }}" class="pnl-link">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $link['d'] }}"/>
                </svg>
                <span class="pnl-text">{{ $link['label'] }}</span>
            </a>
        @endforeach

        <form action="{{ route('logout') }}" method="POST" class="pnl-form" data-confirm="مطمئنی می‌خوای خارج بشی؟">
            @csrf
            <button type="submit" class="pnl-link is-danger">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $iconLogout }}"/>
                </svg>
                <span class="pnl-text">خروج از حساب</span>
            </button>
        </form>
    </div>
</div>
