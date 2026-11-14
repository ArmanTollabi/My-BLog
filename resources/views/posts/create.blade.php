@extends('layout')
@section('title', 'ایجاد پست')
@section('content')

    <section class="max-w-4xl mx-auto px-6 py-12">

        <a href="{{ route('posts.index') }}" class="btn-glass">
            <span aria-hidden="true">→</span> بازگشت به پست‌ها
        </a>

        <div class="my-8">
            <h1 class="text-3xl md:text-4xl font-bold text-white">ایجاد پست جدید</h1>
            <p class="mt-2 text-white/60">یک پست تازه به وبلاگ اضافه کنید.</p>
        </div>

        @foreach($errors->all() as $error)
            <x-alert type="error">{{ $error }}</x-alert>
        @endforeach

        <form action="{{ route('posts.store') }}" method="POST" enctype="multipart/form-data"
              class="glass rounded-3xl p-6 md:p-8">
            @csrf

            @include('posts._form', ['post' => null])

            <div class="flex justify-end gap-3">
                <a href="{{ route('posts.index') }}" class="btn-cancel">انصراف</a>
                <button type="submit" class="btn-create">ذخیره‌ی پست</button>
            </div>
        </form>

    </section>

@endsection
