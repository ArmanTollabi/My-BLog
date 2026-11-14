{{--
    فیلدهای مشترک فرم پست (برای ایجاد و ویرایش).
    ورودی:  $post  → null در حالت ایجاد، مدل پست در حالت ویرایش
--}}
@php
    $post = $post ?? null;
    $selectedTags = collect(old('tags', $post ? $post->tags->pluck('id')->all() : []))->map(fn ($v) => (int) $v)->all();
@endphp

{{-- عنوان --}}
<div class="mb-6">
    <label for="title" class="field-label">عنوان</label>
    <input type="text" id="title" name="title" required class="field"
           placeholder="عنوان پست را بنویسید…"
           value="{{ old('title', $post->title ?? '') }}">
</div>

{{-- دسته‌بندی --}}
<div class="mb-6">
    <label for="category" class="field-label">دسته‌بندی</label>
    <select id="category" name="category" class="field">
        <option value="">یک دسته‌بندی انتخاب کنید</option>
        @foreach($categories as $category)
            <option value="{{ $category->id }}"
                {{ (string) old('category', $post->category_id ?? '') === (string) $category->id ? 'selected' : '' }}>
                {{ $category->title }}
            </option>
        @endforeach
    </select>
</div>

{{-- تگ‌ها --}}
<div class="mb-6">
    <span class="field-label">تگ‌ها</span>

    <div id="tag-select" class="relative">

        <button type="button" id="tag-select-button"
                class="field min-h-[46px] flex flex-wrap items-center gap-2 text-start"
                aria-haspopup="listbox" aria-expanded="false" aria-controls="tag-dropdown">
            <span id="selected-tags" class="flex flex-1 flex-wrap gap-2">
                <span class="text-white/40">انتخاب تگ‌ها…</span>
            </span>
            <span class="text-white/50" aria-hidden="true">▼</span>
        </button>

        <div id="tag-dropdown"
             class="hidden absolute z-50 mt-2 w-full overflow-hidden rounded-2xl border border-white/20
                    bg-[#1a0f3d]/95 shadow-2xl backdrop-blur-xl">

            <div class="border-b border-white/10 p-2">
                <input type="text" id="tag-search" placeholder="جستجوی تگ…" class="field !py-2 text-sm">
            </div>

            <div id="tag-options" class="max-h-52 overflow-y-auto p-1">
                @foreach($tags as $tag)
                    <label class="tag-option flex cursor-pointer items-center gap-3 rounded-lg px-3 py-2"
                           data-name="{{ mb_strtolower($tag->title) }}">
                        <input type="checkbox" name="tags[]" value="{{ $tag->id }}" class="tag-checkbox"
                            {{ in_array($tag->id, $selectedTags) ? 'checked' : '' }}>
                        <span class="text-white/85">{{ $tag->title }}</span>
                    </label>
                @endforeach
            </div>
        </div>
    </div>

    @error('tags')
    <p class="mt-2 text-sm text-rose-300">{{ $message }}</p>
    @enderror
</div>

{{-- ادیتور متن --}}
@include('posts._editor', ['initial' => \App\Support\HtmlSanitizer::clean(old('content', $post?->editor_html))])

{{-- تصویر پست --}}
<div class="mb-8">
    <span class="field-label">تصویر پست</span>

    <div id="upload-box" data-upload-url="{{ route('posts.upload-image') }}" class="rounded-2xl border-2 border-dashed border-white/25 bg-white/5 p-6 text-center transition-colors">

        <input type="file" id="image-input" accept="image/*" class="hidden">

        <label for="image-input" class="cursor-pointer">
            <img id="image-preview"
                 src="{{ ($post && $post->image) ? Storage::url($post->image) : '' }}"
                 alt="پیش‌نمایش تصویر"
                 class="{{ ($post && $post->image) ? '' : 'hidden' }} mx-auto mb-3 max-h-48 rounded-xl">

            <div id="upload-placeholder" class="{{ ($post && $post->image) ? 'hidden' : '' }} text-sm text-white/50">
                {{ $post ? 'برای انتخاب تصویر جدید کلیک کنید' : 'برای انتخاب تصویر کلیک کنید' }}
            </div>
        </label>

        <div id="progress-wrapper" class="hidden mt-4">
            <div class="h-2 w-full overflow-hidden rounded-full bg-white/15">
                <div id="progress-bar" class="h-2 rounded-full bg-gradient-to-l from-violet-400 to-cyan-400 transition-all" style="width: 0%"></div>
            </div>
            <p id="progress-text" class="mt-2 text-xs text-white/60">0%</p>
        </div>

        <p id="upload-success" class="hidden mt-3 text-sm font-semibold text-emerald-300">✓ تصویر آپلود شد</p>
    </div>

    {{-- مسیر تصویر آپلودشده؛ همراه فرم اصلی ارسال می‌شود --}}
    <input type="hidden" name="image_path" id="image_path" value="{{ old('image_path') }}">

    @error('image')
    <p class="mt-2 text-xs text-rose-300">{{ $message }}</p>
    @enderror
</div>

{{-- وضعیت انتشار (رفتار و تقویم جلالی: resources/js/post-status.js) --}}
@php
    $statusValue = old('status', $post?->effective_status->value ?? \App\Enums\PostStatus::Published->value);
    $scheduledAt = old('scheduled_at', $post?->scheduled_at_jalali ?? '');
@endphp

<div class="mb-8" data-post-status>
    <label for="status" class="field-label">وضعیت انتشار</label>

    <select id="status" name="status" class="field" data-status-select>
        @foreach(\App\Enums\PostStatus::cases() as $s)
            <option value="{{ $s->value }}" data-hint="{{ $s->hint() }}" {{ $statusValue === $s->value ? 'selected' : '' }}>
                {{ $s->label() }}
            </option>
        @endforeach
    </select>
    <p class="mt-2 text-xs text-white/55" data-status-hint></p>

    @error('status')
    <p class="mt-2 text-sm text-rose-300">{{ $message }}</p>
    @enderror

    {{-- فقط برای «زمان‌بندی‌شده»: تقویم جلالی + ساعت و دقیقه --}}
    <div class="mt-5 {{ $statusValue === 'scheduled' ? '' : 'hidden' }}" data-schedule-box>
        <label for="scheduled_at" class="field-label">زمان انتشار (تاریخ جلالی و ساعت)</label>
        <input type="text" id="scheduled_at" name="scheduled_at" class="field text-left" dir="ltr"
               autocomplete="off" inputmode="none" placeholder="1405/08/24 10:00"
               data-jdp value="{{ $scheduledAt }}">
        <p class="mt-2 text-xs text-white/45">روی فیلد کلیک کن؛ روز، ساعت و دقیقه را انتخاب کن. پست در همان لحظه خودکار منتشر می‌شود.</p>

        @error('scheduled_at')
        <p class="mt-2 text-sm text-rose-300">{{ $message }}</p>
        @enderror
    </div>
</div>

@push('scripts')
    @vite('resources/js/post-form.js')
@endpush
