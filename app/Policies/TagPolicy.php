<?php

namespace App\Policies;

use App\Models\User;

class TagPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public function create(? User $user) :bool{
        return $user && $user->role === "admin";
    }

    public function edit(? User $user) :bool{
        return $user && $user->role === "admin";
    }

    public function delete(? User $user) :bool{
        return $user && $user->role === "admin";
    }
}
