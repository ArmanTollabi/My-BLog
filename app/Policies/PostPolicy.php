<?php

namespace App\Policies;

use App\Models\Post;
use App\Models\User;

class PostPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public function create(User $user){
        return $user->role === 'admin';
    }

    public function update(User $user, Post $post):bool{

        return $user->role === 'admin' || $post->user_id === $user->id;
        //return $user->id === $post->user_id;
    }

    public function delete(User $user, Post $post):bool{

        return $user->role === 'admin' || $user->id === $post->user_id;
        //return $user->id === $post->user_id;
    }
}
