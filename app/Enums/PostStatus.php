<?php

namespace App\Enums;

/** وضعیت انتشار پست. مقدار در دیتابیس همین رشته‌هاست (ستون posts.status). */
enum PostStatus: string
{
    case Draft = 'draft';
    case Published = 'published';
    case Scheduled = 'scheduled';
    case Archived = 'archived';

    public function label(): string
    {
        return match ($this) {
            self::Draft     => 'پیش‌نویس',
            self::Published => 'منتشرشده',
            self::Scheduled => 'زمان‌بندی‌شده',
            self::Archived  => 'بایگانی‌شده',
        };
    }

    /** توضیح کوتاه زیر کشوی وضعیت در فرم */
    public function hint(): string
    {
        return match ($this) {
            self::Draft     => 'ذخیره می‌شود ولی در سایت دیده نمی‌شود؛ فقط خودت و مدیر می‌بینید.',
            self::Published => 'همین حالا برای همه نمایش داده می‌شود.',
            self::Scheduled => 'در تاریخ و ساعتی که انتخاب می‌کنی خودکار منتشر می‌شود.',
            self::Archived  => 'از سایت برداشته می‌شود ولی حذف نمی‌شود؛ هر زمان خواستی دوباره منتشرش کن.',
        };
    }

    /** کلاس‌های Tailwind نشانک (badge) */
    public function badgeClasses(): string
    {
        return match ($this) {
            self::Draft     => 'border-amber-300/40 bg-amber-400/15 text-amber-200',
            self::Published => 'border-emerald-300/40 bg-emerald-400/15 text-emerald-200',
            self::Scheduled => 'border-sky-300/40 bg-sky-400/15 text-sky-200',
            self::Archived  => 'border-white/20 bg-white/10 text-white/65',
        };
    }

    /** @return list<string> */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
