<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Support\HtmlSanitizer;
use Illuminate\Database\Eloquent\Casts\Attribute;



class Post extends Model
{
    use HasFactory;

    protected $fillable =[
        'title',
        'description',
        'content',
        'category_id',
        'image',
        'user_id',
    ];

// defining a local scope
  /*  public function scopeWithImage(Builder $query):void
    {
        $query->whereNotNull('image');
    }*/

    public function scopeSearch(Builder $query,$search): Builder{
        return $query->where(function (Builder $query) use ($search) {
            $query->where('title', 'LIKE', "%{$search}%")
                ->orWhere('description', 'LIKE', "%{$search}%");
        });
    }

    public function scopeCategory(Builder $query,$category): Builder{
        return $query->where('category_id',$category);
    }

    public function user(): belongsTo{
        return $this->belongsTo(User::class);
    }

    public function comments(): hasMany{
        return $this->hasMany(Comment::class);
    }
    public function category(): belongsTo{
        return $this->belongsTo(Category::class);
    }

    public function tags(): belongsToMany{
        return $this->belongsToMany(Tag::class);
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



    /** کاربرانی که این پست را لایک کرده‌اند */
    public function likes(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'post_likes')->withTimestamps();
    }

    /** آیا این کاربر پست را لایک کرده؟ (مهمان = false) */
    public function isLikedBy(?User $user): bool
    {
        return $user !== null && $this->likes()->where('users.id', $user->id)->exists();
    }

}
