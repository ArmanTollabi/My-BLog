<?php

namespace App\Console\Commands;

use App\Enums\PostStatus;
use App\Models\Post;
use Illuminate\Console\Command;

class PublishScheduledPosts extends Command
{
    protected $signature = 'posts:publish-scheduled';

    protected $description = 'پست‌های زمان‌بندی‌شده‌ای که موعدشان رسیده را «منتشرشده» می‌کند';

    public function handle(): int
    {
        // toBase: فقط وضعیت عوض شود (updated_at دست نمی‌خورد). نمایش عمومی به این فرمان وابسته نیست (Post::scopePublished زمان را هم چک می‌کند).
        $count = Post::query()->toBase()
            ->where('status', PostStatus::Scheduled->value)
            ->where('published_at', '<=', now())
            ->update(['status' => PostStatus::Published->value]);

        $this->info("{$count} پست منتشر شد.");

        return self::SUCCESS;
    }
}
