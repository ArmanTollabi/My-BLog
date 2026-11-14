<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Morilog\Jalali\CalendarUtils;
use Morilog\Jalali\Jalalian;

trait HasJalaliDates
{
    // آبجکت Jalalian، برای فرمت دلخواه
    protected function createdAtJalali(): Attribute
    {
        return Attribute::get(fn () => Jalalian::fromCarbon($this->created_at));
    }

    // فقط تاریخ، با اعداد فارسی
    protected function createdAtFa(): Attribute
    {
        return Attribute::get(fn () => CalendarUtils::convertNumbers(
            Jalalian::fromCarbon($this->created_at)->format('Y/m/d')
        ));
    }

    // تاریخ + ساعت و دقیقه، با اعداد فارسی
    protected function createdAtWithHoursFa(): Attribute
    {
        return Attribute::get(fn () => CalendarUtils::convertNumbers(
            Jalalian::fromCarbon($this->created_at)->format('Y/m/d H:i')
        ));
    }

    protected function deletedAtWithHoursFa(): Attribute
    {
        return Attribute::get(fn () => CalendarUtils::convertNumbers(
            Jalalian::fromCarbon($this->deleted_at)->format('Y/m/d H:i')
        ));
    }

    protected function createdAtAgo(): Attribute
    {
        return Attribute::get(fn () => CalendarUtils::convertNumbers(
            Jalalian::fromCarbon($this->created_at)->ago()
        ));
    }
}
