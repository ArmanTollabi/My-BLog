<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function ShowLoginForm(){
        return view('auth.login');
    }
    public function login(Request $request){

      $validated = $request->validate([
          'email' => 'required|email',
          'password' => 'required',
      ]);

        if(Auth::attempt($validated, $request->boolean('remember'))){
            $request->session()->regenerate();
            $admin = $request->user()->role === 'admin';
            return redirect()->intended(route($admin ? 'admin.dashboard' : 'dashboard'))->with('success','ورود شما با موفقیت انجام شد');
        }
        return back()->withErrors(['email'=>'رمز عبور یا ایمیل شما اشتباه است'])->onlyInput('email');
    }
    public function logout(Request $request){
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login')->with('success','با موفقیت خارج شدید');
    }
}
