<?php

namespace App\View\Composers;

use App\Models\Category;
use Illuminate\View\View;

/** داده‌های هدر (دسته‌ها، اعلان‌ها). قبلاً این کوئری‌ها داخل header.blade.php بودند. */
class HeaderComposer
{
    public function compose(View $view): void
    {
        // در داشبورد و پنل ادمین منوی دسته‌ها نمایش داده نمی‌شود، پس کوئری هم زده نمی‌شود
        $inPanel = request()->routeIs('dashboard', 'dashboard.*', 'admin.*');

        $routeCategory = request()->route('category');

        $view->with([
            'navCategories'       => $inPanel
                ? collect()
                : Category::has('posts')->orderBy('title')->get(['id', 'title', 'slug']),
            'navActiveCategoryId' => $routeCategory instanceof Category ? $routeCategory->id : $routeCategory,
        ]);

        if ($user = auth()->user()) {
            $view->with([
                'notifications' => $user->notifications()->latest()->limit(10)->get(),
                'unreadCount'   => $user->unreadNotifications()->count(),
            ]);
        }
    }
}
