<?php

namespace App\Http\Controllers;

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

    public function index(Request $request): View
    {
        $user = $request->user();

        // اگر رابطه‌ها در مدل User تعریف نشده باشند، خطا نمی‌دهد
        $hasPosts = method_exists($user, 'posts');
        $hasComments = method_exists($user, 'comments');

        $posts = $hasPosts ? $user->posts()->latest()->take(5)->get() : collect();

        $stats = [
            'posts'    => $hasPosts ? $user->posts()->count() : 0,
            'comments' => $hasComments ? $user->comments()->count() : 0,
        ];

        return view('dashboard', compact('user', 'posts', 'stats'));
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
    public function updateAvatar(Request $request): RedirectResponse|JsonResponse
    {
        // در حالت AJAX خطای اعتبارسنجی به‌صورت JSON با کد ۴۲۲ برمی‌گردد
        $request->validate([
            'avatar' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'], // حداکثر ۲ مگابایت
        ]);

        $user = $request->user();

        $path = $request->file('avatar')->store('avatars', self::DISK);

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
