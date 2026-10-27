<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use App\Notifications\NewCommentNotification;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    private const PENDING_MESSAGE = 'کامنت شما ثبت شد و پس از تایید ادمین برای همه نمایش داده می‌شود.';

    public function store(Request $request, Post $post)
    {
        $request->validate([
            'body' => 'required|min:3|max:100',
        ]);

        // کامنت ادمین مستقیم تایید می‌شود؛ بقیه منتظر تایید می‌مانند
        $approved = $request->user()->role === 'admin';

        $comment = Comment::create([
            'user_id' => $request->user()->id,
            'post_id' => $post->id,
            'body'    => $request->body,
            'status'  => $approved ? Comment::STATUS_APPROVED : Comment::STATUS_PENDING,
        ]);

        // نوتیفیکیشن فقط برای کامنت تاییدشده (برای بقیه، بعد از تایید ادمین ارسال می‌شود)
        if ($approved) {
            $post->user->notify(new NewCommentNotification($comment));
        }

        return back()->with('success', $approved ? 'کامنت با موفقیت ثبت شد.' : self::PENDING_MESSAGE);
    }

    public function update(Request $request, Comment $comment)
    {
        $this->authorize('update_comment', $comment);

        $request->validate([
            'body' => 'required|min:3|max:100',
        ]);

        $approved = $request->user()->role === 'admin';

        $comment->update([
            'body'   => $request->body,
            // ویرایش کامنت توسط کاربر عادی، دوباره نیاز به تایید دارد
            'status' => $approved ? Comment::STATUS_APPROVED : Comment::STATUS_PENDING,
        ]);

        return back()->with(
            'success',
            $approved ? 'کامنت ویرایش شد.' : 'کامنت ویرایش شد و پس از تایید ادمین دوباره نمایش داده می‌شود.'
        );
    }

    public function destroy(Comment $comment)
    {
        $this->authorize('delete_comment', $comment);

        // حذف نرم: کامنت به سطل زباله می‌رود و فقط ادمین می‌تواند آن را برای همیشه پاک کند
        $comment->delete();

        return back()->with('success', 'کامنت حذف شد.');
    }

    public function reply(Request $request, Comment $comment)
    {
        // فقط به کامنت اصلیِ تاییدشده می‌شود ریپلای داد
        abort_if(! $comment->isApproved() || $comment->parent_id !== null, 404);

        $request->validate([
            'reply_body' => 'required|min:3|max:100',
        ]);

        $approved = $request->user()->role === 'admin';

        Comment::create([
            'user_id'   => $request->user()->id,
            'post_id'   => $comment->post_id,
            'parent_id' => $comment->id,
            'body'      => $request->reply_body,
            'status'    => $approved ? Comment::STATUS_APPROVED : Comment::STATUS_PENDING,
        ]);

        return back()->with('success', $approved ? 'پاسخ با موفقیت ثبت شد.' : self::PENDING_MESSAGE);
    }
}
