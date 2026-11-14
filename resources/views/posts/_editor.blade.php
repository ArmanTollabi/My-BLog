{{--
    ادیتور متن پست (Quill). در create و edit با:
    @include('posts._editor', ['initial' => $html])
    $initial باید HTML پاک‌سازی‌شده باشد.
    منطق ادیتور: resources/js/post-form.js (با @vite از posts/_form لود می‌شود).
--}}
<div class="mb-8">
    <label class="block text-sm font-semibold text-gray-700 mb-2">متن پست</label>

    <div class="rte" id="rte" data-upload-url="{{ route('posts.upload-image') }}">
        <div id="editor">{!! $initial !!}</div>
    </div>
    <p id="rte-status" class="rte-status" role="status"></p>

    <input type="hidden" name="content" id="content">

    @error('content')
    <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
    @enderror
</div>
