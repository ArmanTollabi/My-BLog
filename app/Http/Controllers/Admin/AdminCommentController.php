<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Enums\CommentStatus;
use App\Models\Comment;
use App\Notifications\NewCommentNotification;
use App\Support\SearchTerm;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Support\Facades\DB;

class AdminCommentController extends Controller implements HasMiddleware
{
    /** فقط ادمین (همان معیاری که در CommentPolicy استفاده شده: role === 'admin') */
    public static function middleware(): array
    {
        return [
            function (Request $request, Closure $next) {
                abort_unless($request->user()?->isAdmin(), 403);

                return $next($request);
            },
        ];
    }

    /** لیست همه‌ی کامنت‌ها با فیلتر وضعیت و جستجو */
    public function index(Request $request)
    {
        $status = $request->query('status');
        $term = SearchTerm::clean($request->query('search'));
        $like = SearchTerm::like($term);

        $comments = Comment::with(['user', 'post:id,title,slug'])
            ->when(
                in_array($status, CommentStatus::values(), true),
                fn ($q) => $q->where('status', $status)
            )
            ->when($term !== '', function ($q) use ($like) {
                $q->where(function ($w) use ($like) {
                    $w->where('body', 'like', $like)
                        ->orWhereHas('user', fn ($u) => $u->where('name', 'like', $like));
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.comments.index', [
            'comments' => $comments,
            'counts'   => $this->counts(),
            'status'   => $status,
        ]);
    }

    /** سطل زباله */
    public function trash()
    {
        $comments = Comment::onlyTrashed()
            ->with(['user', 'post:id,title,slug'])
            ->latest('deleted_at')
            ->paginate(15);

        return view('admin.comments.trash', [
            'comments' => $comments,
            'counts'   => $this->counts(),
        ]);
    }

    public function approve(Comment $comment)
    {
        if ($comment->isApproved()) {
            return back();
        }

        $comment->update(['status' => CommentStatus::Approved->value]);

        // بعد از تایید، صاحب پست خبردار می‌شود (فقط برای کامنت اصلی)
        if ($comment->parent_id === null) {
            $comment->post->user?->notify(new NewCommentNotification($comment));
        }

        return back()->with('success', 'کامنت تایید شد.');
    }

    public function unapprove(Comment $comment)
    {
        $comment->update(['status' => CommentStatus::Pending->value]);

        return back()->with('success', 'کامنت به حالت «در انتظار تایید» برگشت.');
    }

    /** انتقال به سطل زباله */
    public function destroy(Comment $comment)
    {
        $comment->delete();

        return back()->with('success', 'کامنت به سطل زباله منتقل شد.');
    }

    public function restore(Comment $comment)
    {
        $comment->restore();

        return back()->with('success', 'کامنت بازیابی شد.');
    }

    /** حذف همیشگی از دیتابیس (ریپلای‌های آن هم پاک می‌شوند) */
    public function forceDelete(Comment $comment)
    {
        $comment->replies()->withTrashed()->forceDelete();
        $comment->forceDelete();

        return back()->with('success', 'کامنت برای همیشه حذف شد.');
    }

    /** خالی کردن کل سطل زباله (حذف همیشگی همه‌ی کامنت‌های حذف‌شده + ریپلای‌هایشان) */
    public function emptyTrash()
    {
        $ids = Comment::onlyTrashed()->pluck('id');

        if ($ids->isEmpty()) {
            return back()->with('success', 'سطل زباله از قبل خالی بود.');
        }

        DB::transaction(function () use ($ids) {
            // ریپلای‌های کامنت‌های حذف‌شده (حتی اگر خودشان حذف نشده باشند) — دسته‌ای تا لیست IDها زیادی بزرگ نشود
            $ids->chunk(1000)->each(
                fn ($chunk) => Comment::withTrashed()->whereIn('parent_id', $chunk)->forceDelete()
            );

            Comment::onlyTrashed()->forceDelete();
        });

        return redirect()->route('admin.comments.trash')->with('success', 'سطل زباله خالی شد.');
    }

    /** چهار شمارنده با «یک» کوئری (قبلاً چهار کوئری جدا بود) */
    private function counts(): array
    {
        $row = Comment::withTrashed()
            ->selectRaw('count(case when deleted_at is null then 1 end) as total')
            ->selectRaw('count(case when deleted_at is null and status = ? then 1 end) as pending', [CommentStatus::Pending->value])
            ->selectRaw('count(case when deleted_at is null and status = ? then 1 end) as approved', [CommentStatus::Approved->value])
            ->selectRaw('count(case when deleted_at is not null then 1 end) as trashed')
            ->toBase()
            ->first();

        return [
            'all'      => (int) $row->total,
            'pending'  => (int) $row->pending,
            'approved' => (int) $row->approved,
            'trash'    => (int) $row->trashed,
        ];
    }
}
