@extends('layout')

@section('title', $category->title)

@section('content')

    <x-seo :title="$category->title" :description="'پست‌های دسته‌ی ' . $category->title" />

    <div class="max-w-6xl mx-auto px-6 py-8">

        <x-breadcrumbs :items="[
            ['label' => 'خانه', 'url' => route('home')],
            ['label' => 'دسته‌بندی‌ها', 'url' => route('categories.index')],
            ['label' => $category->title],
        ]" />

        <div class="mb-8">

            <h1 class="text-3xl font-extrabold text-white">
                {{ $category->title }}
            </h1>



        </div>

        @if($posts->isNotEmpty())

            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">

                @foreach($posts as $post)
                    @include('posts._card', ['post' => $post])
                @endforeach

            </div>

        @else

            <div class="glass rounded-3xl p-10 text-center text-white/60">
                هنوز پستی در این دسته‌بندی منتشر نشده است.
            </div>

        @endif

    </div>

@endsection
