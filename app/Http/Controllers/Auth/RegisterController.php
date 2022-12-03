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
    /**
     * Create admin
     * @OA\Post (
     *     path="/api/register",
     *     tags={"Register"},
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                      type="object",
     *                      @OA\Property(
     *                          property="email",
     *                          type="string"
     *                      ),
     *                      @OA\Property(
     *                          property="name",
     *                          type="string"
     *                      ),
     *                      @OA\Property(
     *                          property="password",
     *                          type="string"
     *                      ),
     *                      @OA\Property(
     *                          property="password_confirmation",
     *                          type="string"
     *                      ),
     *                      @OA\Property(
     *                          property="role",
     *                          type="integer"
     *                      )
     *                 ),
     *                 example={
     *                     "email":"example@email.com",
     *                     "name":"John Doe",
     *                     "password": "123456",
     *                     "password_confirmation": "123456",
     *                     "role": 0
     *                }
     *             )
     *         )
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Success",
     *          @OA\JsonContent(
     *              @OA\Property(property="id", type="number", example=1),
     *              @OA\Property(property="email", type="string", example="email"),
     *              @OA\Property(property="name", type="string", example="name"),
     *              @OA\Property(property="password", type="string", example="password"),
     *              @OA\Property(property="role", type="string", example=0),
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
    public function storeAdmin(StoreAdminRequest $request)
    {
        $validated = $request->validated();

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        return $this->successResponse(
            'Thank you for registring.', [
                'user' => new UserResource($user), 
                'token' => $user->createToken('Token for: ' . $user->name)->plainTextToken
        ]);
    }

    /**
     * Admin creates a user
     * @OA\Post (
     *     path="/api/user/register",
     *     tags={"Register"},
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                      type="object",
     *                      @OA\Property(
     *                          property="email",
     *                          type="string"
     *                      ),
     *                      @OA\Property(
     *                          property="name",
     *                          type="string"
     *                      ),
     *                      @OA\Property(
     *                          property="password",
     *                          type="string"
     *                      ),
     *                      @OA\Property(
     *                          property="team_id",
     *                          type="integer"
     *                      )
     *                 ),
     *                 example={
     *                     "email":"example@email.com",
     *                     "name":"John Doe",
     *                     "password": "123456",
     *                     "team_id": 1
     *                }
     *             )
     *         )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="success",
     *          @OA\JsonContent(
     *              @OA\Property(property="id", type="number", example=1),
     *              @OA\Property(property="email", type="string", example="email"),
     *              @OA\Property(property="name", type="string", example="name"),
     *              @OA\Property(property="password", type="string", example="password"),
     *              @OA\Property(property="role", type="string", example=1),
     *              @OA\Property(property="updated_at", type="string", example="2022-10-11T09:25:53.000000Z"),
     *              @OA\Property(property="created_at", type="string", example="2022-10-11T09:25:53.000000Z"),
     *          )
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="invalid",
     *          @OA\JsonContent(
     *              @OA\Property(property="msg", type="string", example="fail"),
     *          )
     *      )
     * )
     */
    public function storeUser(StoreUserRequest $request)
    {
        $validated = $request->validated();

        $this->authorize('isAdmin', User::class);
        $this->authorize('canAccessTeam', [User::class, $validated['team_id']]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 1,
            'created_by' => Auth::user()->id,
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
