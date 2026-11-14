@extends('layout')

@section('title', 'صفحه پیدا نشد')

@section('content')
    @include('errors._error-card', [
        'code'    => '404',
        'title'   => 'صفحه‌ای که دنبالش بودی پیدا نشد',
        'message' => 'ممکنه آدرس رو اشتباه وارد کرده باشی یا این صفحه حذف یا جابه‌جا شده باشه.',
        'icon'    => '<svg class="h-9 w-9 text-violet-200" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/></svg>',
    ])
@endsection
