{{-- FILE: resources/views/admin/comments/_tabs.blade.php --}}
{{-- تب‌های بالای صفحه‌ی مدیریت کامنت‌ها. ورودی‌ها: $counts, $active (all|pending|approved|trash) --}}
@php
    $tabs = [
        'all'      => ['label' => 'همه',            'url' => route('admin.comments.index')],
        'pending'  => ['label' => 'در انتظار تایید', 'url' => route('admin.comments.index', ['status' => 'pending'])],
        'approved' => ['label' => 'تایید شده',       'url' => route('admin.comments.index', ['status' => 'approved'])],
        'trash'    => ['label' => 'سطل زباله',       'url' => route('admin.comments.trash')],
    ];
@endphp

<div class="flex flex-wrap items-center gap-2 mb-6">
    @foreach($tabs as $key => $tab)
        <a href="{{ $tab['url'] }}"
           class="{{ $active === $key ? 'btn-create' : 'btn-glass' }} btn-sm"
           @if($active === $key) aria-current="page" @endif>
            {{ $tab['label'] }}
            <span class="opacity-70">({{ $counts[$key] }})</span>
        </a>
    @endforeach
</div>
