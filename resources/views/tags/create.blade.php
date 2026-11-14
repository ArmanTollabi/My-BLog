@extends('layout')

@section('title', 'ایجاد تگ')

@section('content')

    <div class="max-w-2xl mx-auto px-6 py-8">

        <div class="mb-8">
            <h1 class="text-3xl font-extrabold text-white">ایجاد تگ</h1>
            <p class="text-white/60 mt-2">یک تگ جدید برای دسته‌بندی پست‌ها اضافه کنید.</p>
        </div>

        <form action="{{ route('tags.store') }}" method="POST" class="glass rounded-3xl p-6 md:p-8">
            @csrf

            <div class="mb-6">
                <label for="title" class="field-label">نام تگ</label>
                <input type="text" name="title" id="title" value="{{ old('title') }}" required maxlength="255"
                       class="field @error('title') !border-rose-400/70 @enderror"
                       placeholder="نام تگ را وارد کنید">
                @error('title')
                    <p class="text-rose-300 text-sm mt-2">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center gap-3">
                <button type="submit" class="btn-create">ایجاد تگ</button>
                <a href="{{ route('tags.index') }}" class="btn-cancel">انصراف</a>
            </div>
        </form>

    </div>

@endsection
