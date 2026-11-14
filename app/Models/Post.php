<?php

namespace App\Models;

use App\Enums\PostStatus;
use App\Models\Concerns\HasSlug;
use App\Services\ImageService;
use App\Support\Fa;
use App\Support\HtmlSanitizer;
use App\Support\SearchTerm;
use App\Traits\HasJalaliDates;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Morilog\Jalali\Jalalian;

class Post extends Model
{
    use HasJalaliDates;
    use HasFactory;
    use HasSlug;

    /** ستون‌های لازم برای لیست‌ها. ستون `content` (تا ۳۰۰ هزار کاراکتر) عمداً نیست. */
    public const LIST_COLUMNS = [
        'id', 'title', 'slug', 'description', 'image',
        'category_id', 'user_id', 'status', 'published_at', 'created_at', 'updated_at',
    ];

    protected $fillable = [
        'title',
        'description',
        'content',
        'category_id',
        'image',
        'user_id',
        'status',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'status'       => PostStatus::class,
            'published_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        // هر پستِ «منتشرشده» حتماً تاریخ انتشار دارد (مسیرهای قدیمی مثل API هم که status نمی‌فرستند)
        static::saving(function (Post $post) {
            if ($post->status === null) {
                $post->status = PostStatus::Published;
            }
            if ($post->status === PostStatus::Published && $post->published_at === null) {
                $post->published_at = now();
            }
        });
    }

    // ───────────── Scope ها (کوئری‌های تکراری یک جا) ─────────────

    /**
     * پست‌هایی که الان برای عموم دیده می‌شوند: وضعیت «منتشرشده»، یا «زمان‌بندی‌شده‌ای که موعدش رسیده».
     * (قید زمان در خودِ کوئری است، پس حتی اگر scheduler اجرا نشود پست سر وقت دیده می‌شود.)
     * هر کوئری عمومی (خانه، لیست، جستجو، دسته، تگ، sitemap، API) باید این را داشته باشد.
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where(fn (Builder $q) => $q
            ->where($this->qualifyColumn('status'), PostStatus::Published->value)
            ->orWhere(fn (Builder $s) => $s
                ->where($this->qualifyColumn('status'), PostStatus::Scheduled->value)
                ->where($this->qualifyColumn('published_at'), '<=', now())));
    }

    /** زمان‌بندی‌شده‌هایی که هنوز موعدشان نرسیده */
    public function scopeUpcoming(Builder $query): Builder
    {
        return $query->where($this->qualifyColumn('status'), PostStatus::Scheduled->value)
            ->where($this->qualifyColumn('published_at'), '>', now());
    }

    /** فیلتر وضعیت برای پنل ادمین: published | scheduled | draft | archived (هر چیز دیگر = همه) */
    public function scopeWithStatus(Builder $query, ?string $status): Builder
    {
        return match ($status) {
            'published' => $query->published(),
            'scheduled' => $query->upcoming(),
            'draft'     => $query->where($this->qualifyColumn('status'), PostStatus::Draft->value),
            'archived'  => $query->where($this->qualifyColumn('status'), PostStatus::Archived->value),
            default     => $query,
        };
    }

    /** فقط ستون‌های سبک (بدون content). با رابطه‌های belongsToMany هم کار می‌کند. */
    public function scopeListColumns(Builder $query): Builder
    {
        return $query->select(array_map(fn (string $c) => $this->qualifyColumn($c), self::LIST_COLUMNS));
    }

    public function scopeSearch(Builder $query, ?string $search): Builder
    {
        $term = SearchTerm::clean($search);

        if ($term === '') {
            return $query;
        }

        $like = SearchTerm::like($term);

        return $query->where(fn (Builder $q) => $q
            ->where('title', 'like', $like)
            ->orWhere('description', 'like', $like));
    }

    public function scopeCategory(Builder $query, $category): Builder
    {
        return $query->where('category_id', $category);
    }

    public function scopeTagged(Builder $query, $tagId): Builder
    {
        return $query->whereHas('tags', fn (Builder $q) => $q->where('tags.id', $tagId));
    }

    /** latest | oldest | popular  — بر اساس «تاریخ انتشار» (فقط برای لیست‌های عمومی). «popular» به withCount('likes') نیاز دارد */
    public function scopeSortBy(Builder $query, ?string $sort): Builder
    {
        return match ($sort) {
            'oldest'  => $query->orderBy($this->qualifyColumn('published_at'))->orderBy('id'),
            'popular' => $query->orderByDesc('likes_count')->orderByDesc($this->qualifyColumn('published_at'))->orderByDesc('id'),
            default   => $query->orderByDesc($this->qualifyColumn('published_at'))->orderByDesc('id'),
        };
    }

    /**
     * فقط $perCategory پستِ منتشرشده‌ی جدیدتر هر دسته، با «یک» کوئری (به‌جای خواندن همه‌ی پست‌ها در PHP).
     * نیاز: MySQL 8+ یا MariaDB 10.2+ (Window Function).
     */
    public function scopeLatestPerCategory(Builder $query, int $perCategory): Builder
    {
        $ranked = $this->newQuery()
            ->published()
            ->whereNotNull('category_id')
            ->selectRaw('id, ROW_NUMBER() OVER (PARTITION BY category_id ORDER BY published_at DESC, id DESC) as rn')
            ->toBase();

        $ids = DB::query()->fromSub($ranked, 'ranked')->where('rn', '<=', $perCategory)->select('id');

        return $query->whereIn($this->qualifyColumn('id'), $ids);
    }

    // ───────────── رابطه‌ها ─────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    /** کاربرانی که این پست را لایک کرده‌اند */
    public function likes(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'post_likes')->withTimestamps();
    }

    public function savers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'post_saves')->withTimestamps();
    }

    public function isLikedBy(?User $user): bool
    {
        return $user !== null && $this->likes()->where('users.id', $user->id)->exists();
    }

    public function isSavedBy(?User $user): bool
    {
        return $user !== null && $this->savers()->where('users.id', $user->id)->exists();
    }

    // ───────────── وضعیت انتشار ─────────────

    /** آیا الان برای عموم دیده می‌شود؟ (منطقِ scopePublished برای یک مدل) */
    public function isPublic(): bool
    {
        return match ($this->status) {
            PostStatus::Published => true,
            PostStatus::Scheduled => $this->published_at !== null && $this->published_at->lte(now()),
            default               => false,
        };
    }

    /** پیش‌نویس/زمان‌بندی/بایگانی فقط برای نویسنده و ادمین قابل مشاهده است */
    public function isViewableBy(?User $viewer): bool
    {
        return $this->isPublic() || ($viewer !== null && ($viewer->isAdmin() || $viewer->id === $this->user_id));
    }

    /** وضعیت واقعی برای نمایش: «زمان‌بندی‌شده»ای که موعدش رسیده، «منتشرشده» است (حتی اگر scheduler هنوز وضعیت را عوض نکرده) */
    protected function effectiveStatus(): Attribute
    {
        return Attribute::get(fn () => $this->status === PostStatus::Scheduled && $this->isPublic()
            ? PostStatus::Published
            : ($this->status ?? PostStatus::Published));
    }

    /** مقدار اولیه‌ی فیلد تقویم جلالی در فرم ویرایش (ارقام انگلیسی؛ فقط برای پست زمان‌بندی‌شده) */
    protected function scheduledAtJalali(): Attribute
    {
        return Attribute::get(fn () => $this->status === PostStatus::Scheduled && $this->published_at
            ? Jalalian::fromCarbon($this->published_at)->format('Y/m/d H:i')
            : '');
    }

    /** تاریخ انتشار (فقط روز) با ارقام فارسی */
    protected function publishedAtFa(): Attribute
    {
        return Attribute::get(fn () => $this->published_at
            ? \Morilog\Jalali\CalendarUtils::convertNumbers(Jalalian::fromCarbon($this->published_at)->format('Y/m/d'))
            : null);
    }

    /** تاریخ و ساعت انتشار (فارسی) برای پنل ادمین */
    protected function publishedAtWithHoursFa(): Attribute
    {
        return Attribute::get(fn () => $this->published_at
            ? \Morilog\Jalali\CalendarUtils::convertNumbers(Jalalian::fromCarbon($this->published_at)->format('Y/m/d H:i'))
            : null);
    }

    // ───────────── Accessor ها ─────────────

    /** آدرس عکس اصلی شاخص (یا null) */
    protected function imageUrl(): Attribute
    {
        return Attribute::get(fn () => $this->image ? Storage::url($this->image) : null);
    }

    /**
     * آدرس thumbnail ۶۴۰×۴۰۰ برای کارت‌ها؛ اگر برای پست قدیمی هنوز ساخته نشده، همان عکس اصلی.
     * (برای ساخت thumbnail قدیمی‌ها: php artisan images:optimize)
     */
    protected function imageThumbUrl(): Attribute
    {
        return Attribute::get(function () {
            if (! $this->image) {
                return null;
            }

            $thumb = ImageService::thumbPath($this->image);

            return Storage::disk(ImageService::DISK)->exists($thumb) ? Storage::url($thumb) : Storage::url($this->image);
        });
    }

    /** زمان تقریبی مطالعه (دقیقه)؛ ۲۰۰ کلمه در دقیقه. فقط وقتی متن کامل لود شده معنا دارد. */
    protected function readingTime(): Attribute
    {
        return Attribute::get(function () {
            $text  = trim(strip_tags((string) $this->body_html));
            $words = $text === '' ? 0 : count(preg_split('/\s+/u', $text, -1, PREG_SPLIT_NO_EMPTY));

            return max(1, (int) ceil($words / 200));
        });
    }

    /** همان زمان مطالعه با ارقام فارسی */
    protected function readingTimeFa(): Attribute
    {
        return Attribute::get(fn () => Fa::digits($this->reading_time));
    }

    /** تعداد بازدید با ارقام فارسی و جداکننده‌ی هزارگان */
    protected function viewsFa(): Attribute
    {
        return Attribute::get(fn () => Fa::number($this->views_count ?? 0));
    }

    /**
     * یک بازدید اضافه می‌کند. عمداً با کوئری پایه (toBase) تا updated_at پست عوض نشود
     * و بدون لود/ذخیره‌ی مدل (یک UPDATE ساده‌ی اتمی).
     */
    public function recordView(): void
    {
        static::query()->toBase()->where($this->getKeyName(), $this->getKey())->increment('views_count');
        $this->views_count = ($this->views_count ?? 0) + 1;
    }

    /** HTML آماده‌ی نمایش در صفحه‌ی پست (برای پست‌های قدیمی: متن ساده با خط‌شکن) */
    protected function bodyHtml(): Attribute
    {
        return Attribute::get(function () {
            if (filled($this->content)) {
                return HtmlSanitizer::clean($this->content);   // دفاع دوم؛ ارزان است
            }

            return $this->legacyHtml();
        });
    }

    /** HTML اولیه برای ادیتور در صفحه‌ی edit */
    protected function editorHtml(): Attribute
    {
        return Attribute::get(fn () => filled($this->content) ? $this->content : $this->legacyHtml());
    }

    private function legacyHtml(): string
    {
        return collect(preg_split('/\R/u', (string) $this->description))
            ->map(fn ($line) => trim($line))
            ->filter()
            ->map(fn ($line) => '<p dir="auto">' . e($line) . '</p>')
            ->implode('');
    }
}
