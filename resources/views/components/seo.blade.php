{{--
    سئو و اشتراک‌گذاری: meta description، canonical، Open Graph (تلگرام/واتس‌اپ/…)، Twitter Card و برای مقاله JSON-LD.
    در بخش content هر صفحه صدا بزن (خروجی با @push('head') داخل <head> لیاوت می‌نشیند؛ لیاوت باید @stack('head') داشته باشد).

    <x-seo :title="$post->title" :description="$post->description" :image="$post->image_url" type="article"
           :published="$post->created_at" :modified="$post->updated_at" :author="$post->user?->name" />

    props: title, description, image (مسیر یا URL کامل)، type (website|article)، published/modified (تاریخ)،
           author، noindex (برای صفحه‌های فیلتر/جستجو)، canonical (اختیاری)
--}}
@props([
    'title' => null, 'description' => null, 'image' => null, 'type' => 'website',
    'published' => null, 'modified' => null, 'author' => null, 'noindex' => false, 'canonical' => null,
])

@php
    $site  = config('app.name');
    $title = $title ?: $site;
    $desc  = \Illuminate\Support\Str::limit(trim(preg_replace('/\s+/u', ' ', strip_tags((string) $description))), 160);
    $image = $image ? (\Illuminate\Support\Str::startsWith($image, ['http://', 'https://']) ? $image : url($image)) : null;

    // canonical: بدون پارامترهای فیلتر؛ فقط شماره‌ی صفحه‌ی صفحه‌بندی
    $canonical = $canonical ?? url()->current() . (request()->integer('page') > 1 ? '?page=' . request()->integer('page') : '');

    $ld = $type === 'article' ? array_filter([
        '@context'      => 'https://schema.org',
        '@type'         => 'BlogPosting',
        'headline'      => $title,
        'description'   => $desc ?: null,
        'image'         => $image,
        'datePublished' => $published?->toIso8601String(),
        'dateModified'  => ($modified ?? $published)?->toIso8601String(),
        'author'        => $author ? ['@type' => 'Person', 'name' => $author] : null,
        'publisher'     => ['@type' => 'Organization', 'name' => $site],
        'mainEntityOfPage' => $canonical,
        'inLanguage'    => 'fa-IR',
    ]) : null;
@endphp

@push('head')
    @if($desc)<meta name="description" content="{{ $desc }}">@endif
    <link rel="canonical" href="{{ $canonical }}">
    @if($noindex)<meta name="robots" content="noindex,follow">@endif

    <meta property="og:locale" content="fa_IR">
    <meta property="og:site_name" content="{{ $site }}">
    <meta property="og:type" content="{{ $type }}">
    <meta property="og:title" content="{{ $title }}">
    @if($desc)<meta property="og:description" content="{{ $desc }}">@endif
    <meta property="og:url" content="{{ $canonical }}">
    @if($image)<meta property="og:image" content="{{ $image }}">@endif
    @if($type === 'article' && $published)<meta property="article:published_time" content="{{ $published->toIso8601String() }}">@endif

    <meta name="twitter:card" content="{{ $image ? 'summary_large_image' : 'summary' }}">
    <meta name="twitter:title" content="{{ $title }}">
    @if($desc)<meta name="twitter:description" content="{{ $desc }}">@endif
    @if($image)<meta name="twitter:image" content="{{ $image }}">@endif

    @if($ld)<script type="application/ld+json">{!! json_encode($ld, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG) !!}</script>@endif
@endpush
