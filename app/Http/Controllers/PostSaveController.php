<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PostSaveController extends Controller
{
    public function toggle(Request $request, Post $post): JsonResponse
    {
        $result = $post->savers()->toggle($request->user()->id);

        return response()->json([
            'saved' => count($result['attached']) > 0,
        ]);
    }
}
