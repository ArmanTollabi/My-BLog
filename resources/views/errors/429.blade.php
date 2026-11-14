@extends('layout')

@section('title', 'درخواست بیش از حد')

@section('content')
    @include('errors._error-card', [
        'code'    => '429',
        'title'   => 'تعداد درخواست‌هات زیاده',
        'message' => 'در مدت کوتاهی درخواست‌های زیادی ارسال کردی. چند دقیقه صبر کن و دوباره تلاش کن.',
        'icon'    => '<svg class="h-9 w-9 text-orange-300" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M15.362 5.214A8.252 8.252 0 0 1 12 21 8.25 8.25 0 0 1 6.038 7.047 8.287 8.287 0 0 0 9 9.601a8.983 8.983 0 0 1 3.361-6.867 8.21 8.21 0 0 0 3 2.48Z"/></svg>',
    ])
@endsection
