@if ($paginator->hasPages())
    @php
        $fa = fn ($n) => strtr((string) $n, ['0' => '۰', '1' => '۱', '2' => '۲', '3' => '۳', '4' => '۴', '5' => '۵', '6' => '۶', '7' => '۷', '8' => '۸', '9' => '۹']);
    @endphp

    {{-- از div استفاده شده (نه nav) تا استایل خودکار nav روی آن اعمال نشود --}}
    <div class="glass-pager glass" role="navigation" aria-label="صفحه‌بندی">

        {{-- قبلی (در چیدمان راست‌به‌چپ سمت راست است) --}}
        @if ($paginator->onFirstPage())
            <span class="pg pg-arrow disabled" aria-disabled="true" aria-label="قبلی">
                  <svg width="18" height="18" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M12.79 5.23a.75.75 0 01-.02 1.06L8.83 10l3.94 3.71a.75.75 0 11-1.04 1.08l-4.5-4.25a.75.75 0 010-1.08l4.5-4.25a.75.75 0 011.06.02z" clip-rule="evenodd"/></svg>
            </span>
        @else
            <a class="pg pg-arrow" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="قبلی">
                <svg width="18" height="18" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M12.79 5.23a.75.75 0 01-.02 1.06L8.83 10l3.94 3.71a.75.75 0 11-1.04 1.08l-4.5-4.25a.75.75 0 010-1.08l4.5-4.25a.75.75 0 011.06.02z" clip-rule="evenodd"/></svg>
            </a>
        @endif

        {{-- شماره صفحه‌ها --}}
        @foreach ($elements as $element)
            @if (is_string($element))
                <span class="pg dots" aria-hidden="true">…</span>
            @endif

            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span class="pg active" aria-current="page">{{ $fa($page) }}</span>
                    @else
                        <a class="pg" href="{{ $url }}" aria-label="صفحه {{ $fa($page) }}">{{ $fa($page) }}</a>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- بعدی (سمت چپ) --}}
        @if ($paginator->hasMorePages())
            <a class="pg pg-arrow" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="بعدی">
                <svg width="18" height="18" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.17 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd"/></svg>
            </a>
        @else
            <span class="pg pg-arrow disabled" aria-disabled="true" aria-label="بعدی">
                <svg width="18" height="18" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.17 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd"/></svg>
            </span>
        @endif
    </div>
@endif
