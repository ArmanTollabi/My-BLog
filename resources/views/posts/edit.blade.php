@extends('layout')
@section('title', 'ویرایش پست')
@section('content')

    <section class="max-w-4xl mx-auto px-6 py-12">

        <a href="{{ route('posts.show', $post) }}" class="btn-glass">
            <span aria-hidden="true">→</span> بازگشت به پست
        </a>

        <div class="my-8">
            <h1 class="text-3xl md:text-4xl font-bold text-white">ویرایش پست</h1>
            <p class="mt-2 text-white/60 truncate">{{ $post->title }}</p>
        </div>

        @foreach($errors->all() as $error)
            <x-alert type="error">{{ $error }}</x-alert>
        @endforeach

        <form action="{{ route('posts.update', $post) }}" method="POST" enctype="multipart/form-data"
              class="glass rounded-3xl p-6 md:p-8">
            @csrf
            @method('PUT')

            @include('posts._form', ['post' => $post])

            <div class="flex justify-end gap-3">
                <a href="{{ route('posts.show', $post) }}" class="btn-cancel">انصراف</a>
                <button type="submit" class="btn-update">ذخیره‌ی تغییرات</button>
            </div>
        </form>

    </section>

@endsection
