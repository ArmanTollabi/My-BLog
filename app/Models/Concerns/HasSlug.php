<?php

namespace App\Models\Concerns;

trait HasSlug
{
    /** از چه ستونی slug ساخته شود (در مدل می‌توانید عوضش کنید) */
    protected function slugSource(): string
    {
        return 'title';
    }

    /** این متد خودکار هنگام بوت شدن مدل اجرا می‌شود (قانون: bootNameOfTrait) */
    protected static function bootHasSlug(): void
    {
        static::creating(function ($model) {
            if (blank($model->slug)) {
                $model->slug = $model->makeUniqueSlug($model->{$model->slugSource()});
            }
        });

        // عنوان که عوض شد، slug هم از عنوان جدید ساخته می‌شود (مگر خودِ کد slug را صریح عوض کرده باشد)
        static::updating(function ($model) {
            $source = $model->slugSource();

            if ($model->shouldRegenerateSlug() && $model->isDirty($source) && ! $model->isDirty('slug')) {
                $model->slug = $model->makeUniqueSlug($model->{$source}, $model->getKey());
            }
        });
    }

    /**
     * آیا با تغییر عنوان، slug هم عوض شود؟ (پیش‌فرض: بله)
     * مدلی که آدرسش باید همیشه ثابت بماند، این متد را override کند و false برگرداند.
     */
    protected function shouldRegenerateSlug(): bool
    {
        return true;
    }

    /** Laravel با این ستون مدل را از آدرس پیدا می‌کند: /posts/{slug} */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /** تبدیل عنوان به slug؛ حروف فارسی حفظ می‌شود */
    public static function slugify(string $text): string
    {
        $text = strtr($text, ['ي' => 'ی', 'ك' => 'ک', 'ة' => 'ه']);            // حروف عربی → فارسی
        $text = strtr($text, ['۰'=>'0','۱'=>'1','۲'=>'2','۳'=>'3','۴'=>'4','۵'=>'5','۶'=>'6','۷'=>'7','۸'=>'8','۹'=>'9',
            '٠'=>'0','١'=>'1','٢'=>'2','٣'=>'3','٤'=>'4','٥'=>'5','٦'=>'6','٧'=>'7','٨'=>'8','٩'=>'9']);
        $text = mb_strtolower($text);
        $text = preg_replace('/[^\p{L}\p{N}]+/u', '-', $text);                  // هر چیزی جز حرف/عدد → خط تیره (نیم‌فاصله هم)
        $text = trim(mb_substr($text, 0, 80), '-');

        return $text !== '' ? $text : 'item';
    }

    /** $ignoreId: در ویرایش، خودِ همین رکورد با slug فعلی‌اش «تکراری» حساب نشود */
    public function makeUniqueSlug(string $title, mixed $ignoreId = null): string
    {
        $base = $slug = static::slugify($title);
        $i = 2;

        // تا وقتی slug تکراری است، -2 و -3 و ... اضافه کن
        while (static::withoutGlobalScopes()
            ->where('slug', $slug)
            ->when($ignoreId !== null, fn ($q) => $q->whereKeyNot($ignoreId))
            ->exists()) {
            $slug = $base.'-'.$i++;
        }

        return $slug;
    }
}
