<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Http\Resources\UserResource;
use App\Http\Controllers\BaseController;
use App\Http\Requests\LoginLoginRequest;

class LoginController extends BaseController
{
    public function login(LoginLoginRequest $request)
    {
        $validated = $request->validated();

        if(!auth()->attempt(['email' => $validated['email'], 'password' => $validated['password']])){
            return $this->errorResponse('The info you provided do not match our record.');
        }
        
        $user = User::where('email', $validated['email'])->first();

        return $this->successResponse(
            'You are now logged in.', [
                'user' => new UserResource($user), 
                'token' => $user->createToken('Token for: ' . $user->name)->plainTextToken
        ]);
    }
}
