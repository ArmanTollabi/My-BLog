<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use Illuminate\Http\Response;
use Illuminate\Support\LazyCollection;

class SeoController extends Controller
{
    /** /robots.txt — پنل‌ها و صفحه‌های حساب از موتورهای جستجو دور می‌مانند */
    public function robots(): Response
    {
        $body = implode("\n", [
            'User-agent: *',
            'Disallow: /admin',
            'Disallow: /dashboard',
            'Disallow: /auth/',
            'Disallow: /posts/search',
            '',
            'Sitemap: ' . url('/sitemap.xml'),
            '',
        ]);

        return response($body, 200, ['Content-Type' => 'text/plain; charset=UTF-8']);
    }

    /** /sitemap.xml — خانه، لیست پست‌ها، دسته‌ها و همه‌ی پست‌ها (با جریان‌خوانی؛ بدون لود همه‌ی مدل‌ها) */
    public function sitemap(): Response
    {
        $urls = LazyCollection::make(function () {
            yield ['loc' => route('home')];
            yield ['loc' => route('posts.index')];

            foreach (Category::query()->select(['id', 'slug', 'updated_at'])->orderBy('id')->lazy(500) as $category) {
                yield ['loc' => route('categories.show', $category), 'lastmod' => $category->updated_at?->toIso8601String()];
            }

            foreach (Post::query()->published()->select(['id', 'slug', 'updated_at'])->orderBy('id')->lazy(500) as $post) {
                yield ['loc' => route('posts.show', $post), 'lastmod' => $post->updated_at?->toIso8601String()];
            }
        });

        // خط <?xml باید از PHP بیاید، نه از blade: روی سرورهایی که short_open_tag روشن است،
        // نوشتن آن داخل فایل blade باعث ParseError می‌شود.
        $xml = '<' . '?xml version="1.0" encoding="UTF-8"?>' . "\n"
            . view('seo.sitemap', ['urls' => $urls])->render();

        return response($xml, 200, ['Content-Type' => 'application/xml; charset=UTF-8']);
    }
}
