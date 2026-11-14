<?php

namespace App\Http\Requests;

use App\Enums\PostStatus;
use App\Support\JalaliDate;
use Closure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/** اعتبارسنجی مشترک فرم ساخت و ویرایش پست (دسترسی‌ها همچنان با Policy در کنترلر چک می‌شوند) */
class SavePostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title'      => ['required', 'string', 'max:255'],
            'category'   => ['required', 'exists:categories,id'],
            'content'    => ['required', 'string', 'max:300000'],
            // فقط مسیرهایی که خودِ سایت آپلود کرده (جلوگیری از اشاره به فایل دیگران)
            'image_path' => ['nullable', 'string', 'max:255', 'regex:#^posts/uploads/[\w/\-]+\.webp$#'],
            'tags'       => ['nullable', 'array'],
            'tags.*'     => ['exists:tags,id'],

            'status'       => ['required', Rule::enum(PostStatus::class)],
            // فقط وقتی «زمان‌بندی‌شده» انتخاب شده لازم است؛ تاریخ جلالی + ساعت، مثل ۱۴۰۵/۰۸/۲۴ ۱۰:۳۰
            'scheduled_at' => [
                'required_if:status,' . PostStatus::Scheduled->value,
                'nullable', 'string', 'max:32',
                $this->scheduledRule(),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'status.required'             => 'وضعیت پست را انتخاب کن.',
            'status.enum'                 => 'وضعیت انتخاب‌شده معتبر نیست.',
            'scheduled_at.required_if'    => 'برای انتشار زمان‌بندی‌شده، تاریخ و ساعت انتشار را انتخاب کن.',
        ];
    }

    private function scheduledRule(): Closure
    {
        return function (string $attribute, mixed $value, Closure $fail) {
            if ($this->input('status') !== PostStatus::Scheduled->value || blank($value)) {
                return;
            }

            $at = JalaliDate::parse($value);

            if ($at === null) {
                $fail('تاریخ و ساعت انتشار معتبر نیست (نمونه: ۱۴۰۵/۰۸/۲۴ ۱۰:۳۰).');
            } elseif ($at->lte(now()->addMinute())) {
                $fail('زمان انتشار باید در آینده باشد.');
            }
        };
    }
}
