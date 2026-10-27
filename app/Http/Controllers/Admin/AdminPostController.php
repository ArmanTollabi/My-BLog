<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;

class AdminPostController extends Controller
{
    /** لیست مختصر پست‌ها برای ادمین */
    public function index(Request $request)
    {
        $search = trim((string) $request->query('search'));

        $posts = Post::with(['user', 'category'])
            ->withCount('comments')
            ->when($search !== '', fn ($q) => $q->where('title', 'like', "%{$search}%"))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.posts.index', compact('posts'));
    }
}
