<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Models\User;
use App\Http\Resources\TeamResource;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreTeamRequest;
use App\Http\Requests\UpdateTeamRequest;

class TeamController extends BaseController
{
    public function index()
    {
        $this->authorize('isAdmin', User::class);

        $userId = Auth::user()->id;

        return TeamResource::collection(
            Team::
                whereHas('users', function($q) use($userId) {
                    $q->where('user_id', '=', $userId);  
                })
                ->get()
        );
    }

    public function store(StoreTeamRequest $request)
    {
        $user = Auth::user();

        $this->authorize('isAdmin', $user);

        $validated = $request->validated();

        $team = Team::create([
            'name' => $validated['name']
        ]);

        if (!$team) {
            return $this->errorResponse('There was an issue while creating the team, please try again later.');
        }

        $team->users()->attach($user->id);

        return $this->successResponse('Team created successfully.', new TeamResource($team));
    }

    public function update(UpdateTeamRequest $request, Team $team)
    {
        $this->authorize('isAdmin', User::class);
        $this->authorize('canAccessTeam', [User::class, $team->id]);

        $team->update($request->validated());

        return $this->successResponse('Team updated successfully.', new TeamResource($team));
    }

    public function destroy(Team $team)
    {
        $this->authorize('isAdmin', User::class);
        $this->authorize('canAccessTeam', [User::class, $team->id]);

        $team->users(Auth::user()->id)->detach();
        $team->users(Auth::user()->id)->delete();
        $team->delete();

        return $this->successResponse('Team deleted successfully.');
    }
}
