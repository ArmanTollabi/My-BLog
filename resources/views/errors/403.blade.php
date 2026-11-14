@extends('layout')

@section('title', 'دسترسی غیرمجاز')

@section('content')
    @include('errors._error-card', [
        'code'    => '403',
        'title'   => 'اجازه‌ی دسترسی به این صفحه رو نداری',
        'message' => 'برای دیدن این بخش باید سطح دسترسی لازم رو داشته باشی. اگه فکر می‌کنی اشتباهی رخ داده، با مدیر سایت تماس بگیر.',
        'icon'    => '<svg class="h-9 w-9 text-rose-300" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z"/></svg>',
    ])
@endsection
