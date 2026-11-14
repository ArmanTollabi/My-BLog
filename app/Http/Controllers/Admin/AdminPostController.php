<?php

namespace App\Http\Controllers\Admin;

use App\Enums\PostStatus;
use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;

class AdminPostController extends Controller
{
    /** لیست پست‌ها برای ادمین، با فیلتر وضعیت (?status=draft|published|scheduled|archived) و جستجو */
    public function index(Request $request)
    {
        $status = in_array($request->query('status'), PostStatus::values(), true) ? $request->query('status') : null;

        $posts = Post::query()
            ->listColumns()
            ->with(['user:id,name', 'category:id,title,slug'])
            ->withCount('comments')
            ->search($request->query('search'))
            ->withStatus($status)
            ->latest()                // پنل ادمین: بر اساس تاریخ ساخت (پیش‌نویس‌ها تاریخ انتشار ندارند)
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('admin.posts.index', [
            'posts'  => $posts,
            'counts' => $this->counts(),
            'status' => $status,
        ]);
    }

    /** شمارنده‌ی هر وضعیت با «یک» کوئری (منتشرشده/زمان‌بندی‌شده با در نظر گرفتن زمان فعلی) */
    private function counts(): array
    {
        $now = now()->toDateTimeString();

        $row = Post::query()->toBase()
            ->selectRaw('count(*) as total')
            ->selectRaw('count(case when status = ? or (status = ? and published_at <= ?) then 1 end) as published', [PostStatus::Published->value, PostStatus::Scheduled->value, $now])
            ->selectRaw('count(case when status = ? and published_at > ? then 1 end) as scheduled', [PostStatus::Scheduled->value, $now])
            ->selectRaw('count(case when status = ? then 1 end) as draft', [PostStatus::Draft->value])
            ->selectRaw('count(case when status = ? then 1 end) as archived', [PostStatus::Archived->value])
            ->first();

        return [
            'all'       => (int) $row->total,
            'published' => (int) $row->published,
            'scheduled' => (int) $row->scheduled,
            'draft'     => (int) $row->draft,
            'archived'  => (int) $row->archived,
        ];
    }
}
