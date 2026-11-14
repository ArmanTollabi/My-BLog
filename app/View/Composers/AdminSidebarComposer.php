<?php

namespace App\View\Composers;

use App\Models\Comment;
use Illuminate\View\View;

/** تعداد کامنت‌های در انتظار برای نشان کنار منوی ادمین (قبلاً داخل _sidebar.blade.php کوئری می‌خورد) */
class AdminSidebarComposer
{
    public function compose(View $view): void
    {
        $view->with('pendingCommentsCount', Comment::pending()->count());
    }
}
