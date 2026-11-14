@extends('layout')

@section('title', 'نشست منقضی شد')

@section('content')
    @include('errors._error-card', [
        'code'    => '419',
        'title'   => 'نشست شما منقضی شده',
        'message' => 'مدت زیادی از باز شدن این صفحه گذشته. صفحه رو رفرش کن و دوباره امتحان کن.',
        'icon'    => '<svg class="h-9 w-9 text-cyan-300" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>',
    ])
@endsection
