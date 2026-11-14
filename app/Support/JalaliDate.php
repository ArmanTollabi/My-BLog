<?php

namespace App\Support;

use Carbon\Carbon;
use Morilog\Jalali\CalendarUtils;
use Morilog\Jalali\Jalalian;
use Throwable;

/** تبدیل «تاریخ و ساعت جلالی» فرم (مثل ۱۴۰۵/۰۸/۲۴ ۱۰:۳۰) به Carbon؛ null اگر نامعتبر باشد */
class JalaliDate
{
    public static function parse(?string $value): ?Carbon
    {
        // ارقام فارسی/عربی → انگلیسی
        $v = trim(CalendarUtils::convertNumbers((string) $value, true));

        if (! preg_match('#^(\d{4})/(\d{1,2})/(\d{1,2})\s+(\d{1,2}):(\d{2})$#', $v, $m)) {
            return null;
        }

        [, $y, $mo, $d, $h, $mi] = array_map('intval', $m);
        $normalized = sprintf('%04d/%02d/%02d %02d:%02d', $y, $mo, $d, $h, $mi);

        try {
            $carbon = Jalalian::fromFormat('Y/m/d H:i', $normalized)->toCarbon();

            // رفت‌وبرگشت: اگر تاریخ نامعتبر بوده (مثلاً ۳۱ شهریور)، مقدار برگشتی با ورودی فرق می‌کند
            return Jalalian::fromCarbon($carbon)->format('Y/m/d H:i') === $normalized ? $carbon : null;
        } catch (Throwable) {
            return null;
        }
    }
}
