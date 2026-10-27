<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Notifications\NewCommentNotification;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;

class AdminCommentController extends Controller implements HasMiddleware
{
    /** فقط ادمین (همان معیاری که در CommentPolicy استفاده شده: role === 'admin') */
    public static function middleware(): array
    {
        return [
            function (Request $request, Closure $next) {
                abort_unless($request->user()?->role === 'admin', 403);

                return $next($request);
            },
        ];
    }

    /** لیست همه‌ی کامنت‌ها با فیلتر وضعیت و جستجو */
    public function index(Request $request)
    {
        $status = $request->query('status');
        $search = trim((string) $request->query('search'));

        $comments = Comment::with(['user', 'post'])
            ->when(
                in_array($status, [Comment::STATUS_PENDING, Comment::STATUS_APPROVED], true),
                fn ($q) => $q->where('status', $status)
            )
            ->when($search !== '', function ($q) use ($search) {
                $q->where(function ($w) use ($search) {
                    $w->where('body', 'like', "%{$search}%")
                        ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$search}%"));
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
            ->with(['user', 'post'])
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

        $comment->update(['status' => Comment::STATUS_APPROVED]);

        // بعد از تایید، صاحب پست خبردار می‌شود (فقط برای کامنت اصلی، مثل قبل)
        if ($comment->parent_id === null) {
            $comment->post->user?->notify(new NewCommentNotification($comment));
        }

        return back()->with('success', 'کامنت تایید شد.');
    }

    public function unapprove(Comment $comment)
    {
        $comment->update(['status' => Comment::STATUS_PENDING]);

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

    private function counts(): array
    {
        return [
            'all'      => Comment::count(),
            'pending'  => Comment::pending()->count(),
            'approved' => Comment::approved()->count(),
            'trash'    => Comment::onlyTrashed()->count(),
        ];
    }
}
