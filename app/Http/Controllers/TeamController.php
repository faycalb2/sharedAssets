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

    /**
     * Create Team
     * @OA\Post (
     *     path="/api/teams",
     *     tags={"Teams"},
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                      type="object",
     *                      @OA\Property(
     *                          property="name",
     *                          type="string"
     *                      )
     *                 ),
     *                 example={
     *                     "label":"Sales"
     *                }
     *             )
     *         )
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Success",
     *          @OA\JsonContent(
     *              @OA\Property(property="id", type="number", example=1),
     *              @OA\Property(property="name", type="string", example="Sales"),
     *              @OA\Property(property="updated_at", type="string", example="2022-10-11T09:25:53.000000Z"),
     *              @OA\Property(property="created_at", type="string", example="2022-10-11T09:25:53.000000Z"),
     *          )
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad Request"
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     * )
     */
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

    /**
     * Update Team
     * @OA\Put (
     *     path="/api/teams/update/{id}",
     *     tags={"Teams"},
     *     @OA\Parameter(
     *         in="path",
     *         name="id",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                      type="object",
     *                      @OA\Property(
     *                          property="name",
     *                          type="string"
     *                      )
     *                 ),
     *                 example={
     *                     "title":"Updated Team"
     *                }
     *             )
     *         )
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Success",
     *          @OA\JsonContent(
     *              @OA\Property(property="id", type="number", example=1),
     *              @OA\Property(property="name", type="string", example="Updated Team"),
     *              @OA\Property(property="updated_at", type="string", example="2021-12-11T09:25:53.000000Z"),
     *              @OA\Property(property="created_at", type="string", example="2021-12-11T09:25:53.000000Z")
     *          )
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad Request"
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     * )
     */
    public function update(UpdateTeamRequest $request, Team $team)
    {
        $this->authorize('isAdmin', User::class);
        $this->authorize('canAccessTeam', [User::class, $team->id]);

        $team->update($request->validated());

        return $this->successResponse('Team updated successfully.', new TeamResource($team));
    }

    /**
     * Delete Team
     * @OA\Delete (
     *     path="/api/teams/delete/{id}",
     *     tags={"Teams"},
     *     @OA\Parameter(
     *         in="path",
     *         name="id",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *          response=204,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *             @OA\Property(property="msg", type="string", example="Team is deleted successfully")
     *         )
     *       ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Resource Not Found"
     *      )
     * )
     */
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
