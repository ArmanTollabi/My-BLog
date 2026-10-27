<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\Rules\Password as PasswordRule;

class NewPasswordController extends Controller
{
    public function create(Request $request,string $token){
        return view('auth.reset-password',[
            'token' => $token,
            'email' => $request->email
        ]);

    }
    public function store(Request $request)
    {
     $request->validate([
         'email' => 'required|string|email',
         'password' => ['required', 'confirmed', PasswordRule::defaults()],
         'token' => 'required'
     ]);
     $status = Password::reset($request->only('email', 'password', 'password_confirmation', 'token'),
         function ($user) use ($request) {
         $user->forceFill([
             'password' => Hash::make($request->password),
         ]);
     });
     if($status == Password::PASSWORD_RESET){
         return redirect()->route('login')->with('success', __($status));
     }
     return back()->withInput($request->only('email'))->withErrors(['email' => __($status)]);

    }

}
