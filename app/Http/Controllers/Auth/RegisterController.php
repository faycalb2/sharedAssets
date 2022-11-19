<?php

namespace App\Http\Controllers\Auth;

use App\Models\Team;
use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\StoreAdminRequest;

class RegisterController extends Controller
{
    public function storeAdmin(StoreAdminRequest $request)
    {
        $request->validated($request->all());

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        if (!$user) {
            return response()->json([
                'error' => 'There was an issue with registration, please try again later.',
            ]);
        }

        return response()->json([
            'success' => 'Thank you for registring.',
            'user' => $user,
            'token' => $user->createToken('Token for: ' . $user->name)->plainTextToken
        ]);
    }

    public function storeUser(StoreUserRequest $request)
    {
        
        $userId = Auth::user()->id;
        
        $teams = Team::whereHas('users', function($q) use($userId) {
            $q->where('user_id', '=', $userId);  
        })->get();
        
        if ($teams->where('id', $request->team_id)->count() === 0) {
            return response()->json([
                'error' => 'Error finding the team, please contact support.',
            ]);
        }
        
        $request->validated($request->all());
    
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 1,
            'team_id' => $request->team_id,
        ]);

        $user->teams()->attach($request->team_id);

        if (!$user) {
            return response()->json([
                'error' => 'There was an issue with registration, please try again later.',
            ]);
        }

        return response()->json([
            'success' => 'User created successfully.',
            'user' => $user,
        ]);
    }
}
