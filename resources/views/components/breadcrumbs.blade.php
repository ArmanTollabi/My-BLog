{{--
    مسیر صفحه (breadcrumb) — استایل: resources/css/breadcrumbs.css
    استفاده:
      <x-breadcrumbs :items="[
          ['label' => 'خانه',   'url' => route('home')],
          ['label' => 'پست‌ها', 'url' => route('posts.index')],
          ['label' => $post->title],              ← آخرین آیتم = صفحه‌ی فعلی (بدون لینک)
      ]" />
    خروجی شامل داده‌ی ساختاریافته‌ی BreadcrumbList (JSON-LD) برای سئو هم هست.
--}}
@props(['items'])

@php
    $items = array_values($items);
    $ld = [
        '@context' => 'https://schema.org',
        '@type'    => 'BreadcrumbList',
        'itemListElement' => collect($items)->map(fn ($item, $i) => array_filter([
            '@type'    => 'ListItem',
            'position' => $i + 1,
            'name'     => $item['label'],
            'item'     => $item['url'] ?? url()->current(),
        ]))->all(),
    ];
@endphp

<nav class="crumbs" aria-label="مسیر صفحه">
    <ol>
        @foreach($items as $item)
            @if($loop->last || empty($item['url']))
                <li class="crumbs-now" @if($loop->last) aria-current="page" @endif>
                    <span>{{ \Illuminate\Support\Str::limit($item['label'], 40) }}</span>
                </li>
            @else
                <li><a href="{{ $item['url'] }}">{{ $item['label'] }}</a></li>
            @endif
        @endforeach
    </ol>
</nav>

<script type="application/ld+json">{!! json_encode($ld, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG) !!}</script>
