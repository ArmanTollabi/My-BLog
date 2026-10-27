<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comment extends Model
{
    use SoftDeletes;

    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved';

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
        return $this->status === self::STATUS_APPROVED;
    }

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where($this->qualifyColumn('status'), self::STATUS_APPROVED);
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where($this->qualifyColumn('status'), self::STATUS_PENDING);
    }

    /**
     * کامنت‌هایی که این بیننده حق دیدنشان را دارد:
     *  - ادمین: همه (به‌جز سطل زباله)
     *  - کاربر عادی: تایید‌شده‌ها + کامنت‌های خودش
     *  - مهمان: فقط تایید‌شده‌ها
     */
    public function scopeVisibleTo(Builder $query, ?User $user): Builder
    {
        if ($user && $user->role === 'admin') {
            return $query;
        }

        return $query->where(function (Builder $q) use ($user) {
            $q->where($this->qualifyColumn('status'), self::STATUS_APPROVED);

            if ($user) {
                $q->orWhere($this->qualifyColumn('user_id'), $user->id);
            }
        });
    }
}
