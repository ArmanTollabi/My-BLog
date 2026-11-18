@extends('layout')
@section('title','ویرایش کاربر')
@section('content')
    <section class="max-w-3xl mx-auto px-6 py-12"> <!-- Page Header -->
        <div class="mb-8">
            <h1 class="text-3xl md:text-4xl font-bold text-white">
                ویرایش کاربر : {{$user->name}}
            </h1>
        </div>

        <!-- Errors -->
        @if($errors->any())
            @foreach($errors->all() as $error)

                <x-alert type="error">{{$error}}</x-alert>
            @endforeach
        @endif

        <!-- Form -->
        <form action="{{route('admin.users.update',$user)}}" method="POST" class="glass rounded-3xl p-6 md:p-8">
            @csrf
            @method('PUT')

            <!-- Name -->
            <div class="mb-6">
                <label for="name" class="block text-sm font-semibold text-white mb-2">
                    نام کاربر
                </label>
                <input type="text" id="name" name="name" placeholder="نام را وارد کنید"
                       value="{{ old('name', $user->name) }}" required
                       class="glass-input @error('name') !border-rose-400/70 @enderror">
            </div>

            <!-- Email -->
            <div class="mb-6">
                <label for="email" class="block text-sm font-semibold text-white mb-2">
                    ایمیل
                </label>
                <input type="email" id="email" name="email" placeholder="ایمیل را وارد کنید"
                       value="{{ old('email', $user->email) }}" required dir="ltr"
                       class="glass-input text-left @error('email') !border-rose-400/70 @enderror">
            </div>

            {{-- User role: فقط ادمین‌ها این بخش را می‌بینند؛ فقط ادمین اصلی (id=1) می‌تواند تغییرش بدهد --}}
            @if(auth()->user()->isAdmin())
                @php($isSelf = auth()->id() === $user->id)
                <div class="mb-6">
                    <label for="role" class="block text-sm font-semibold text-white mb-2">
                        نقش کاربر
                    </label>

                    @can('edit_admin', App\Models\User::class)
                        <select id="role" name="role" @disabled($isSelf) required
                                class="glass-input @error('role') !border-rose-400/70 @enderror">
                            <option value="">نقش کاربر را انتخاب کنید</option>
                            @foreach(\App\Enums\UserRole::cases() as $role)
                                <option value="{{ $role->value }}" @selected(old('role', $user->role) === $role->value)>{{ $role->label() }}</option>
                            @endforeach
                        </select>

                        @if($isSelf)
                            <p class="text-white/50 text-xs mt-1">نقش حساب خودت را نمی‌توانی تغییر بدهی.</p>
                        @endif
                    @else
                        {{-- ادمین‌های دیگر: فقط نمایش، بدون امکان تغییر --}}
                        <p class="glass-input opacity-70 cursor-not-allowed">
                            {{ \App\Enums\UserRole::tryFrom((string) $user->role)?->label() ?? '—' }}
                        </p>
                    @endcan

                    @error('role')
                    <p class="text-rose-300 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            @endif

            {{-- Current / New Image --}}
            <div class="mb-8">

                <label class="block text-sm font-bold text-white mb-2">
                    آواتار
                </label>

                <div id="upload-box" data-avatar-upload data-upload-url="{{ route('admin.users.upload-avatar') }}"
                     class="border-2 border-dashed border-white/30 rounded-2xl bg-white/5 p-6 text-center
                            transition-colors hover:bg-white/10">

                    <input type="file"
                           id="avatar-input"
                           accept="image/*"
                           class="hidden">

                    <label for="avatar-input" class="cursor-pointer block">

                        {{-- Preview --}}
                        <img id="avatar-preview"
                             src="{{ $user->avatar ? Storage::url($user->avatar) : '' }}"
                             alt="پیش‌نمایش"
                             class="{{ $user->avatar ? '' : 'hidden' }} w-32 h-32 object-cover mx-auto rounded-full
                                    border-2 border-white/40 shadow-lg shadow-violet-500/30 mb-3">

                        {{-- Placeholder --}}
                        <div id="upload-placeholder"
                             class="{{ $user->avatar ? 'hidden' : '' }} text-white/55 text-sm">
                            <svg class="w-10 h-10 mx-auto mb-2 text-white/40" xmlns="http://www.w3.org/2000/svg"
                                 fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5"/>
                            </svg>
                            برای انتخاب آواتار جدید کلیک کن
                        </div>

                        @if($user->avatar)
                            <span class="text-white/55 text-xs">برای تغییر عکس کلیک کن</span>
                        @endif

                    </label>

                    {{-- Progress --}}
                    <div id="progress-wrapper" class="hidden mt-4">

                        <div class="w-full bg-white/15 rounded-full h-2 overflow-hidden">
                            <div id="progress-bar"
                                 class="bg-gradient-to-r from-violet-500 to-cyan-400 h-2 rounded-full transition-all"
                                 style="width: 0%">
                            </div>
                        </div>

                        <p id="progress-text"
                           class="text-xs text-white/60 mt-2">
                            0%
                        </p>

                    </div>

                    {{-- Success --}}
                    <p id="upload-success"
                       class="hidden text-emerald-300 text-sm font-semibold mt-3">
                        ✓ تصویر جدید آپلود شد
                    </p>

                </div>

                {{-- مسیر تصویر جدید --}}
                <input type="hidden"
                       name="avatar_path"
                       id="avatar_path"
                       value="{{ old('avatar_path') }}">

            </div>

            {{-- Password --}}
            <div class="mb-8">
                <hr class="my-6 border-white/10">
                <p class="text-sm text-white/55 mb-4">برای تغییر رمز عبور، هر دو فیلد زیر را پر کن. در غیر این صورت خالی بگذار.</p>

                <div class="mb-6">
                    <label for="current_password" class="block text-sm font-bold text-white mb-2">رمز عبور فعلی</label>
                    <input type="password" id="current_password" name="current_password"
                           dir="ltr" autocomplete="current-password"
                           class="glass-input text-left @error('current_password') !border-rose-400/70 @enderror">
                    @error('current_password')
                    <p class="text-rose-300 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <label for="new_password" class="block text-sm font-bold text-white mb-2">رمز عبور جدید</label>
                    <input type="password" id="new_password" name="new_password"
                           dir="ltr" autocomplete="new-password"
                           class="glass-input text-left @error('new_password') !border-rose-400/70 @enderror">
                    @error('new_password')
                    <p class="text-rose-300 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

            </div>

            <!-- Buttons -->
            <div class="flex justify-end gap-3">
                <a  href="{{route('admin.users.index')}}" class="btn-cancel">
                    انصراف
                </a>
                <button type="submit" class="btn-update">
                    ذخیره‌ی تغییرات
                </button>
            </div>
        </form>
    </section>

@endsection
