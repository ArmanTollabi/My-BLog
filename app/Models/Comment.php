<?php

namespace App\Models;

use App\Enums\CommentStatus;
use App\Traits\HasJalaliDates;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comment extends Model
{
    use SoftDeletes;
    use HasJalaliDates;

    // سازگاری با کدهای قدیمی (Comment::STATUS_PENDING)؛ منبع اصلی حالا Enum است
    public const STATUS_PENDING = CommentStatus::Pending->value;
    public const STATUS_APPROVED = CommentStatus::Approved->value;

    protected $fillable = [
        'user_id',
        'post_id',
        'parent_id',
        'body',
        'status',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    public function replies(): HasMany
    {
        return $this->hasMany(Comment::class, 'parent_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    public function isApproved(): bool
    {
        return $this->status === CommentStatus::Approved->value;
    }

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where($this->qualifyColumn('status'), CommentStatus::Approved->value);
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where($this->qualifyColumn('status'), CommentStatus::Pending->value);
    }

    /**
     * کامنت‌هایی که این بیننده حق دیدنشان را دارد:
     *  - ادمین: همه (به‌جز سطل زباله)
     *  - کاربر عادی: تایید‌شده‌ها + کامنت‌های خودش
     *  - مهمان: فقط تایید‌شده‌ها
     */
    public function scopeVisibleTo(Builder $query, ?User $user): Builder
    {
        if ($user?->isAdmin()) {
            return $query;
        }

        return $query->where(function (Builder $q) use ($user) {
            $q->where($this->qualifyColumn('status'), CommentStatus::Approved->value);

            if ($user) {
                $q->orWhere($this->qualifyColumn('user_id'), $user->id);
            }
        });
    }
}
