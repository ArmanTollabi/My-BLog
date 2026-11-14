<?php

namespace App\Enums;

/** نقش کاربر. مقدار ذخیره‌شده در دیتابیس همان رشته‌ی قبلی است ('admin' / 'subscriber'). */
enum UserRole: string
{
    case Admin = 'admin';
    case Subscriber = 'subscriber';

    public function label(): string
    {
        return match ($this) {
            self::Admin      => 'مدیر',
            self::Subscriber => 'کاربر عادی',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
