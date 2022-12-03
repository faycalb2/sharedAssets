<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Asset;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\AssetResource;
use App\Http\Requests\StoreAssetRequest;
use App\Http\Requests\UpdateAssetRequest;

class AssetController extends BaseController
{
    public function index()
    {
        $userId = Auth::user()->id;

        return AssetResource::collection(
            Asset::
                latest()
                ->search(request('search'))
                ->whereHas('team', function($query) use ($userId) {
                    $query->whereHas('users', function($query) use ($userId) {
                        $query->where('user_id', $userId);
                    });
                })
                ->get()
        );
    }

    /**
     * Create asset
     * @OA\Post (
     *     path="/api/assets",
     *     tags={"Assets"},
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                      type="object",
     *                      @OA\Property(
     *                          property="label",
     *                          type="string"
     *                      ),
     *                      @OA\Property(
     *                          property="content",
     *                          type="string"
     *                      ),
     *                      @OA\Property(
     *                          property="team_id",
     *                          type="integer"
     *                      )
     *                 ),
     *                 example={
     *                     "label":"Price how much",
     *                     "content":"some content goes here....",
     *                     "team_id": 1
     *                }
     *             )
     *         )
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Success",
     *          @OA\JsonContent(
     *              @OA\Property(property="id", type="number", example=1),
     *              @OA\Property(property="label", type="string", example="Price how much"),
     *              @OA\Property(property="content", type="string", example="some content goes here...."),
     *              @OA\Property(property="team_id", type="integer", example=1),
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
    public function store(StoreAssetRequest $request)
    {
        $this->authorize('isAdmin', User::class);
        $this->authorize('canAccessTeam', [User::class, $request->team_id]);

        $validated = $request->validated();

        $asset = Asset::create([
            'label' => $validated['label'],
            'content' => $validated['content'],
            'team_id' => $validated['team_id'],
            'user_id' => Auth::user()->id,
        ]);

        if (!$asset) {
            return $this->errorResponse('There was an issue with creating the asset, please try again later.');
        }

        return $this->successResponse('Asset created successfully.', new AssetResource($asset));
    }

    /**
     * Update Asset
     * @OA\Put (
     *     path="/api/assets/update/{id}",
     *     tags={"Assets"},
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
     *                          property="label",
     *                          type="string"
     *                      ),
     *                      @OA\Property(
     *                          property="content",
     *                          type="string"
     *                      ),
     *                      @OA\Property(
     *                          property="team_id",
     *                          type="integer"
     *                      )
     *                 ),
     *                 example={
     *                     "title":"Updated Label",
     *                     "content":"Updated content goes here...",
     *                     "team_id": 1
     *                }
     *             )
     *         )
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Success",
     *          @OA\JsonContent(
     *              @OA\Property(property="id", type="number", example=1),
     *              @OA\Property(property="label", type="string", example="Updated Label"),
     *              @OA\Property(property="content", type="string", example="Updated content goes here..."),
     *              @OA\Property(property="team_id", type="string", example=1),
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
    public function update(UpdateAssetRequest $request, Asset $asset)
    {
        $this->authorize('isAdmin', User::class);
        $this->authorize('canAccessTeam', [User::class, $asset->team_id]);

        $asset->update($request->validated());

        return $this->successResponse('Asset updated successfully.', new AssetResource($asset));
    }

    /**
     * Delete Asset
     * @OA\Delete (
     *     path="/api/assets/delete/{id}",
     *     tags={"Assets"},
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
     *             @OA\Property(property="msg", type="string", example="Asset is deleted successfully")
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
    public function destroy(Asset $asset)
    {
        $this->authorize('isAdmin', User::class);
        $this->authorize('canAccessTeam', [User::class, $asset->team_id]);
        
        $asset->delete();

        return $this->successResponse('Asset deleted successfully.');
    }
}