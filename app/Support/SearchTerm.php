<?php

namespace App\Support;

/** پاک‌سازی عبارت جستجو و ساخت الگوی امن LIKE (یک جا، برای همه‌ی جستجوها) */
final class SearchTerm
{
    public static function clean(?string $value, int $max = 60): string
    {
        $value = trim((string) preg_replace('/\s+/u', ' ', (string) $value));
        $value = strtr($value, ['ي' => 'ی', 'ك' => 'ک']); // یکسان‌سازی «ی» و «ک» عربی/فارسی

        return mb_substr($value, 0, $max);
    }

    /** کاراکترهای % و _ و \ را خنثی می‌کند تا کاربر نتواند الگوی دلخواه بسازد */
    public static function like(string $term): string
    {
        return '%' . addcslashes($term, '%_\\') . '%';
    }
}
