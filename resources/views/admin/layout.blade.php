{{-- FILE: resources/views/admin/layout.blade.php
     لی‌آوتِ صفحه‌های ادمین: همان layout اصلی سایت + یک سایدبار مخصوص ادمین.
     صفحه‌های ادمین باید @extends('admin.layout') و محتوا را داخل @section('admin-content') بنویسند.

     ریسپانسیو:
       - زیر lg  : سایدبار به‌صورت نوار افقی بالای محتوا (تک‌ستونه)
       - lg به بالا: ستون ثابت کنار محتوا --}}
@extends('layout')

@section('content')

    <div class="mx-auto grid max-w-7xl grid-cols-1 items-start gap-4 px-4 py-6
                sm:gap-6 sm:px-6 sm:py-8
                lg:grid-cols-[14rem_minmax(0,1fr)] lg:py-10
                xl:grid-cols-[15rem_minmax(0,1fr)]">

        <div class="min-w-0 lg:self-start">
            @include('admin._sidebar')
        </div>

        <div class="min-w-0">
            @yield('admin-content')
        </div>

    </div>

@endsection
