<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Support\Collection;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // یک‌بار تاریخ ثبت رکوردهای ۱۲ ماه اخیر را می‌گیریم و بازه‌های ۷ روز / ۳۰ روز / ۱۲ ماه
        // را در حافظه می‌سازیم (مستقل از نوع دیتابیس و بدون کش).
        $from = now()->startOfMonth()->subMonths(11);

        $users    = User::where('created_at', '>=', $from)->get(['created_at'])->pluck('created_at');
        $posts    = Post::where('created_at', '>=', $from)->get(['created_at'])->pluck('created_at');
        $comments = Comment::where('created_at', '>=', $from)->get(['created_at'])->pluck('created_at');

        $totalUsers = User::count();

        $chart = [
            '7d'  => $this->series('7d', $users, $posts, $comments, $totalUsers),
            '30d' => $this->series('30d', $users, $posts, $comments, $totalUsers),
            '12m' => $this->series('12m', $users, $posts, $comments, $totalUsers),
        ];

        $totals = [
            'users'        => $totalUsers,
            'posts'        => Post::count(),
            'comments'     => Comment::count(),
            'pending'      => Comment::pending()->count(),
            'newUsersWeek' => $users->filter(fn ($d) => $d->gte(now()->subDays(7)))->count(),
        ];

        return view('admin.dashboard', [
            'totals'          => $totals,
            'chart'           => $chart,
            'categories'      => $this->categoryDistribution(),
            'latestUsers'     => User::latest()->take(5)->get(),
            'pendingComments' => Comment::pending()->with(['user', 'post'])->latest()->take(5)->get(),
        ]);
    }

    /** برچسب‌ها و داده‌های یک بازه برای چارت‌ها */
    private function series(string $range, Collection $users, Collection $posts, Collection $comments, int $totalUsers): array
    {
        [$start, $key, $label, $step] = match ($range) {
            '7d'  => [now()->startOfDay()->subDays(6), 'Y-m-d', 'm/d', 'day'],
            '30d' => [now()->startOfDay()->subDays(29), 'Y-m-d', 'm/d', 'day'],
            '12m' => [now()->startOfMonth()->subMonths(11), 'Y-m', 'Y/m', 'month'],
        };

        $inRange = fn (Collection $dates) => $dates->filter(fn ($d) => $d->gte($start));
        $bucket  = fn (Collection $dates) => $inRange($dates)->countBy(fn ($d) => $d->format($key));

        $newUsers = $bucket($users);
        $newPosts = $bucket($posts);
        $newComments = $bucket($comments);

        // تعداد کاربران قبل از شروع بازه (برای نمودار تجمعی)
        $running = $totalUsers - $inRange($users)->count();

        $out = ['labels' => [], 'users' => [], 'newUsers' => [], 'posts' => [], 'comments' => []];
        $cursor = $start->copy();

        while ($cursor->lte(now())) {
            $k = $cursor->format($key);
            $running += $newUsers[$k] ?? 0;

            $out['labels'][]   = $cursor->format($label);
            $out['users'][]    = $running;
            $out['newUsers'][] = $newUsers[$k] ?? 0;
            $out['posts'][]    = $newPosts[$k] ?? 0;
            $out['comments'][] = $newComments[$k] ?? 0;

            $step === 'day' ? $cursor->addDay() : $cursor->addMonth();
        }

        return $out;
    }

    /** تعداد پست در هر کتگوری برای چارت دونات */
    private function categoryDistribution(): array
    {
        $counts = Post::selectRaw('category_id, count(*) as total')
            ->groupBy('category_id')
            ->pluck('total', 'category_id');

        $titles = Category::whereIn('id', $counts->keys()->filter())->pluck('title', 'id');

        return $counts->map(fn ($total, $id) => [
            'label' => $titles[$id] ?? 'بدون کتگوری',
            'value' => $total,
        ])->values()->all();
    }
}
