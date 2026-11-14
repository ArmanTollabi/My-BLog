<?php

namespace App\Http\Controllers;

use App\Enums\CommentStatus;
use App\Services\ImageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class DashboardController extends Controller
{
    // دیسکی که عکس‌های دیگر سایت (مثل عکس پست‌ها) روی آن ذخیره می‌شوند
    private const DISK = 'public';

    // ───────────── صفحه‌های داشبورد (هرکدام فقط کوئری‌های خودش را می‌زند) ─────────────

    // اطلاعات حساب: نام/ایمیل، آواتار، رمز عبور
    public function index(Request $request): View
    {
        $user = $request->user();

        // یک کوئری برای هر سه شمارنده
        $user->loadCount([
            'posts',
            'comments as approved_comments_count' => fn ($q) => $q->where('status', CommentStatus::Approved->value),
            'savedPosts' => fn ($q) => $q->published(),   // بوکمارکِ پستی که بعداً پیش‌نویس/بایگانی شده نشمرده شود
        ]);

        return view('dashboard.index', compact('user'));
    }

    // کامنت‌های کاربر
    public function comments(Request $request): View
    {
        $user = $request->user();

        // شمارش بر اساس وضعیت: ['approved' => 3, 'pending' => 1]
        $commentCounts = $user->comments()
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $comments = $user->comments()
            ->with('post:id,title,slug')
            ->latest()
            ->paginate(6)
            ->withQueryString();

        return view('dashboard.comments', compact('user', 'comments', 'commentCounts'));
    }

    // پست‌های کاربر
    public function posts(Request $request): View
    {
        $user = $request->user();

        // پست‌های خودِ کاربر با «همه‌ی وضعیت‌ها» (پیش‌نویس، زمان‌بندی‌شده، بایگانی هم) — کارت نشانک وضعیت دارد
        $posts = $user->posts()
            ->listColumns()
            ->with('category:id,title,slug')
            ->withCount('likes')
            ->latest()
            ->paginate(6)
            ->withQueryString();

        return view('dashboard.posts', compact('user', 'posts'));
    }

    // پست‌های بوکمارک‌شده
    public function bookmarks(Request $request): View
    {
        $user = $request->user();

        // فقط پست‌هایی که الان منتشر شده‌اند
        $posts = $user->savedPosts()
            ->published()
            ->listColumns()
            ->with('category:id,title,slug')
            ->withCount('likes')
            ->latest('posts.created_at')
            ->paginate(6)
            ->withQueryString();

        return view('dashboard.bookmarks', compact('user', 'posts'));
    }

    // تغییر نام و ایمیل
    public function updateProfile(Request $request): RedirectResponse
    {
        $user = $request->user();

        // ایمیل عمداً اینجا نیست: حتی اگر کسی با ابزارهای مرورگر فیلد email بفرستد، نادیده گرفته می‌شود
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $user->name = $data['name'];
        $user->save();

        return back()->with('success', 'اطلاعات حساب با موفقیت به‌روز شد.');
    }

    // انتخاب / تغییر عکس پروفایل (با AJAX → JSON، بدون JS → ریدایرکت معمولی)
    public function updateAvatar(Request $request, ImageService $images): RedirectResponse|JsonResponse
    {
        // در حالت AJAX خطای اعتبارسنجی به‌صورت JSON با کد ۴۲۲ برمی‌گردد
        $request->validate([
            'avatar' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'], // حداکثر ۲ مگابایت
        ]);

        $user = $request->user();

        $path = $images->storeAvatar($request->file('avatar'), 'avatars');   // مربع ۲۵۶px، webp

        // عکس قبلی پاک شود تا فایل بی‌استفاده روی سرور نماند
        if ($user->avatar) {
            Storage::disk(self::DISK)->delete($user->avatar);
        }

        $user->avatar = $path;
        $user->save();

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'عکس پروفایل به‌روز شد.',
                'url'     => Storage::disk(self::DISK)->url($path),
            ]);
        }

        return back()->with('success', 'عکس پروفایل به‌روز شد.');
    }

    // حذف عکس پروفایل
    public function destroyAvatar(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->avatar) {
            Storage::disk(self::DISK)->delete($user->avatar);
            $user->avatar = null;
            $user->save();
        }

        return back()->with('success', 'عکس پروفایل حذف شد.');
    }

    // تغییر رمز عبور
    public function updatePassword(Request $request): RedirectResponse
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password'         => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = $request->user();
        $user->password = Hash::make($request->input('password'));
        $user->save();

        return back()->with('success', 'رمز عبور با موفقیت تغییر کرد.');
    }
}
