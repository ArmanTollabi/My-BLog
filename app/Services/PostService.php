<?php

namespace App\Services;

use App\Enums\PostStatus;
use App\Models\Post;
use App\Models\User;
use App\Support\HtmlSanitizer;
use App\Support\JalaliDate;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

/** منطق ساخت / ویرایش / حذف پست (کنترلر فقط درخواست را می‌گیرد و جواب می‌دهد) */
class PostService
{
    private const DISK = 'public';

    public function create(array $data, User $author): Post
    {
        $content = $this->cleanContent($data['content']);

        return DB::transaction(function () use ($data, $content, $author) {
            $post = Post::create([
                'title'       => $data['title'],
                'category_id' => $data['category'],
                'content'     => $content,
                'description' => HtmlSanitizer::plainText($content, 250), // خلاصه برای لیست‌ها و جستجو
                'image'       => $data['image_path'] ?? null,
                'user_id'     => $author->id,
                ...$this->publication($data),
            ]);
            $post->tags()->attach($data['tags'] ?? []);

            return $post;
        });
    }

    public function update(Post $post, array $data): Post
    {
        $content = $this->cleanContent($data['content']);
        $attributes = [
            'title'       => $data['title'],
            'category_id' => $data['category'],
            'content'     => $content,
            'description' => HtmlSanitizer::plainText($content, 250),
            ...$this->publication($data, $post),
        ];

        $oldFiles = [];

        // عکس شاخص جدید → عکس قبلی حذف شود
        if (! empty($data['image_path'])) {
            if ($post->image && $post->image !== $data['image_path']) {
                $oldFiles[] = $post->image;
                $oldFiles[] = ImageService::thumbPath($post->image);
            }
            $attributes['image'] = $data['image_path'];
        }

        // عکس‌هایی که از داخل متن برداشته شده‌اند
        $removed = array_diff(HtmlSanitizer::imagePaths($post->content), HtmlSanitizer::imagePaths($content));
        $oldFiles = array_merge($oldFiles, array_values($removed));

        DB::transaction(function () use ($post, $attributes, $data) {
            $post->update($attributes);
            $post->tags()->sync($data['tags'] ?? []);
        });

        // فایل‌ها فقط بعد از موفق بودن دیتابیس پاک می‌شوند
        $this->deleteFiles($oldFiles);

        return $post;
    }

    public function delete(Post $post): void
    {
        $files = array_merge(
            $post->image ? [$post->image, ImageService::thumbPath($post->image)] : [],
            HtmlSanitizer::imagePaths($post->content)
        );

        $post->delete();
        $this->deleteFiles($files);
    }

    /**
     * status + published_at از روی فرم.
     *  - منتشرشده: تاریخ انتشارِ قبلی (اگر گذشته باشد) می‌ماند تا با هر ویرایش «تازه» نشود؛ وگرنه همین الان.
     *  - زمان‌بندی‌شده: تاریخ جلالی فرم (سرور آینده بودنش را در SavePostRequest چک کرده).
     *  - پیش‌نویس/بایگانی: تاریخ انتشار دست نمی‌خورد (برای پست جدید null).
     */
    private function publication(array $data, ?Post $post = null): array
    {
        $status = PostStatus::tryFrom($data['status'] ?? '') ?? PostStatus::Published;
        $current = $post?->published_at;

        $publishedAt = match ($status) {
            PostStatus::Published => ($current && $current->lte(now())) ? $current : Carbon::now(),
            PostStatus::Scheduled => JalaliDate::parse($data['scheduled_at'] ?? null),
            default               => $current,
        };

        return ['status' => $status, 'published_at' => $publishedAt];
    }

    private function cleanContent(string $html): string
    {
        $content = HtmlSanitizer::clean($html);

        if (! HtmlSanitizer::hasContent($content)) {
            throw ValidationException::withMessages(['content' => 'متن پست نمی‌تواند خالی باشد.']);
        }

        return $content;
    }

    private function deleteFiles(array $paths): void
    {
        $paths = array_values(array_filter($paths));

        if ($paths) {
            Storage::disk(self::DISK)->delete($paths);
        }
    }
}
