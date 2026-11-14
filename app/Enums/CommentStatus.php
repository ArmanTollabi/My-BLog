<?php

namespace App\Enums;

use App\Models\User;

/** وضعیت کامنت. مقدار ذخیره‌شده در دیتابیس همان رشته‌ی قبلی است ('pending' / 'approved'). */
enum CommentStatus: string
{
    case Pending = 'pending';
    case Approved = 'approved';

    public function label(): string
    {
        return match ($this) {
            self::Pending  => 'در انتظار تایید',
            self::Approved => 'تایید شده',
        };
    }

    /** ['pending', 'approved'] — برای اعتبارسنجی و in_array */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /** قانون مدیریت کامنت در «یک» جا: کامنت ادمین مستقیم تایید می‌شود، بقیه منتظر تایید می‌مانند */
    public static function forAuthor(User $user): self
    {
        return $user->isAdmin() ? self::Approved : self::Pending;
    }
}
