<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    private const SUPER_ADMIN_ID = 1;

    /** ادمین اصلی: تنها کسی که نقش‌ها را تعیین/تغییر می‌دهد و ادمین‌ها را مدیریت می‌کند */
    private function isSuper(User $user): bool
    {
        return $user->id === self::SUPER_ADMIN_ID;
    }

    public function isAdmin(User $user): bool
    {
        return $user->role === 'admin';
    }

    /** دیدن/تغییر نقش‌ها و ساخت ادمین: فقط ادمین اصلی */
    public function edit_admin(User $user): bool
    {
        return $this->isSuper($user);
    }

    /**
     * ویرایش کاربر:
     *  - ادمین اصلی: همه
     *  - هر کاربر: حساب خودش
     *  - ادمین‌های دیگر: فقط کاربران غیرادمین
     */
    public function update_user(User $user, User $targetUser): bool
    {
        if ($this->isSuper($user) || $user->is($targetUser)) {
            return true;
        }

        return $user->role === 'admin' && $targetUser->role !== 'admin';
    }

    public function access_user(User $user): bool
    {
        return $user->role === 'admin';
    }

    /**
     * حذف کاربر:
     *  - هیچ‌کس نمی‌تواند خودش را «به‌عنوان ادمین» حذف کند؛ کاربر عادی فقط خودش را می‌تواند
     *  - ادمین اصلی: همه‌ی دیگران
     *  - ادمین‌های دیگر: فقط کاربران غیرادمین
     */
    public function delete_user(User $user, User $targetUser): bool
    {
        if ($user->is($targetUser)) {
            return ! $this->isAdmin($user);
        }

        if ($this->isSuper($user)) {
            return true;
        }

        return $user->role === 'admin' && $targetUser->role !== 'admin';
    }

    public function viewAny(User $user): bool { return false; }
    public function view(User $user, User $model): bool { return false; }
    public function create(User $user): bool { return false; }
    public function update(User $user, User $model): bool { return false; }
    public function delete(User $user, User $model): bool { return false; }
    public function restore(User $user, User $model): bool { return false; }
    public function forceDelete(User $user, User $model): bool { return false; }
}
