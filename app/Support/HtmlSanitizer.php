<?php

namespace App\Support;

use DOMDocument;
use DOMElement;
use DOMNode;
use DOMXPath;
use Illuminate\Support\Facades\Storage;

/**
 * پاک‌سازی HTML تولیدشده توسط ادیتور (جلوگیری از XSS).
 * روش «لیست سفید»: فقط تگ‌ها و attribute های مجاز می‌مانند، بقیه حذف یا باز می‌شوند.
 */
class HtmlSanitizer
{
    /** تگ‌های مجاز */
    private const ALLOWED_TAGS = [
        'p', 'br', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6',
        'strong', 'b', 'em', 'i', 'u', 's', 'strike', 'del', 'sub', 'sup',
        'ul', 'ol', 'li', 'blockquote', 'pre', 'code', 'hr',
        'a', 'img', 'span',
    ];

    /** تگ‌هایی که همراه با محتوایشان کامل حذف می‌شوند */
    private const DROP_WITH_CONTENT = [
        'script', 'style', 'iframe', 'frame', 'frameset', 'object', 'embed', 'applet',
        'svg', 'math', 'form', 'input', 'button', 'textarea', 'select', 'option',
        'noscript', 'template', 'link', 'meta', 'base', 'title', 'head', 'audio', 'video',
    ];

    public static function clean(?string $html): string
    {
        $html = trim((string) $html);

        if ($html === '') {
            return '';
        }

        $dom = new DOMDocument('1.0', 'UTF-8');
        $previous = libxml_use_internal_errors(true);
        $dom->loadHTML(
            '<!DOCTYPE html><html><head><meta charset="utf-8"></head><body><div id="__root">' . $html . '</div></body></html>',
            LIBXML_NONET
        );
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        $root = (new DOMXPath($dom))->query('//div[@id="__root"]')->item(0);

        if (! $root) {
            return '';
        }

        self::sanitizeChildren($root, $dom);

        $out = '';
        foreach ($root->childNodes as $child) {
            $out .= $dom->saveHTML($child);
        }

        return trim($out);
    }

    /** متن ساده‌ی HTML (برای خلاصه‌ی پست و جستجو) */
    public static function plainText(?string $html, int $limit = 250): string
    {
        $html = preg_replace('~</(p|h[1-6]|li|blockquote|pre|div)>|<br\s*/?>~i', ' ', (string) $html);
        $text = html_entity_decode(strip_tags($html), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = trim(preg_replace('/\s+/u', ' ', $text));

        return \Illuminate\Support\Str::limit($text, $limit, '…');
    }

    /** آیا محتوا چیزی دارد (متن یا عکس)؟ */
    public static function hasContent(?string $html): bool
    {
        return self::plainText($html, 10) !== '' || stripos((string) $html, '<img') !== false;
    }

    /** مسیر (روی دیسک public) عکس‌هایی که داخل HTML استفاده شده‌اند */
    public static function imagePaths(?string $html): array
    {
        if (! $html || ! preg_match_all('/<img\b[^>]*\bsrc="([^"]+)"/i', $html, $m)) {
            return [];
        }

        $paths = [];
        foreach ($m[1] as $src) {
            $path = self::storagePath(html_entity_decode($src));
            if ($path !== null) {
                $paths[] = $path;
            }
        }

        return array_values(array_unique($paths));
    }

    // ---------------------------------------------------------------------

    private static function sanitizeChildren(DOMNode $parent, DOMDocument $dom): void
    {
        foreach (iterator_to_array($parent->childNodes) as $node) {

            if ($node->nodeType === XML_TEXT_NODE) {
                continue;
            }

            if (! $node instanceof DOMElement) {      // کامنت و ...
                $parent->removeChild($node);
                continue;
            }

            $tag = strtolower($node->tagName);

            if (in_array($tag, self::DROP_WITH_CONTENT, true)) {
                $parent->removeChild($node);
                continue;
            }

            self::sanitizeChildren($node, $dom);

            if (! in_array($tag, self::ALLOWED_TAGS, true)) {
                // تگ ناشناخته: فقط محتوایش می‌ماند
                while ($node->firstChild) {
                    $parent->insertBefore($node->firstChild, $node);
                }
                $parent->removeChild($node);
                continue;
            }

            self::sanitizeAttributes($node, $tag);

            // عکسِ بدون src معتبر بی‌فایده است
            if ($tag === 'img' && ! $node->hasAttribute('src')) {
                $parent->removeChild($node);
            }
        }
    }

    private static function sanitizeAttributes(DOMElement $el, string $tag): void
    {
        foreach (iterator_to_array($el->attributes) as $attr) {
            $name  = strtolower($attr->name);
            $value = $attr->value;
            $keep  = false;

            switch (true) {
                case $name === 'class':
                    // فقط کلاس‌های خودِ ادیتور (ql-align-center و ...)
                    $classes = array_filter(preg_split('/\s+/', $value), fn ($c) => preg_match('/^ql-[a-z0-9-]+$/', $c));
                    if ($classes) {
                        $el->setAttribute('class', implode(' ', $classes));
                        $keep = true;
                    }
                    break;

                case $name === 'dir':
                    $keep = in_array(strtolower($value), ['rtl', 'ltr', 'auto'], true);
                    break;

                case $tag === 'a' && $name === 'href':
                    $url  = self::safeUrl($value);
                    $keep = $url !== null;
                    if ($keep) {
                        $el->setAttribute('href', $url);
                    }
                    break;

                case $tag === 'a' && $name === 'title':
                    $keep = true;
                    break;

                case $tag === 'img' && $name === 'src':
                    $keep = self::storagePath($value) !== null;
                    break;

                case $tag === 'img' && $name === 'alt':
                    $keep = true;
                    break;
            }

            if (! $keep) {
                $el->removeAttribute($attr->name);
            }
        }

        if ($tag === 'a' && $el->hasAttribute('href')) {
            $el->setAttribute('target', '_blank');
            $el->setAttribute('rel', 'noopener noreferrer nofollow');
        }

        if ($tag === 'img') {
            $el->setAttribute('loading', 'lazy');
        }

        // جهت متن هر بلوک خودکار تشخیص داده شود (فارسی راست‌چین، انگلیسی چپ‌چین)
        if (in_array($tag, ['p', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'li', 'blockquote'], true) && ! $el->hasAttribute('dir')) {
            $el->setAttribute('dir', 'auto');
        }
    }

    /** فقط http(s)، mailto، tel، لینک نسبی و #anchor */
    private static function safeUrl(string $url): ?string
    {
        $url = trim(preg_replace('/[\x00-\x20\x7F]+/u', '', $url));

        if ($url === '') {
            return null;
        }

        if (preg_match('/^(https?:|mailto:|tel:)/i', $url) || $url[0] === '#' || ($url[0] === '/' && ! str_starts_with($url, '//'))) {
            return $url;
        }

        return null;
    }

    /** اگر src به فایلی روی دیسک public همین سایت اشاره کند، مسیر آن را برمی‌گرداند */
    private static function storagePath(string $src): ?string
    {
        $src  = trim($src);
        $base = Storage::disk('public')->url('');           // مثلاً http://site/storage/

        foreach ([$base, '/storage/'] as $prefix) {
            if ($prefix !== '' && str_starts_with($src, $prefix)) {
                $path = ltrim(substr($src, strlen($prefix)), '/');

                // فقط عکس‌های آپلودشده‌ی ادیتور؛ بدون ../
                if (str_starts_with($path, 'posts/') && ! str_contains($path, '..')) {
                    return $path;
                }
            }
        }

        return null;
    }
}
