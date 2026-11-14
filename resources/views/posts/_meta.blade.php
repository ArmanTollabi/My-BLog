{{--
    خط «نویسنده + تاریخ» (آواتار، نام، تاریخ شمسی). استایل: resources/css/post-meta.css
    ورودی‌ها:
      $post         → مدل پست (رابطه‌ی user باید لود شده باشد؛ برای آواتار ستون avatar هم لازم است)
      $size         → 'sm' (پیش‌فرض، کارت‌ها) | 'lg' (صفحه‌ی پست)
      $withReading  → true = زمان مطالعه هم نشان داده شود (فقط جایی که متن کامل لود شده)
    تاریخ: «تاریخ انتشار» (برای پیش‌نویس‌ها که هنوز منتشر نشده‌اند: تاریخ ساخت)
--}}
@php
    $size   = $size ?? 'sm';
    $author = $post->user;
    $name   = $author?->name ?? 'کاربر حذف‌شده';
    $date   = $post->published_at ?? $post->created_at;
@endphp
<div class="pmeta {{ $size === 'lg' ? 'pmeta-lg' : '' }}">
    <span class="pm-avatar" aria-hidden="true">
        @if($author?->avatar)
            <img src="{{ Storage::url($author->avatar) }}" alt="" width="64" height="64" loading="lazy" decoding="async">
        @else
            {{ mb_strtoupper(mb_substr($name, 0, 1)) }}
        @endif
    </span>
    <span class="pm-text">
        <span class="pm-name">{{ $name }}</span>
        <span class="pm-sub">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5"/>
            </svg>
            <time datetime="{{ $date?->toIso8601String() }}">{{ $post->published_at_fa ?? $post->created_at_fa }}</time>
            @if(! empty($withReading))
                <span class="pm-dot" aria-hidden="true">•</span>
                <span>{{ $post->reading_time_fa }} دقیقه مطالعه</span>
            @endif
        </span>
    </span>
</div>
