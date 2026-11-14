{{-- FILE: resources/views/admin/dashboard.blade.php --}}
@extends('admin.layout')

@section('title', 'آمار و گزارش')

@section('admin-content')

    <section>

        <div class="mb-6 sm:mb-8">
            <h1 class="text-2xl sm:text-3xl md:text-4xl font-bold gradient-text">آمار و گزارش</h1>
            <p class="text-white/55 mt-2">نمای کلی از کاربران، پست‌ها و کامنت‌ها</p>
        </div>

        {{-- کارت‌های خلاصه --}}
        <div class="grid grid-cols-2 xl:grid-cols-4 gap-3 sm:gap-4 mb-6 sm:mb-8">

            <div class="glass rounded-3xl p-4 sm:p-5 min-w-0">
                <p class="text-xs sm:text-sm text-white/55">کل کاربران</p>
                <p class="text-2xl sm:text-3xl font-bold text-white mt-1">{{ number_format($totals['users']) }}</p>
                <p class="text-xs text-emerald-300 mt-2">+{{ $totals['newUsersWeek'] }} در ۷ روز اخیر</p>
            </div>

            <a href="{{ route('admin.posts.index') }}" class="glass rounded-3xl p-4 sm:p-5 min-w-0 transition hover:bg-white/5">
                <p class="text-xs sm:text-sm text-white/55">کل پست‌ها</p>
                <p class="text-2xl sm:text-3xl font-bold text-white mt-1">{{ number_format($totals['posts']) }}</p>
                <p class="text-xs text-white/45 mt-2">مشاهده پست‌ها</p>
            </a>

            <a href="{{ route('admin.comments.index') }}" class="glass rounded-3xl p-4 sm:p-5 min-w-0 transition hover:bg-white/5">
                <p class="text-xs sm:text-sm text-white/55">کل کامنت‌ها</p>
                <p class="text-2xl sm:text-3xl font-bold text-white mt-1">{{ number_format($totals['comments']) }}</p>
                <p class="text-xs text-white/45 mt-2">مدیریت کامنت‌ها</p>
            </a>

            <a href="{{ route('admin.comments.index', ['status' => 'pending']) }}"
               class="glass rounded-3xl p-4 sm:p-5 min-w-0 transition hover:bg-white/5">
                <p class="text-xs sm:text-sm text-white/55">در انتظار تایید</p>
                <p class="text-2xl sm:text-3xl font-bold {{ $totals['pending'] ? 'text-amber-300' : 'text-white' }} mt-1">
                    {{ number_format($totals['pending']) }}
                </p>
                <p class="text-xs text-white/45 mt-2">بررسی کامنت‌ها</p>
            </a>

        </div>

        {{-- چارت اصلی: رشد کاربران --}}
        <div class="glass rounded-3xl p-4 sm:p-6 mb-6">
            <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
                <h2 class="text-base sm:text-lg font-bold text-white">رشد کاربران</h2>

                <div class="flex flex-wrap items-center gap-2" id="range-buttons">
                    <button type="button" data-range="7d"  class="btn-glass btn-sm">۷ روز</button>
                    <button type="button" data-range="30d" class="btn-glass btn-sm">۳۰ روز</button>
                    <button type="button" data-range="12m" class="btn-glass btn-sm">۱۲ ماه</button>
                </div>
            </div>
            <div dir="ltr" class="relative h-64 sm:h-80"><canvas id="usersChart"></canvas></div>
        </div>

        <div class="grid xl:grid-cols-3 gap-4 sm:gap-6 mb-6 sm:mb-8">

            <div class="glass rounded-3xl p-4 sm:p-6 xl:col-span-2 min-w-0">
                <h2 class="text-lg font-bold text-white mb-4">فعالیت: پست و کامنت جدید</h2>
                <div dir="ltr" class="relative h-60 sm:h-72"><canvas id="activityChart"></canvas></div>
            </div>

            <div class="glass rounded-3xl p-4 sm:p-6 min-w-0">
                <h2 class="text-lg font-bold text-white mb-4">پست‌ها به تفکیک کتگوری</h2>
                @if(count($categories))
                    <div dir="ltr" class="relative h-72"><canvas id="categoryChart"></canvas></div>
                @else
                    <p class="text-white/55 text-sm">هنوز پستی ثبت نشده.</p>
                @endif
            </div>

        </div>

        {{-- لیست‌های کوتاه --}}
        <div class="grid xl:grid-cols-2 gap-4 sm:gap-6">

            <div class="glass rounded-3xl p-4 sm:p-6 min-w-0">
                <div class="flex items-center justify-between gap-3 mb-4">
                    <h2 class="text-base sm:text-lg font-bold text-white">کاربران جدید</h2>
                    <a href="{{ route('admin.users.index') }}" class="btn-glass btn-sm">همه</a>
                </div>

                @forelse($latestUsers as $user)
                    <div class="flex items-center gap-3 py-2">
                        <span class="user-avatar">
                            @if($user->avatar)
                                <img src="{{ Storage::url($user->avatar) }}" alt="">
                            @else
                                {{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}
                            @endif
                        </span>
                        <div class="min-w-0 flex-1">
                            <p class="font-semibold text-white truncate">{{ $user->name }}</p>
                            <p dir="ltr" class="text-xs text-white/50 truncate text-left">{{ $user->email }}</p>
                        </div>
                        <span class="hidden text-xs text-white/45 min-[420px]:inline">{{ $user->created_at_fa }}</span>
                    </div>
                @empty
                    <p class="text-white/55 text-sm">کاربری وجود ندارد.</p>
                @endforelse
            </div>

            <div class="glass rounded-3xl p-4 sm:p-6 min-w-0">
                <div class="flex items-center justify-between gap-3 mb-4">
                    <h2 class="text-base sm:text-lg font-bold text-white">کامنت‌های در انتظار تایید</h2>
                    <a href="{{ route('admin.comments.index', ['status' => 'pending']) }}" class="btn-glass btn-sm">همه</a>
                </div>

                @forelse($pendingComments as $comment)
                    <div class="py-3 {{ ! $loop->last ? 'border-b border-white/10' : '' }}">
                        <p class="text-xs sm:text-sm text-white/55">
                            {{ $comment->user?->name ?? 'کاربر حذف‌شده' }}
                            @if($comment->post)
                                · <a href="{{ route('posts.show', $comment->post) }}#comment-{{ $comment->id }}"
                                     class="text-violet-300 hover:underline">{{ \Illuminate\Support\Str::limit($comment->post->title, 28) }}</a>
                            @endif
                        </p>
                        <p class="mt-1 text-white/85 break-words">{{ \Illuminate\Support\Str::limit($comment->body, 80) }}</p>

                        <div class="flex flex-wrap gap-2 mt-2">
                            <form action="{{ route('admin.comments.approve', $comment) }}" method="POST">
                                @csrf @method('PATCH')
                                <button type="submit" class="btn-create btn-sm">تایید</button>
                            </form>
                            <form action="{{ route('admin.comments.destroy', $comment) }}" method="POST"
                                  onsubmit="return confirm('این کامنت به سطل زباله منتقل شود؟')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-delete btn-sm">حذف</button>
                            </form>
                        </div>
                    </div>
                @empty
                    <p class="text-white/55 text-sm">کامنتی در انتظار تایید نیست 🎉</p>
                @endforelse
            </div>

        </div>

    </section>

@endsection

@push('scripts')
    {{-- داده‌ها به‌صورت JSON امن به اسکریپت داده می‌شوند؛ خود اسکریپت: resources/js/admin-dashboard.js --}}
    <script type="application/json" id="admin-dashboard-data">{!! json_encode(['chart' => $chart, 'categories' => $categories], JSON_HEX_TAG | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE) !!}</script>
    @vite('resources/js/admin-dashboard.js')
@endpush
