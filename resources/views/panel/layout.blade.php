{{-- FILE: resources/views/panel/layout.blade.php
     قالب مشترک «پنل‌ها» (داشبورد کاربر + پنل ادمین).
     استایل: resources/css/panel.css (بخش «قالب مشترک پنل‌ها») — رفتار (تاریخ امروز): resources/js/panel.js

     لی‌آوت‌های فرزند این دو section را پر می‌کنند:  panel-sidebar  و  panel-body --}}
@extends('layout')

@section('content')

    {{-- dir="rtl" روی همین قاب: ستون اول (سایدبار) همیشه سمت راست --}}
    <div dir="rtl" class="panel-shell">

        <div class="panel-side">
            @yield('panel-sidebar')
        </div>

        <div class="panel-main">
            <div class="panel-wrap">

                {{-- نوار بالا: خوش‌آمدگویی + تاریخ (تاریخ را panel.js پر می‌کند) --}}
                <div class="panel-top">
                    <div class="panel-top-text">
                        <p class="panel-hello">{{ auth()->user()->name }} عزیز؛ خوش آمدی <span aria-hidden="true">👋</span></p>
                        <p class="panel-date" data-panel-date></p>
                    </div>

                    <a href="{{ route('home') }}" class="btn-glass btn-sm">مشاهده‌ی سایت</a>
                </div>

                @yield('panel-body')
            </div>
        </div>

    </div>

@endsection
