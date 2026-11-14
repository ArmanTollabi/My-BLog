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
            'Disallow: /login',
            'Disallow: /register',
            'Disallow: /forgot-password',
            'Disallow: /reset-password',
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

        return response(view('seo.sitemap', ['urls' => $urls])->render(), 200, ['Content-Type' => 'application/xml; charset=UTF-8']);
    }
}
