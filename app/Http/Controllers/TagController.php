<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Http\Request;

class TagController extends Controller
{
    public function index(){
        $tags = Tag::withCount('posts')->latest()->get();
        return view('tags.index', compact('tags'));
    }
    public function show(Tag $tag){
        //$posts = $tag->posts()->with('user')->latest()->get();   ---This is also correct!
        $tag->load('posts.user');
        return view('tags.show', compact('tag'));

    }

    public function create(){

        return view('tags.create');
    }

    public function store(Request $request){
        $validated = $request->validate([
            'title' => 'required|string|unique:tags',
        ]);

        Tag::create($validated);
        return redirect()->route('tags.index')->with('success', 'Tag created!');
    }

    public function destroy(Tag $tag){
        $tag->delete();
        return redirect()->back()->with('success','tag has been deleted');
    }
}
