<?php

namespace App\Support;

/** کمک‌های نمایش فارسی. */
class Fa
{
    /**
     * ارقام لاتین را به فارسی تبدیل می‌کند.
     * نکته: strtr با دو رشته بایت‌به‌بایت کار می‌کند و ارقام فارسی چندبایتی‌اند (نتیجه: «؟»)؛
     * با آرایه امن است.
     */
    public static function digits(string|int|float|null $value): string
    {
        return strtr((string) $value, [
            '0' => '۰', '1' => '۱', '2' => '۲', '3' => '۳', '4' => '۴',
            '5' => '۵', '6' => '۶', '7' => '۷', '8' => '۸', '9' => '۹',
        ]);
    }

    /** عدد با جداکننده‌ی هزارگان و ارقام فارسی: 12500 → ۱۲٬۵۰۰ */
    public static function number(int|float|null $value): string
    {
        return str_replace(',', '٬', self::digits(number_format((float) $value)));
    }
}
