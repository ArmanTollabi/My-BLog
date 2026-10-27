<?php

namespace App\Policies;

use App\Models\User;

class CategoryPolicy
{

    public function view_cat(User $user):bool{
        return $user->role === 'admin';
    }

    public function delete_cat(User $user):bool{
        return $user->role === 'admin';
    }

    public function create_cat(User $user):bool{
        return $user->role === 'admin';
    }
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }
}
