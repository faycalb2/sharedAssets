<?php

namespace App\Http\Controllers;

use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreTeamRequest;

class TeamController extends Controller
{
    public function store(StoreTeamRequest $request)
    {
        $user = Auth::user();

        if ($user->role !== 0) {
            return response()->json([
                'error' => 'You are not authorized to perform this operation.',
            ]);
        }

        $request->validated($request->all());

        $team = Team::create([
            'name' => $request->name
        ]);

        if (!$team) {
            return response()->json([
                'error' => 'There was an issue while creating the team, please try again later.',
            ]);
        }

        $team->users()->attach($user->id);

        return response()->json([
            'success' => 'Team created successfully.',
            'team' => $team,
        ]);
    }

    public function update(Request $request, Team $team)
    {
        $user = Auth::user();
        $userId = $user->id;

        $teams = Team::whereHas('users', function($q) use($userId) {
            $q->where('user_id', '=', $userId);  
        })->get();

        if ($teams->where('id', $team->id)->count() === 0 || $user->role !== 0) {
            return response()->json([
                'error' => 'You are not authorized to perform this operation.',
            ]);
        }

        $team->update($request->all());

        return response()->json([
            'success' => 'team updated successfully.',
            'team' => $team,
        ]);
    }

    public function destroy(Team $team)
    {
        $verified_team = $team->users()->where('user_id', Auth::user()->id)->first();
        
        if (!$verified_team) {
            return response()->json([
                'error' => 'You are not authorized to perform this action.',
            ]);
        }

        $team->users(Auth::user()->id)->detach();
        $team->users(Auth::user()->id)->delete();
        $team->delete();

        return response()->json('Team is deleted.');
    }
}
