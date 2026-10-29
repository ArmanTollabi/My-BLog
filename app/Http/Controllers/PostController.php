<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Http\Resources\PostResource;
use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use App\Support\HtmlSanitizer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;


class PostController extends Controller
{

    public function apiIndex(Request $request){
        $per_page = min($request->integer('per_page', 6), 7);
        $posts = Post::with('user','tags','category')
            ->when($request->search,fn($query)   => $query->search($request->search))
            ->when($request->category,fn($query) => $query->category($request->category))
            ->when($request->sort === 'oldest',fn($query) => $query->oldest(),fn($query) => $query->latest())
            ->latest()
            ->paginate($per_page)
            ->withQueryString();
        return PostResource::collection($posts);
    }

    public function apiShow(Post $post)
    {
        $post->load('user','tags');
        return new PostResource($post);
    }
    public function apiStore(StorePostRequest $request)
    {
        $this->authorize('create', Post::class);
        $validated = $request->validated();
        $validated['user_id'] =$request->user()->id;
        $post = Post::create($validated);

        return response()->json([
            'message' => 'Post created successfully',
            'post'    => new PostResource($post)
        ],201);
    }

    public function apiUpdate(UpdatePostRequest $request, Post $post){
        $this->authorize('update', $post);
        $validated = $request->validated();
        $post->update($validated);
        return response()->json([
            'message' => 'Post updated successfully',
            'post'    => new PostResource($post)
        ],200);
    }

    public function apiDestroy(Post $post){
        $this->authorize('delete', $post);
        $post->delete();
        return response()->noContent();
    }

    public function home(){
        $sections = Post::with('category')
            ->whereNotNull('category_id')
            ->latest()
            ->get()
            ->groupBy('category_id')
            ->map(fn ($posts) => [
                'category' => $posts->first()->category,
                'posts'    => $posts->take(6), // فقط ۶ پستِ جدیدتر هر کتگوری
            ])
            ->filter(fn ($s) => $s['category'] !== null);

        return view('index', compact('sections'));
    }
    public function index(Request $request){

        $tags = Tag::latest()->get();
        $categories = Category::latest()->get();

        $posts = Post::with(['user', 'category', 'tags'])->withCount('likes')->when($request->search, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->where('title', 'like', '%' . $search . '%')
                    ->orWhere('description', 'like', '%' . $search . '%');
            });
        })
            //category filtering
            ->when($request->category, function ($query, $category) {
                $query->where('category_id', $category);
            })
            //tags filtering
            ->when($request->tag, function ($query, $tag) {
                $query->whereHas('tags', function ($query) use ($tag) {
                    $query->where('tags.id', $tag);
                });
            })
            //sorting latest or oldest
            ->when($request->sort === 'oldest', function ($query) {
                $query->oldest();
            },
                function ($query) {
                    $query->latest();
                })
            ->paginate(6)
            ->withQueryString();

        return view('posts.index', compact('posts','categories','tags'));
    }

    public function show(Post $post){
        $post->load('user','category','tags')->loadCount('likes');
        $liked = $post->isLikedBy(auth()->user());

        // مهمان بعد از ورود/ثبت‌نام (از پاپ‌آپ لایک) به همین پست برگردد
        if (auth()->guest()) {
            session()->put('url.intended', url()->current());
        }

        $comments = $post->comments()
            ->whereNull('parent_id')
            ->with([
                'user',
                'replies' => fn ($q) => $q->oldest()->with('user'), // ترتیب ریپلای ها از قدیمی ترین شروع میشه میاد پایین، هرچی ریپلای پایین تره جدید تره
            ])
            ->latest()
            ->paginate(6);
        return view('posts.show', compact('post', 'comments', 'liked'));
    }

    public function create(){
        $this->authorize('create', Post::class);
        $categories = Category::latest()->get();
        $tags = Tag::latest()->get();
        return view('posts.create', compact('categories', 'tags'));
    }

    public function store(Request $request){
        $this->authorize('create', Post::class);

        $validated = $request->validate([
            'title'      => 'required|string|max:255',
            'category'   => 'required|exists:categories,id',
            'content'    => 'required|string|max:300000',
            'image_path' => 'nullable|string',
            'tags'       => 'nullable|array',
            'tags.*'     => 'exists:tags,id',
        ]);

        $content = HtmlSanitizer::clean($validated['content']);
        if (! HtmlSanitizer::hasContent($content)) {
            return back()->withInput()->withErrors(['content' => 'متن پست نمی‌تواند خالی باشد.']);
        }

        $post = Post::create([
            'title'       => $validated['title'],
            'category_id' => $validated['category'],
            'content'     => $content,
            'description' => HtmlSanitizer::plainText($content, 250), // خلاصه برای لیست‌ها و جستجو
            'image'       => $validated['image_path'] ?? null,
            'user_id'     => auth()->id(),
        ]);
        $post->tags()->attach($validated['tags'] ?? []);

        return redirect()
            ->route('posts.index')
            ->with('success', 'پست با موفقیت اضافه شد');
    }

    public function edit(Post $post){
        $this->authorize('update', $post);
        $post->load('user','category','tags');
        $categories = Category::get();
        $tags = Tag::latest()->get();
        return view('posts.edit', compact('post', 'categories','tags'));
    }

    public function update(Request $request, Post $post){
        $this->authorize('update', $post);

        $validated = $request->validate([
            'title'      => 'required|string|max:255',
            'category'   => 'required|exists:categories,id',
            'content'    => 'required|string|max:300000',
            'image_path' => 'nullable|string',
            'tags'       => 'nullable|array',
            'tags.*'     => 'exists:tags,id',
        ]);

        $content = HtmlSanitizer::clean($validated['content']);
        if (! HtmlSanitizer::hasContent($content)) {
            return back()->withInput()->withErrors(['content' => 'متن پست نمی‌تواند خالی باشد.']);
        }

        $data = [
            'title'       => $validated['title'],
            'category_id' => $validated['category'],
            'content'     => $content,
            'description' => HtmlSanitizer::plainText($content, 250),
        ];

        // عکس شاخص جدید → عکس قبلی حذف شود
        if (! empty($validated['image_path'])) {
            if ($post->image && $post->image !== $validated['image_path']) {
                Storage::disk('public')->delete($post->image);
            }
            $data['image'] = $validated['image_path'];
        }

        // عکس‌هایی که از داخل متن برداشته شده‌اند از دیسک پاک شوند
        $removed = array_diff(HtmlSanitizer::imagePaths($post->content), HtmlSanitizer::imagePaths($content));
        if ($removed) {
            Storage::disk('public')->delete(array_values($removed));
        }

        $post->update($data);
        $post->tags()->sync($validated['tags'] ?? []);

        return redirect()->route('posts.show', $post)->with('success', 'پست با موفقیت اپدیت شد');
    }

    public function destroy(Post $post){
        $this->authorize('delete', $post);
        if($post->image){
            Storage::disk('public')->delete($post->image);
        }
        // عکس‌های داخل متن پست
        $inline = HtmlSanitizer::imagePaths($post->content);
        if ($inline) {
            Storage::disk('public')->delete($inline);
        }
        $post->delete();
        return redirect()->route('posts.index')->with('success','post deleted successfully');
    }

    public function uploadImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:10280',
        ]);
        $manager = new ImageManager(new Driver());
        $image   = $manager->decodeSplFileInfo($request->file('image'))
            ->scaleDown(width: 1600, height: 1600)
            ->encodeUsingMediaType('image/webp', quality: 80);

        $path = 'posts/uploads/' . now()->format('Y/m/d')
            . '/' . Str::uuid() . '.webp';

        Storage::disk('public')->put($path, (string) $image);

        return response()->json([
            'success' => true,
            'path' => $path,
            'url' => Storage::url($path),
        ]);
    }
}
