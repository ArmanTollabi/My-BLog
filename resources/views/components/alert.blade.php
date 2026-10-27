@props(['type' => 'success', 'title' => null])

@php
    $type = in_array($type, ['success', 'error', 'warning', 'info']) ? $type : 'info';

    $titles = [
        'success' => 'Success!',
        'error'   => 'Error!',
        'warning' => 'Warning!',
        'info'    => 'Info',
    ];

    // پیام‌های موفقیت و اطلاعات خودکار محو می‌شوند؛ خطا و هشدار می‌مانند تا دیده شوند
    $auto = in_array($type, ['success', 'info']);
@endphp

<div
    role="{{ $type === 'error' ? 'alert' : 'status' }}"
    {{ $attributes->class(['alert', 'alert-' . $type, 'alert-auto' => $auto]) }}
>
    {{-- آیکن --}}
    <svg class="alert-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
         stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
        @switch($type)
            @case('success')
                <path d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                @break
            @case('error')
                <path d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/>
                @break
            @case('warning')
                <path d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
                @break
            @default
                <path d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"/>
        @endswitch
    </svg>

    {{-- متن --}}
    <div class="alert-body">
        <strong class="alert-title">{{ $title ?? $titles[$type] }}</strong>
        <span class="alert-text">{{ $slot }}</span>
    </div>

    {{-- دکمه بستن --}}
    <button type="button" class="alert-close" aria-label="Close"
            onclick="this.closest('.alert').remove()">
        <svg class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
            <path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z"/>
        </svg>
    </button>
</div>
