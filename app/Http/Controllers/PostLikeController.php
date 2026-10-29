<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PostLikeController extends Controller
{
    /**
     * لایک / برداشتن لایک (toggle). فقط کاربر لاگین‌شده؛ خروجی JSON برای AJAX.
     */
    public function toggle(Request $request, Post $post): JsonResponse
    {
        // toggle: اگر لایک بوده برمی‌دارد، اگر نبوده اضافه می‌کند
        $result = $post->likes()->toggle($request->user()->id);

        return response()->json([
            'liked' => count($result['attached']) > 0,
            'count' => $post->likes()->count(),
        ]);
    }
}
