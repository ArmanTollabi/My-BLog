<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Models\User;

class CategoryController extends Controller
{
    public function apiIndex(){
        $categories = Category::latest()->get();
        return CategoryResource::collection($categories);
    }

    public function apiShow(Category $category){

        return new CategoryResource($category);
    }

    public function apiStore(StoreCategoryRequest $request){

        $validated = $request->validated();
        $category = Category::create($validated);

        return response()->json([
            'message' => 'Category created successfully',
            'category' => $category
        ],201);
    }
    public function index(Category $category,User $user){

        $categories = Category::withCount('posts')->get();
        return view('categories.index', compact('categories','user'));
    }
    public function create(){
        $this->authorize('create_cat', Category::class);
        return view('categories.create');
    }

    public function store(Request $request,Category $category){
        $this->authorize('create_cat', $category);
        $validated = $request->validate([
            'title' => 'required|unique:categories|max:255',
        ]);

        Category::create($validated);

        return redirect()->route('categories.index')->with('success', 'Category created successfully');
    }
    public function show(Category $category){

        $posts = $category->posts()->get();
        return view('categories.show', compact('category', 'posts'));
    }

    public function destroy(Category $category){
        $this->authorize('delete_cat', $category);
        $category->delete();
        return redirect()->route('categories.index')->with('success', 'Category deleted successfully');
    }
}
