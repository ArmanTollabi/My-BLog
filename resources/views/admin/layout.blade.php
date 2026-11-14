{{-- FILE: resources/views/admin/layout.blade.php
     لی‌آوت صفحه‌های ادمین: قالب مشترک پنل‌ها (panel.layout) + سایدبار ادمین.
     صفحه‌های ادمین: @extends('admin.layout') و محتوا داخل @section('admin-content') --}}
@extends('panel.layout')

@section('panel-sidebar')
    @include('admin._sidebar')
@endsection

@section('panel-body')
    @yield('admin-content')
@endsection
