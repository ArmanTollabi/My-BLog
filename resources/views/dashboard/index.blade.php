@extends('dashboard.layout')

@section('title', 'داشبورد')

@section('dash-title', 'اطلاعات حساب')
@section('dash-subtitle', 'سلام '.$user->name.'! نام، عکس پروفایل و رمز عبورتان را اینجا مدیریت کنید.')

@section('dash-content')

    <div class="grid gap-6 xl:grid-cols-3">

        {{-- ===== ستون پروفایل ===== --}}
        <div class="glass rounded-3xl p-6 sm:p-8 text-center self-start">

            {{-- عکس پروفایل: کلیک روی آن = انتخاب عکس --}}
            <form id="avatar-form" action="{{ route('dashboard.avatar') }}" method="POST"
                  enctype="multipart/form-data">
                @csrf

                <label for="avatar-input"
                       class="group relative mx-auto block w-fit cursor-pointer rounded-full"
                       title="تغییر عکس پروفایل">

                        <span id="avatar-box" class="user-avatar is-xl">
                            @if($user->avatar)
                                <img src="{{ Storage::url($user->avatar) }}" alt="عکس پروفایل">
                            @else
                                {{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}
                            @endif
                        </span>

                    {{-- لایه‌ی دوربین هنگام hover --}}
                    <span class="absolute inset-0 grid place-items-center rounded-full bg-black/50 text-white
                                     opacity-0 transition group-hover:opacity-100 group-focus-within:opacity-100">
                            <svg class="w-7 h-7" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                 stroke-width="1.8" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M6.827 6.175A2.31 2.31 0 0 1 5.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 0 0-1.134-.175 2.31 2.31 0 0 1-1.64-1.055l-.822-1.316a2.192 2.192 0 0 0-1.736-1.039 48.774 48.774 0 0 0-5.232 0 2.192 2.192 0 0 0-1.736 1.039l-.821 1.316Z"/>
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M16.5 12.75a4.5 4.5 0 1 1-9 0 4.5 4.5 0 0 1 9 0Z"/>
                            </svg>
                        </span>
                </label>

                <input id="avatar-input" type="file" name="avatar"
                       accept="image/png,image/jpeg,image/webp" class="sr-only">

                {{-- دکمه‌های ذخیره / انصراف (بعد از انتخاب عکس نمایش داده می‌شوند) --}}
                <div id="avatar-actions" class="hidden mt-4 flex items-center justify-center gap-2">
                    <button type="submit" class="btn-update btn-sm">ذخیره عکس</button>
                    <button type="button" id="avatar-reset" class="btn-cancel btn-sm">انصراف</button>
                </div>

                {{-- نوار پیشرفت آپلود --}}
                <div id="avatar-progress" class="hidden mt-4">
                    <div class="h-2 w-full overflow-hidden rounded-full bg-white/15">
                        <div id="avatar-progress-bar"
                             class="h-2 rounded-full bg-gradient-to-r from-violet-500 to-cyan-400 transition-all"
                             style="width: 0%"></div>
                    </div>
                    <p id="avatar-progress-text" class="mt-2 text-xs text-white/60">0%</p>
                </div>

                <p id="avatar-client-error" class="mt-3 text-xs text-rose-300 empty:hidden"></p>
                @error('avatar')
                <p class="mt-3 text-xs text-rose-300">{{ $message }}</p>
                @enderror

                <p class="mt-3 text-xs text-white/45">JPG، PNG یا WebP — حداکثر ۲ مگابایت</p>
            </form>

            {{-- پیام موفقیت آپلود (با JS پر می‌شود) --}}
            <div id="avatar-status" class="mt-3 empty:hidden"></div>

            <form id="avatar-delete-form" action="{{ route('dashboard.avatar.destroy') }}" method="POST"
                  class="mt-3 {{ $user->avatar ? '' : 'hidden' }}"
                  onsubmit="return confirm('مطمئنی می‌خوای عکس پروفایلت رو حذف کنی؟')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-delete btn-sm">حذف عکس</button>
            </form>

            {{-- مشخصات --}}
            <h2 class="mt-6 text-xl font-extrabold text-white">{{ $user->name }}</h2>
            <p dir="ltr" class="mt-1 text-sm text-white/60 break-all">{{ $user->email }}</p>

            <p class="mt-3 inline-block rounded-full border border-white/15 bg-white/10 px-3 py-1 text-xs text-white/65">
                عضو از {{ $user->created_at_fa }}
            </p>

            {{-- آمار --}}
            <div class="mt-6 grid grid-cols-3 gap-3 border-t border-white/10 pt-6">
                <div class="rounded-2xl border border-white/10 bg-white/5 px-2 py-3">
                    <b class="block text-2xl font-extrabold text-white">{{ $user->posts_count }}</b>
                    <span class="text-xs text-white/55">پست</span>
                </div>
                <div class="rounded-2xl border border-white/10 bg-white/5 px-2 py-3">
                    <b class="block text-2xl font-extrabold text-white">{{ $user->approved_comments_count }}</b>
                    <span class="text-xs text-white/55">کامنت</span>
                </div>
                <div class="rounded-2xl border border-white/10 bg-white/5 px-2 py-3">
                    <b class="block text-2xl font-extrabold text-white">{{ $user->saved_posts_count }}</b>
                    <span class="text-xs text-white/55">بوکمارک</span>
                </div>
            </div>
        </div>


        {{-- ===== ستون فرم‌ها ===== --}}
        <div class="space-y-6 xl:col-span-2">

            {{-- اطلاعات حساب --}}
            <form action="{{ route('dashboard.profile') }}" method="POST"
                  class="glass rounded-3xl p-6 sm:p-8 space-y-5">
                @csrf
                @method('PUT')

                <div>
                    <h2 class="text-xl font-bold text-white">اطلاعات حساب</h2>
                    <p class="mt-1 text-sm text-white/55">نام و ایمیل خود را ویرایش کنید.</p>
                </div>

                <div>
                    <label for="name" class="block text-sm font-bold mb-2 text-white">نام</label>
                    <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}"
                           required autocomplete="name"
                           class="glass-input @error('name') !border-rose-400/70 @enderror">
                    @error('name')
                    <p class="mt-2 text-xs text-rose-300">{{ $message }}</p>
                    @enderror
                </div>

                {{-- ایمیل فقط نمایشی است: name ندارد، پس اصلاً ارسال نمی‌شود --}}
                <div>
                    <label for="email" class="block text-sm font-bold mb-2 text-white">ایمیل</label>
                    <input type="email" id="email" value="{{ $user->email }}"
                           readonly aria-readonly="true" tabindex="-1" dir="ltr"
                           class="glass-input text-left cursor-not-allowed opacity-70 select-all">
                    <p class="mt-2 text-xs text-white/45">ایمیل حساب قابل تغییر نیست.</p>
                </div>

                <button type="submit" class="btn-update">ذخیره تغییرات</button>
            </form>

            {{-- تغییر رمز عبور --}}
            <form action="{{ route('dashboard.password') }}" method="POST"
                  class="glass rounded-3xl p-6 sm:p-8 space-y-5">
                @csrf
                @method('PUT')

                <div>
                    <h2 class="text-xl font-bold text-white">تغییر رمز عبور</h2>
                    <p class="mt-1 text-sm text-white/55">برای امنیت بیشتر، رمز قوی و منحصربه‌فرد انتخاب کنید.</p>
                </div>

                <div>
                    <label for="current_password" class="block text-sm font-bold mb-2 text-white">رمز فعلی</label>
                    <input type="password" id="current_password" name="current_password"
                           dir="ltr" required autocomplete="current-password"
                           class="glass-input text-left @error('current_password') !border-rose-400/70 @enderror">
                    @error('current_password')
                    <p class="mt-2 text-xs text-rose-300">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid gap-5 sm:grid-cols-2">
                    <div>
                        <label for="new_password" class="block text-sm font-bold mb-2 text-white">رمز جدید</label>
                        <input type="password" id="new_password" name="password"
                               dir="ltr" required autocomplete="new-password"
                               class="glass-input text-left @error('password') !border-rose-400/70 @enderror">
                        @error('password')
                        <p class="mt-2 text-xs text-rose-300">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="new_password_confirmation" class="block text-sm font-bold mb-2 text-white">
                            تکرار رمز جدید
                        </label>
                        <input type="password" id="new_password_confirmation" name="password_confirmation"
                               dir="ltr" required autocomplete="new-password"
                               class="glass-input text-left">
                    </div>
                </div>

                <button type="submit" class="btn-update">تغییر رمز عبور</button>
            </form>
        </div>
    </div>


@endsection
