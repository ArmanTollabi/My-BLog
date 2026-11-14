<?php

namespace App\Http\Controllers;

use App\Enums\PostStatus;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Http\Resources\PostResource;
use App\Models\Category;
use App\Models\Post;
use App\Http\Requests\SavePostRequest;
use App\Models\Tag;
use App\Services\ImageService;
use App\Services\PostService;
use App\Support\SearchTerm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;


class PostController extends Controller
{
    public function __construct(private readonly PostService $posts)
    {
    }

    public function apiIndex(Request $request){
        $per_page = min($request->integer('per_page', 3), 7);
        $posts = Post::published()->with('user','tags','category')
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
        abort_unless($post->isPublic(), 404);
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

    public function home()
    {
        // یک کوئری: ۶ پستِ جدیدتر هر دسته (بدون خواندن همه‌ی پست‌ها)
        $sections = Post::query()
            ->published()
            ->listColumns()
            ->with('category:id,title,slug')
            ->withCount('likes')
            ->latestPerCategory(6)
            ->sortBy('latest')
            ->get()
            ->groupBy('category_id')
            ->map(fn ($posts) => ['category' => $posts->first()->category, 'posts' => $posts])
            ->filter(fn ($s) => $s['category'] !== null);

        return view('index', compact('sections'));
    }

    /** جستجوی زنده (AJAX) — حداکثر ۵ نتیجه + تعداد کل */
    public function search(Request $request)
    {
        $q = SearchTerm::clean($request->query('q'));

        if (mb_strlen($q) < 2) {
            return response()->json(['message' => 'عبارت جستجو باید حداقل ۲ حرف باشد.'], 422);
        }

        $total = Post::published()->search($q)->count();

        $results = Post::published()->search($q)
            ->listColumns()
            ->with('category:id,title,slug')
            ->orderByRaw('CASE WHEN title LIKE ? THEN 0 ELSE 1 END', [SearchTerm::like($q)]) // اول پست‌هایی که در عنوان آمده‌اند
            ->sortBy('latest')
            ->limit(5)
            ->get()
            ->map(fn (Post $p) => [
                'title'    => $p->title,
                'url'      => route('posts.show', $p),
                'excerpt'  => Str::limit(strip_tags((string) $p->description), 90),
                'image'    => $p->image_thumb_url,
                'category' => $p->category?->title,
                'date'     => $p->published_at_fa ?? $p->created_at_fa,
            ]);

        return response()->json([
            'query'   => $q,
            'total'   => $total,
            'results' => $results,
            'see_all' => route('posts.index', ['search' => $q]),
        ]);
    }

    public function index(Request $request)
    {
        $sort = in_array($request->query('sort'), ['oldest', 'popular'], true) ? $request->query('sort') : 'latest';

        $posts = Post::query()
            ->published()
            ->listColumns()
            ->with(['user', 'category:id,title,slug', 'tags'])
            ->withCount('likes')
            ->withExists(['savers as is_saved' => fn ($q) => $q->where('users.id', auth()->id())])
            ->search($request->query('search'))
            ->when($request->category, fn ($q, $category) => $q->category($category))
            ->when($request->tag, fn ($q, $tag) => $q->tagged($tag))
            ->sortBy($sort)
            ->paginate(9)
            ->withQueryString();

        return view('posts.index', [
            'posts'      => $posts,
            'categories' => Category::withCount(['posts' => fn ($q) => $q->published()])->orderBy('title')->get(),
            'tags'       => Tag::latest()->get(),
        ]);
    }

    public function show(Post $post)
    {
        $viewer = auth()->user();

        // پیش‌نویس / زمان‌بندی‌نشده / بایگانی: فقط نویسنده و ادمین؛ بقیه 404
        abort_unless($post->isViewableBy($viewer), 404);

        // فقط ستون‌های لازم صفحه (نه ردیف کامل کاربر با رمز و ...)
        $post->load(['user:id,name,avatar', 'category:id,title,slug', 'tags']);

        // یک کوئری برای هر دو شمارنده: لایک‌ها + «کل دیدگاه‌های تاییدشده» (اصلی‌ها و ریپلای‌ها)
        $post->loadCount([
            'likes',
            'comments as approved_comments_count' => fn ($q) => $q->approved(),
        ]);

        $liked = $post->isLikedBy($viewer);
        $saved = $post->isSavedBy($viewer);

        // بازدید: هر نشست فقط یک‌بار برای هر پست؛ بازدید خود نویسنده حساب نمی‌شود
        $viewed = session('viewed_posts', []);
        if ($post->isPublic() && ! in_array($post->id, $viewed, true) && auth()->id() !== $post->user_id) {
            $post->recordView();
            session(['viewed_posts' => array_slice([...$viewed, $post->id], -100)]);
        }

        // مهمان بعد از ورود/ثبت‌نام (از پاپ‌آپ لایک) به همین پست برگردد
        if (auth()->guest()) {
            session()->put('url.intended', url()->current());
        }

        // کامنت‌های اصلیِ این صفحه + ریپلای‌هایشان (۳ کوئری کل: کامنت‌ها، ریپلای‌ها، کاربرها).
        // visibleTo: تاییدنشده‌ها فقط برای ادمین و خودِ نویسنده (قبلاً برای همه دیده می‌شد)
        $comments = $post->comments()
            ->visibleTo($viewer)
            ->whereNull('parent_id')
            ->with([
                'user:id,name,avatar',
                'replies' => fn ($q) => $q->visibleTo($viewer)->oldest()->with('user:id,name,avatar'),
            ])
            ->latest()
            ->paginate(6);

        // سایدبار: لیست دسته‌بندی‌ها (فقط ستون‌های لازم + تعداد پست، یک کوئری)
        $categories = Category::query()
            ->select(['id', 'title', 'slug'])
            ->withCount(['posts' => fn ($q) => $q->published()])
            ->orderBy('title')
            ->get();

        return view('posts.show', compact('post', 'comments', 'liked', 'saved', 'categories'));
    }

    public function create(){
        $this->authorize('create', Post::class);
        $categories = Category::latest()->get();
        $tags = Tag::latest()->get();
        return view('posts.create', compact('categories', 'tags'));
    }

    public function store(SavePostRequest $request)
    {
        $this->authorize('create', Post::class);

        $post = $this->posts->create($request->validated(), $request->user());

        // پست منتشرشده → لیست پست‌ها | بقیه (پیش‌نویس، زمان‌بندی، بایگانی) در لیست عمومی نیستند → خودِ پست (فقط برای نویسنده/ادمین قابل دیدن)
        $route = $post->isPublic() ? redirect()->route('posts.index') : redirect()->route('posts.show', $post);

        return $route->with('success', $this->savedMessage($post, 'اضافه شد'));
    }

    public function edit(Post $post){
        $this->authorize('update', $post);
        $post->load('user','category','tags');
        $categories = Category::get();
        $tags = Tag::latest()->get();
        return view('posts.edit', compact('post', 'categories','tags'));
    }

    public function update(SavePostRequest $request, Post $post)
    {
        $this->authorize('update', $post);

        $post = $this->posts->update($post, $request->validated());

        return redirect()->route('posts.show', $post)->with('success', $this->savedMessage($post, 'آپدیت شد'));
    }

    public function destroy(Post $post)
    {
        $this->authorize('delete', $post);

        $this->posts->delete($post);

        return redirect()->route('posts.index')->with('success', 'پست با موفقیت حذف شد');
    }

    /** پیام موفقیت متناسب با وضعیت پست */
    private function savedMessage(Post $post, string $verb): string
    {
        return match ($post->effective_status) {
            PostStatus::Draft     => 'پست به‌عنوان پیش‌نویس ذخیره شد و فقط برای خودت قابل مشاهده است.',
            PostStatus::Archived  => 'پست بایگانی شد و از سایت برداشته شد.',
            PostStatus::Scheduled => 'پست زمان‌بندی شد؛ در ' . $post->published_at_with_hours_fa . ' منتشر می‌شود.',
            default               => "پست با موفقیت {$verb}",
        };
    }

    public function uploadImage(Request $request, ImageService $images)
    {
        $request->validate([
            'image'   => 'required|image|mimes:jpeg,png,jpg,webp|max:10280',
            'purpose' => 'nullable|in:cover,inline',   // cover = عکس شاخص (thumbnail هم ساخته می‌شود)
        ]);

        $path = $request->input('purpose') === 'cover'
            ? $images->storeCover($request->file('image'), 'posts/uploads')
            : $images->storeScaled($request->file('image'), 'posts/uploads');

        return response()->json([
            'success' => true,
            'path'    => $path,
            'url'     => Storage::url($path),
        ]);
    }
}
