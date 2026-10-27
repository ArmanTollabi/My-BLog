<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Password;

class PasswordResetLinkController extends Controller
{
    public function create(){
        return view('auth.forgot-password');
    }

    public function store(Request $request){
        $request->validate(['email' => 'required|email']);
        $status = Password::sendResetLink($request->only('email')); //$request->only('email') is array and sendResetLink method wants an 'array' type of entry, can't accept string thats why we can not give it $request->email! cuz it's string!
        if($status === Password::RESET_LINK_SENT){
           return back()->with('status',__($status));
        }
        return back()->withInput($request->only('email'))->withErrors(['email' => __($status)]);


    }
}
