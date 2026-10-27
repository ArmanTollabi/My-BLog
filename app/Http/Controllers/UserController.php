<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Password;

class UserController extends Controller
{
    public function index(User $user,request $request){
        $this->authorize('access_user', $user);

        $users = User::withCount('posts')->when($request->search,function ($query,$search){
            $query->where(function ($query) use ($search){
                $query->where('name','like','%'.$search.'%')
                    ->orWhere('email','like','%'.$search.'%');
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
        return view('users.index', compact('users'));

    }

    public function show(User $user)
    {
        $this->authorize('is-admin', $user);
        return view('users.show', compact('user'));
    }

    public function create()
    {   $this->authorize('access_user', User::class);
        return view('users.create');
    }

    public function store(Request $request){
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'role' => ['required', Rule::in(['admin', 'subscriber'])],
            'avatar_path' => ['nullable','string']
        ]);

        $validated['avatar'] = $validated['avatar_path'] ?? null;


        unset($validated['avatar_path']);
        User::create($validated);
        return redirect()->route('users.index')->with('success', 'User created successfully');
    }

    public function edit(User $user){
        $this->authorize('update_user',$user);
        return view('users.edit', compact('user'));
    }

    public function update(Request $request,User $user){
        $this->authorize('update_user',$user);
        $validatedData = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255',Rule::unique('users')->ignore($user)],
            'avatar_path' => ['nullable','string']
        ]);

        if(!empty($validatedData['avatar_path'])){
            if($user->avatar){
                Storage::disk('public')->delete($user->avatar);
            }
            $validatedData['avatar'] = $validatedData['avatar_path'];

        }

        unset($validatedData['avatar_path']);

        // تغییر نقش: فقط ادمین، و نه روی حساب خودش (جلوگیری از قفل شدن پنل و ارتقای نقش توسط کاربر عادی)
        if ($request->user()->role === 'admin' && ! $user->is($request->user())) {
            $validatedData['role'] = $request->validate([
                'role' => ['required', Rule::in(['admin', 'subscriber'])],
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

        return redirect()->route('users.show',compact('user'))->with('success','یوزر ادیت شد');
    }

    public function destroy(User $user){
        $this->authorize('delete_user',$user);
        $user->delete();
        return redirect()->route('users.index')->with('success', 'User deleted successfully');
    }

    public function uploadAvatar(Request $request){
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:1280000',
        ]);
        $path = $request->file('avatar')->store('users-avatar/uploads/'.now()->format('Y/m/d'),'public');

        return response()->json([
            'success' => true,
            'path' => $path,
            'url' => Storage::url($path),
        ]);
    }

}
