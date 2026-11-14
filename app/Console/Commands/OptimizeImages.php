<?php

namespace App\Console\Commands;

use App\Models\Post;
use App\Models\User;
use App\Services\ImageService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

/** برای داده‌های قدیمی: thumbnail پست‌ها و کوچک‌کردن آواتارها.  php artisan images:optimize [--avatars] */
class OptimizeImages extends Command
{
    protected $signature = 'images:optimize {--avatars : آواتارهای بزرگ یا غیر webp را هم کوچک کن}';

    protected $description = 'ساخت thumbnail برای عکس شاخص پست‌های قدیمی (و اختیاری: کوچک‌کردن آواتارها)';

    public function handle(ImageService $images): int
    {
        $disk = Storage::disk(ImageService::DISK);
        $made = 0;

        Post::query()->whereNotNull('image')->select(['id', 'image'])->chunkById(100, function ($posts) use ($images, $disk, &$made) {
            foreach ($posts as $post) {
                if ($disk->exists(ImageService::thumbPath($post->image))) {
                    continue;
                }

                try {
                    if ($images->makeThumbnail($post->image)) {
                        $made++;
                    } else {
                        $this->warn("فایل پیدا نشد (پست {$post->id}): {$post->image}");
                    }
                } catch (\Throwable $e) {
                    $this->warn("رد شد (پست {$post->id}): " . $this->reason($e));
                }
            }
        });
        $this->info("thumbnail ساخته‌شده: {$made}");

        if ($this->option('avatars')) {
            $shrunk = 0;

            User::query()->whereNotNull('avatar')->select(['id', 'avatar'])->chunkById(100, function ($users) use ($images, $disk, &$shrunk) {
                foreach ($users as $user) {
                    $old = $user->avatar;
                    $big = $disk->exists($old) && $disk->size($old) > 150 * 1024;

                    if (! $disk->exists($old) || (! $big && str_ends_with($old, '.webp'))) {
                        continue;
                    }

                    try {
                        if ($new = $images->shrinkAvatar($old, 'avatars')) {
                            User::whereKey($user->id)->update(['avatar' => $new]);
                            $disk->delete($old);
                            $shrunk++;
                        }
                    } catch (\Throwable $e) {
                        $this->warn("رد شد (کاربر {$user->id}): " . $this->reason($e));
                    }
                }
            });
            $this->info("آواتار کوچک‌شده: {$shrunk}");
        }

        return self::SUCCESS;
    }

    private function reason(\Throwable $e): string
    {
        return $e instanceof \Illuminate\Validation\ValidationException
            ? collect($e->errors())->flatten()->first()
            : $e->getMessage();
    }
}
