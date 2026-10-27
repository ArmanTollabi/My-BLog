@extends('layout')
@section('title','edit-Post')
@section('content')
    <section class="max-w-4xl mx-auto px-6 py-12"> <!-- Page Header -->
        <a
            href="{{ route('posts.index') }}"
            class="inline-flex items-center
                       px-5 py-2.5
                       border border-gray-300
                       text-gray-700
                       rounded-lg
                       font-semibold
                       hover:bg-gray-50
                       transition"
        >
            ← Back to Posts
        </a>
        <div class="mb-8">
            <h1 class="text-3xl md:text-4xl font-bold text-gray-900">
                Edit Post : {{$post->title}}
            </h1>
        </div>
        <!-- Form -->
        @if($errors->any())
            @foreach($errors->all() as $error)

                <x-alert type="error">{{$error}}</x-alert>
            @endforeach
        @endif
        <form action="{{route('posts.update',$post)}}" method="POST" enctype="multipart/form-data" class="bg-white border border-gray-200 rounded-2xl p-6 md:p-8 shadow-sm">
            @csrf
            @method('PUT')
            <!-- Title -->
            <div class="mb-6">
                <label for="title" class="block text-sm font-semibold text-gray-700 mb-2">
                    Title
                </label>
                <input type="text" id="title" name="title" placeholder="Enter post title..." value="{{old('title',$post->title)}}" required class="w-full px-4 py-3 border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
            </div>
            <!-- Category -->
            <div class="mb-6">
                <label for="category" class="block text-sm font-semibold text-gray-700 mb-2">
                    Category
                </label>
                <select id="category" name="category"  class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-white outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                    <option value="">Select a category</option>
                    @foreach($categories as $category)
                        <option value="{{$category->id}}" {{ (string) old('category', $post->category_id) === (string) $category->id ? 'selected' : '' }}>{{$category->title}}</option>
                    @endforeach
                </select>
            </div>
            {{-- Tags --}}
            <div class="mb-6">

                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Tags
                </label>

                <div id="tag-select" class="relative">

                    {{-- Selected tags button --}}
                    <button
                        type="button"
                        id="tag-select-button"
                        class="w-full min-h-[46px] bg-white border border-gray-300
                   rounded-lg px-3 py-2
                   flex items-center gap-2 flex-wrap
                   text-left
                   focus:outline-none focus:ring-2 focus:ring-purple-500"
                    >

            <span id="selected-tags" class="flex flex-wrap gap-2 flex-1">
                <span class="text-gray-400">
                    Select tags...
                </span>
            </span>

                        <span class="text-gray-400">
                ▼
            </span>

                    </button>


                    {{-- Dropdown --}}
                    <div
                        id="tag-dropdown"
                        class="hidden absolute z-50 mt-2 w-full
                   bg-white border border-gray-200
                   rounded-lg shadow-lg overflow-hidden"
                    >

                        {{-- Search --}}
                        <div class="p-2 border-b border-gray-200">

                            <input
                                type="text"
                                id="tag-search"
                                placeholder="Search tags..."
                                class="w-full border border-gray-300 rounded-md
                           px-3 py-2 text-sm
                           focus:outline-none
                           focus:ring-2 focus:ring-purple-500"
                            >

                        </div>


                        {{-- Tags --}}
                        <div
                            id="tag-options"
                            class="max-h-52 overflow-y-auto p-1"
                        >

                            @foreach($tags as $tag)

                                <label
                                    class="tag-option flex items-center gap-3
                               px-3 py-2 rounded-md
                               cursor-pointer
                               hover:bg-gray-100"
                                    data-name="{{ strtolower($tag->title) }}"
                                >

                                    <input
                                        type="checkbox"
                                        name="tags[]"
                                        value="{{ $tag->id }}"
                                        class="tag-checkbox
                                   rounded border-gray-300
                                   text-purple-600
                                   focus:ring-purple-500"
                                        {{ in_array($tag->id, old('tags', $post->tags->pluck('id')->all())) ? 'checked' : '' }}
                                    >

                                    <span class="text-gray-700">
                            {{ $tag->title }}
                        </span>

                                </label>

                            @endforeach

                        </div>

                    </div>

                </div>


                @error('tags')
                <p class="text-red-500 text-sm mt-2">
                    {{ $message }}
                </p>
                @enderror

            </div>
            @include('posts._editor', ['initial' => \App\Support\HtmlSanitizer::clean(old('content', $post->editor_html))])
            {{-- Current / New Image --}}
            <div class="mb-8">

                <label class="block text-sm font-bold mb-2">
                    تصویر پست
                </label>

                <div id="upload-box"
                     class="border-2 border-dashed border-stone-300 rounded-xl p-6 text-center transition-colors">

                    <input type="file"
                           id="image-input"
                           accept="image/*"
                           class="hidden">

                    <label for="image-input" class="cursor-pointer">

                        {{-- Preview --}}
                        <img id="image-preview"
                             src="{{ $post->image ? Storage::url($post->image) : '' }}"
                             alt="پیش‌نمایش"
                             class="{{ $post->image ? '' : 'hidden' }} max-h-48 mx-auto rounded-lg mb-3">

                        {{-- Placeholder --}}
                        <div id="upload-placeholder"
                             class="{{ $post->image ? 'hidden' : '' }} text-stone-400 text-sm">
                            برای انتخاب تصویر جدید کلیک کن
                        </div>

                    </label>

                    {{-- Progress --}}
                    <div id="progress-wrapper" class="hidden mt-4">

                        <div class="w-full bg-stone-200 rounded-full h-2 overflow-hidden">
                            <div id="progress-bar"
                                 class="bg-stone-900 h-2 rounded-full"
                                 style="width: 0%">
                            </div>
                        </div>

                        <p id="progress-text"
                           class="text-xs text-stone-500 mt-2">
                            0%
                        </p>

                    </div>

                    {{-- Success --}}
                    <p id="upload-success"
                       class="hidden text-emerald-600 text-sm font-semibold mt-3">
                        ✓ تصویر جدید آپلود شد
                    </p>

                </div>

                {{-- مسیر تصویر جدید --}}
                <input type="hidden"
                       name="image_path"
                       id="image_path"
                       value="{{ old('image_path') }}">

            </div>
            <div class="flex justify-end gap-3">
                <a  href="{{route('posts.index')}}" class="btn-cancel">
                    Cancel
                </a>
                <button type="submit" class="btn-update">
                    Update Post
                </button>
            </div>
        </form>
    </section>

@endsection
@push('scripts')

    <script>

        const imageInput = document.getElementById('image-input');

        const uploadBox = document.getElementById('upload-box');

        const uploadPlaceholder =
            document.getElementById('upload-placeholder');

        const imagePreview =
            document.getElementById('image-preview');

        const progressWrapper =
            document.getElementById('progress-wrapper');

        const progressBar =
            document.getElementById('progress-bar');

        const progressText =
            document.getElementById('progress-text');

        const uploadSuccess =
            document.getElementById('upload-success');

        const imagePathInput =
            document.getElementById('image_path');


        imageInput.addEventListener('change', function () {

            const file = this.files[0];

            if (!file) {
                return;
            }


            // -----------------------------
            // نمایش Preview
            // -----------------------------

            const reader = new FileReader();

            reader.onload = function (e) {

                imagePreview.src = e.target.result;

                imagePreview.classList.remove('hidden');

                uploadPlaceholder.classList.add('hidden');

            };

            reader.readAsDataURL(file);


            // -----------------------------
            // Reset وضعیت قبلی
            // -----------------------------

            uploadSuccess.classList.add('hidden');

            progressWrapper.classList.remove('hidden');

            progressBar.style.width = '0%';

            progressText.textContent = '0%';


            // -----------------------------
            // ساخت FormData
            // -----------------------------

            const formData = new FormData();

            formData.append('image', file);

            formData.append(
                '_token',
                document.querySelector('input[name="_token"]').value
            );


            // -----------------------------
            // AJAX
            // -----------------------------

            const xhr = new XMLHttpRequest();

            xhr.open(
                'POST',
                '{{ route("posts.upload-image") }}',
                true
            );


            // -----------------------------
            // Progress
            // -----------------------------

            xhr.upload.addEventListener('progress', function (e) {

                if (e.lengthComputable) {

                    const percent =
                        Math.round((e.loaded / e.total) * 100);

                    progressBar.style.width =
                        percent + '%';

                    progressText.textContent =
                        percent + '%';
                }

            });


            // -----------------------------
            // Response
            // -----------------------------

            xhr.onload = function () {

                if (xhr.status === 200) {

                    const response =
                        JSON.parse(xhr.responseText);


                    // مسیر عکس جدید
                    imagePathInput.value =
                        response.path;


                    progressWrapper.classList.add('hidden');

                    uploadSuccess.classList.remove('hidden');

                    uploadBox.classList.remove(
                        'border-stone-300'
                    );

                    uploadBox.classList.add(
                        'border-emerald-500'
                    );

                } else {

                    progressText.textContent =
                        'خطا در آپلود، دوباره تلاش کن.';

                }

            };


            // -----------------------------
            // Connection Error
            // -----------------------------

            xhr.onerror = function () {

                progressText.textContent =
                    'خطا در اتصال به سرور.';

            };


            xhr.send(formData);

        });

    </script>
    <script>

        const tagSelectButton =
            document.getElementById('tag-select-button');

        const tagDropdown =
            document.getElementById('tag-dropdown');

        const tagSearch =
            document.getElementById('tag-search');

        const tagCheckboxes =
            document.querySelectorAll('.tag-checkbox');

        const selectedTags =
            document.getElementById('selected-tags');


        // Open / close dropdown
        tagSelectButton.addEventListener('click', function () {

            tagDropdown.classList.toggle('hidden');

        });


        // Update selected tags
        tagCheckboxes.forEach(function (checkbox) {

            checkbox.addEventListener('change', function () {

                updateSelectedTags();

            });

        });


        function updateSelectedTags()
        {
            selectedTags.innerHTML = '';

            const checkedTags =
                document.querySelectorAll('.tag-checkbox:checked');


            if (checkedTags.length === 0) {

                selectedTags.innerHTML = `
                <span class="text-gray-400">
                    Select tags...
                </span>
            `;

                return;
            }


            checkedTags.forEach(function (checkbox) {

                const label =
                    checkbox.closest('.tag-option');

                const tagName =
                    label.querySelector('span').textContent.trim();


                const tag = document.createElement('span');

                tag.className =
                    'bg-purple-100 text-purple-700 px-2 py-1 rounded-md text-sm';

                tag.textContent = tagName;


                selectedTags.appendChild(tag);

            });

        }


        // Search tags
        tagSearch.addEventListener('input', function () {

            const search =
                this.value.toLowerCase();


            document.querySelectorAll('.tag-option')
                .forEach(function (option) {

                    const name =
                        option.dataset.name;


                    if (name.includes(search)) {

                        option.classList.remove('hidden');

                    } else {

                        option.classList.add('hidden');

                    }

                });

        });


        // Close when clicking outside
        document.addEventListener('click', function (event) {

            if (!document
                .getElementById('tag-select')
                .contains(event.target)) {

                tagDropdown.classList.add('hidden');

            }

        });
        updateSelectedTags();

    </script>

@endpush
