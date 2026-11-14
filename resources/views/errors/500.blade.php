@extends('layout')

@section('title', 'خطای سرور')

@section('content')
    @include('errors._error-card', [
        'code'    => '500',
        'title'   => 'مشکلی در سرور پیش اومد',
        'message' => 'یه خطای غیرمنتظره رخ داد و ما در حال بررسیش هستیم. لطفاً چند لحظه‌ی دیگه دوباره تلاش کن.',
        'icon'    => '<svg class="h-9 w-9 text-amber-300" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/></svg>',
    ])
@endsection
