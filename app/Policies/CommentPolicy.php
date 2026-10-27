<?php

namespace App\Policies;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;

class CommentPolicy
{
    /**
     * Create a new policy instance.
     */

    public function update_comment(User $user,Comment $comment)
    {
        return $user->id === $comment->user_id || $user->role === 'admin';
    }

    public function delete_comment(User $user,Comment $comment)
    {
        return $user->id === $comment->user_id || $user->role === 'admin';
    }
    public function __construct()
    {
        //
    }
}
