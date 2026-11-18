<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use App\Services\ImageService;
use App\Support\SearchTerm;

class UserController extends Controller
{
    public function index(Request $request){
        $this->authorize('access_user', User::class);

        $term = SearchTerm::clean($request->query('search'));

        $users = User::withCount('posts')->when($term !== '',function ($query) use ($term){
            $like = SearchTerm::like($term);
            $query->where(function ($query) use ($like){
                $query->where('name','like',$like)
                    ->orWhere('email','like',$like);
            });
        })
            ->when($request->post_filter === 'most',function ($query){
                $query->orderByDesc('posts_count');
            })
            ->when($request->post_filter === 'least',function ($query){
                $query->orderBy('posts_count');
            })
            ->when($request->post_filter === 'over_5',function ($query){
                $query->has('posts','>',5);
            })
            ->when($request->post_filter === 'over_10',function ($query){
                $query->has('posts','>',10);
            })
            ->when($request->post_filter === 'over_20',function ($query){
                $query->has('posts','>',20);
            })
            ->oldest()
            ->paginate(4)
            ->withQueryString();
        return view('admin.users.index', compact('users'));

    }

    public function show(User $user)
    {
        $this->authorize('isAdmin', $user);
        return view('admin.users.show', compact('user'));
    }

    public function create()
    {   $this->authorize('access_user', User::class);
        return view('admin.users.create');
    }

    public function store(Request $request){
        $this->authorize('access_user', User::class); // قبلاً چک نمی‌شد
        // فقط ادمین اصلی (id=1) می‌تواند نقش تعیین کند؛ بقیه‌ی ادمین‌ها فقط «کاربر عادی» می‌سازند
        $canSetRole = $request->user()->can('edit_admin', User::class);

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'avatar_path' => ['nullable','string','max:255','regex:#^users-avatar/uploads/[\w/\-]+\.webp$#']
        ];
        if ($canSetRole) {
            $rules['role'] = ['required', Rule::enum(UserRole::class)];
        }

        $validated = $request->validate($rules);

        if (! $canSetRole) {
            $validated['role'] = UserRole::Subscriber->value; // هر چیزی که در درخواست باشد نادیده گرفته می‌شود
        }

        $validated['avatar'] = $validated['avatar_path'] ?? null;


        unset($validated['avatar_path']);
        User::create($validated);
        return redirect()->route('admin.users.index')->with('success', 'کاربر با موفقیت ایجاد شد');
    }

    public function edit(User $user){
        $this->authorize('update_user',$user);
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request,User $user){
        $this->authorize('update_user',$user);
        $validatedData = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255',Rule::unique('users')->ignore($user)],
            'avatar_path' => ['nullable','string','max:255','regex:#^users-avatar/uploads/[\w/\-]+\.webp$#']
        ]);

        if(!empty($validatedData['avatar_path'])){
            if($user->avatar){
                Storage::disk('public')->delete($user->avatar);
            }
            $validatedData['avatar'] = $validatedData['avatar_path'];

        }

        unset($validatedData['avatar_path']);

        // تغییر نقش: فقط ادمین اصلی (id=1) و نه روی حساب خودش. برای بقیه فیلد role کاملاً نادیده گرفته می‌شود.
        if ($request->user()->can('edit_admin', User::class) && ! $user->is($request->user())) {
            $validatedData['role'] = $request->validate([
                'role' => ['required', Rule::enum(UserRole::class)],
            ])['role'];
        }

        if($request->filled('current_password') || $request->filled('new_password')){
            $request->validate([
                'current_password' => ['required', 'string', 'min:6'],
                'new_password' => ['required', 'string', 'min:6'],
            ]);

            if(! Hash::check( $request->current_password , $user->password)){
                // dd('current pass :'.Hash::$request->current_password .'  '.'db pass :'.$user->password);
                return back()->withErrors([
                    'current_passwrod' => 'Your current password does not matches with the password you provided. Please try again.'
                ])->withInput();
            }

            $validatedData['password'] = Hash::make($request->new_password);
        }
        $user->update($validatedData);

        return redirect()->route('admin.users.show',compact('user'))->with('success','کاربر با موفقیت ویرایش شد');
    }

    public function destroy(User $user,Request $request){
        $this->authorize('delete_user',$user);
        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'کاربر با موفقیت حذف شد');
    }

    public function uploadAvatar(Request $request, ImageService $images){
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048', // قبلاً svg مجاز و سقف ~۱٫۲ گیگابایت بود
        ]);
        $path = $images->storeAvatar($request->file('avatar'), 'users-avatar/uploads'); // مربع ۲۵۶px، webp

        return response()->json([
            'success' => true,
            'path' => $path,
            'url' => Storage::url($path),
        ]);
    }

}
