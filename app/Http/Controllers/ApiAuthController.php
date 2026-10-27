<?php

namespace App\Http\Controllers;

use App\Http\Requests\ApiAuthRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ApiAuthController extends Controller
{
    public function apiLogin(ApiAuthRequest $request){

        $user = User::where('email',$request->email)->first();
        if(! $user || !Hash::check($request->password, $user->password)){
            return response()->json([
                'error' => 'credentials not matched'
            ],401);
        }
        $token = $user->createToken('api-token')->plainTextToken;
        return response()->json([
            'message' => 'Logged in successfully',
            'token' => $token,
        ]);
    }

    public function apiLogout(Request $request){

        $request->user()->currentAccessToken()->delete();
        return response()->json([
            'message' => 'Logged out successfully'
        ]);

    }
}
