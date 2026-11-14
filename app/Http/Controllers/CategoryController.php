<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function apiIndex()
    {
        return CategoryResource::collection(Category::latest()->get());
    }

    public function apiShow(Category $category)
    {
        return new CategoryResource($category);
    }

    public function apiStore(StoreCategoryRequest $request)
    {
        $category = Category::create($request->validated());

        return response()->json([
            'message'  => 'Category created successfully',
            'category' => $category,
        ], 201);
    }

    public function index()
    {
        $categories = Category::withCount(['posts' => fn ($q) => $q->published()])->orderBy('title')->get();

        return view('categories.index', compact('categories'));
    }

    public function create()
    {
        $this->authorize('create_cat', Category::class);

        return view('categories.create');
    }

    public function store(Request $request, Category $category)
    {
        $this->authorize('create_cat', $category);

        $validated = $request->validate([
            'title' => 'required|unique:categories|max:255',
        ]);

        Category::create($validated);

        return redirect()->route('categories.index')->with('success', 'دسته بندی مورد نظر با موفقیت ساخته شد');
    }

    public function show(Category $category)
    {
        // بدون N+1 (نویسنده‌ها یک‌جا) و بدون ستون سنگین content
        $posts = $category->posts()
            ->published()
            ->listColumns()
            ->with(['user:id,name,avatar', 'category:id,title,slug'])
            ->sortBy('latest')
            ->get();

        return view('categories.show', compact('category', 'posts'));
    }

    public function destroy(Category $category)
    {
        $this->authorize('delete_cat', $category);
        $category->delete();

        return redirect()->route('categories.index')->with('success', 'دسته بندی مورد نظر با موفقیت پاک شد');
    }
}
