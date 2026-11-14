{{-- FILE: resources/views/dashboard/layout.blade.php
     لی‌آوت داشبورد کاربر: قالب مشترک پنل‌ها (panel.layout) + سایدبار داشبورد.
     هر صفحه: @extends('dashboard.layout') و محتوا داخل @section('dash-content')
     عنوان صفحه: @section('dash-title', '...') و (اختیاری) @section('dash-subtitle', '...') --}}
@extends('panel.layout')

@section('panel-sidebar')
    @include('dashboard._sidebar')
@endsection

@section('panel-body')
    <div class="mb-6">
        <h1 class="text-2xl font-extrabold text-white sm:text-3xl">@yield('dash-title', 'داشبورد من')</h1>
        @hasSection('dash-subtitle')
            <p class="mt-1 text-white/60">@yield('dash-subtitle')</p>
        @endif
    </div>

    @yield('dash-content')
@endsection
