<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Http\Request;

class TagController extends Controller
{
    public function index(){
        $tags = Tag::withCount(['posts' => fn ($q) => $q->published()])
        ->orderBy('title')
        ->get();

        return view('tags.index', compact('tags'));
    }
    public function show(Tag $tag)
    {
        $posts = $tag->posts()
            ->published()
            ->listColumns()
            ->with(['user:id,name,avatar', 'category:id,title,slug'])
            ->sortBy('latest')
            ->get();

        return view('tags.show', compact('tag', 'posts'));
    }

// و index با شمارش پست‌ها (بلید از $tag->posts_count استفاده می‌کند):
//   $tags = Tag::withCount('posts')->orderBy('title')->get();


    public function create(){
        $this->authorize('create', Tag::class);
        return view('tags.create');
    }

    public function store(Request $request){
        $this->authorize('create', Tag::class);
        $validated = $request->validate([
            'title' => 'required|string|unique:tags',
        ]);

        Tag::create($validated);
        return redirect()->route('tags.index')->with('success', 'Tag created!');
    }

    public function destroy(Tag $tag){
        $this->authorize('delete', $tag);
        $tag->delete();
        return redirect()->back()->with('success','tag has been deleted');
    }
}
