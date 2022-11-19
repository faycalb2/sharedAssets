<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginLoginRequest;

class LoginController extends Controller
{
    public function login(LoginLoginRequest $request)
    {
        $request->validated($request->all());

        if(!auth()->attempt($request->only('email','password'))){
            return response()->json([
                'error' => 'The info you provided do not match our record.',
            ]);
        }
        
        $user = User::where('email', $request->email)->first();

        return response()->json([
            'success' => 'You are now logged in.',
            'user' => $user,
            'token' => $user->createToken('Token for: ' . $user->name)->plainTextToken
        ]);
    }
}
