<?php

namespace App\Http\Controllers\Auth;

use App\Models\Team;
use App\Models\User;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\StoreUserRequest;
use App\Http\Controllers\BaseController;
use App\Http\Requests\StoreAdminRequest;

class RegisterController extends BaseController
{
    public function storeAdmin(StoreAdminRequest $request)
    {
        $validated = $request->validated();

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        if (!$user) {
            return response()->json([
                'error' => 'There was an issue with registration, please try again later.',
            ]);
        }

        return $this->successResponse(
            'Thank you for registring.', [
                'user' => new UserResource($user), 
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
        
        $validated = $request->validated();

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 1,
            'created_by' => $userId,
            'team_id' => $validated['team_id'],
        ]);

        $user->teams()->attach($validated['team_id']);

        if (!$user) {
            return response()->json([
                'error' => 'There was an issue with registration, please try again later.',
            ]);
        }

        return $this->successResponse('User is added successfully.');
    }
}
