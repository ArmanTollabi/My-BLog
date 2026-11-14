<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

/**
 * همه‌ی پردازش عکس‌ها یک‌جا: تبدیل به webp، کوچک‌کردن، thumbnail کارت‌ها و آواتار مربعی.
 * (کنترلرها فقط فایل را می‌دهند و مسیر ذخیره‌شده را می‌گیرند.)
 */
class ImageService
{
    public const DISK = 'public';

    public const THUMB_WIDTH  = 640;   // کارت‌ها (نسبت ۱۶:۱۰ مثل کارت)
    public const THUMB_HEIGHT = 400;
    public const AVATAR_SIZE  = 256;   // آواتار مربع (بزرگ‌ترین نمایش ~۱۲۸px با پشتیبانی رتینا)

    /** بیشتر از این تعداد پیکسل (≈ ۱۰۰ مگاپیکسل) پذیرفته نمی‌شود: GD عکس را کامل در رم باز می‌کند (هر پیکسل ≈ ۴ بایت) */
    private const MAX_PIXELS = 100_000_000;

    private ImageManager $manager;

    public function __construct()
    {
        $this->manager = new ImageManager(new Driver());
    }

    /** عکس را حداکثر تا $max پیکسل کوچک می‌کند و به webp ذخیره می‌کند. */
    public function storeScaled(UploadedFile $file, string $dir, int $max = 1600, int $quality = 80): string
    {
        $this->guard(@getimagesize($file->getRealPath()));

        $encoded = $this->manager->decodeSplFileInfo($file)
            ->scaleDown(width: $max, height: $max)
            ->encodeUsingMediaType('image/webp', quality: $quality);

        return $this->put($dir, (string) $encoded);
    }

    /** عکس شاخص پست: نسخه‌ی اصلی (حداکثر ۱۶۰۰px) + thumbnail ۶۴۰×۴۰۰ کنار آن. */
    public function storeCover(UploadedFile $file, string $dir): string
    {
        $path = $this->storeScaled($file, $dir);
        $this->makeThumbnail($path);

        return $path;
    }

    /** آواتار: برش مربعی ۲۵۶×۲۵۶ و webp (به‌جای فایل چندمگابایتی اصلی). */
    public function storeAvatar(UploadedFile $file, string $dir): string
    {
        $this->guard(@getimagesize($file->getRealPath()));

        $encoded = $this->manager->decodeSplFileInfo($file)
            ->cover(self::AVATAR_SIZE, self::AVATAR_SIZE)
            ->encodeUsingMediaType('image/webp', quality: 80);

        return $this->put($dir, (string) $encoded);
    }

    /** thumbnail یک عکس موجود روی دیسک. اگر فایل نبود null. */
    public function makeThumbnail(string $path): ?string
    {
        $disk = Storage::disk(self::DISK);

        if (! $disk->exists($path)) {
            return null;
        }

        $thumb  = self::thumbPath($path);
        $binary = $disk->get($path);
        $this->guard(@getimagesizefromstring($binary));

        $encoded = $this->manager->decodeBinary($binary)
            ->cover(self::THUMB_WIDTH, self::THUMB_HEIGHT)
            ->encodeUsingMediaType('image/webp', quality: 75);

        $disk->put($thumb, (string) $encoded);

        return $thumb;
    }

    /** آواتارِ موجود (بزرگ یا غیر webp) را کوچک می‌کند و مسیر جدید را برمی‌گرداند. */
    public function shrinkAvatar(string $path, string $dir): ?string
    {
        $disk = Storage::disk(self::DISK);

        if (! $disk->exists($path)) {
            return null;
        }

        $binary = $disk->get($path);
        $this->guard(@getimagesizefromstring($binary));

        $encoded = $this->manager->decodeBinary($binary)
            ->cover(self::AVATAR_SIZE, self::AVATAR_SIZE)
            ->encodeUsingMediaType('image/webp', quality: 80);

        return $this->put($dir, (string) $encoded);
    }

    /** posts/uploads/2026/11/a.webp → posts/uploads/2026/11/a-640.webp */
    public static function thumbPath(string $path): string
    {
        return preg_replace('/\.\w+$/', '', $path) . '-' . self::THUMB_WIDTH . '.webp';
    }

    /**
     * قبل از باز کردن عکس، ابعادش را (فقط از هدر فایل، بدون باز کردن) می‌خواند:
     * عکس خیلی بزرگ رد می‌شود، و اگر رم PHP کم باشد (XAMPP: ۱۲۸MB) سقف رم موقتاً بالا می‌رود.
     * خطای «Allowed memory size exhausted» قابل گرفتن نیست؛ پس باید قبل از decode جلویش را گرفت.
     */
    private function guard(array|false $info): void
    {
        if (! $info || empty($info[0]) || empty($info[1])) {
            throw ValidationException::withMessages(['image' => 'فایل تصویر معتبر نیست.']);
        }

        [$width, $height] = $info;

        if ($width * $height > self::MAX_PIXELS) {
            throw ValidationException::withMessages(['image' => 'ابعاد تصویر خیلی بزرگ است؛ تصویری کوچک‌تر انتخاب کنید.']);
        }

        $limit = $this->memoryLimit();
        if ($limit === -1) {
            return;
        }

        $need = (int) ($width * $height * 4 * 2.2) + 48 * 1024 * 1024 + memory_get_usage(true);
        if ($need > $limit) {
            ini_set('memory_limit', (string) $need);
        }
    }

    private function memoryLimit(): int
    {
        $raw = trim((string) ini_get('memory_limit'));
        if ($raw === '' || $raw === '-1') {
            return -1;
        }

        $value = (int) $raw;

        return match (strtolower(substr($raw, -1))) {
            'g'     => $value * 1024 ** 3,
            'm'     => $value * 1024 ** 2,
            'k'     => $value * 1024,
            default => $value,
        };
    }

    private function put(string $dir, string $binary): string
    {
        $path = trim($dir, '/') . '/' . now()->format('Y/m/d') . '/' . Str::uuid() . '.webp';
        Storage::disk(self::DISK)->put($path, $binary);

        return $path;
    }
}
