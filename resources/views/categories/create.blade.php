@extends('layout')

@section('title', 'ایجاد دسته‌بندی')

@section('content')

    <div class="max-w-2xl mx-auto px-6 py-10">

        <a href="{{ route('categories.index') }}" class="btn-glass">
            <span aria-hidden="true">→</span> بازگشت به دسته‌بندی‌ها
        </a>

        <div class="my-8">
            <h1 class="text-3xl font-extrabold text-white">ایجاد دسته‌بندی</h1>
            <p class="mt-2 text-white/60">یک دسته‌بندی تازه برای پست‌هایتان بسازید.</p>
        </div>

        <div class="glass rounded-3xl p-6 md:p-8">
            <form action="{{ route('categories.store') }}" method="POST">
                @csrf

                <label for="name" class="mb-2 block text-sm font-bold text-white/85">نام دسته‌بندی</label>

                <input type="text" id="name" name="title" value="{{ old('title') }}" required
                       placeholder="عنوان دسته‌بندی را بنویسید…"
                       class="w-full rounded-2xl border border-white/20 bg-white/10 px-4 py-3 text-white
                              placeholder-white/40 outline-none transition
                              focus:border-violet-300 focus:ring-4 focus:ring-violet-500/30">

                @error('title')
                <p class="mt-2 text-sm text-rose-300">{{ $message }}</p>
                @enderror

                <div class="mt-8 flex items-center justify-end gap-3">
                    <a href="{{ route('categories.index') }}" class="btn-cancel">انصراف</a>
                    <button type="submit" class="btn-create">ایجاد</button>
                </div>
            </form>
        </div>

    </div>

@endsection
