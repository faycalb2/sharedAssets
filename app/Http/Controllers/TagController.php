<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use App\Models\User;
use App\Http\Resources\TagResource;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreTagRequest;
use App\Http\Requests\UpdateTeamRequest;

class TagController extends BaseController
{
    public function index()
    {
        $user = Auth::user();
        $the_admin = $user->created_by;
        if ($the_admin == null) {
            $the_admin = $user->id;
        }

        $user = User::where('id', '=', $the_admin)->first();

        $tags = $user->tags()->get();

        return TagResource::collection($tags);
    }

    /**
     * Create tag
     * @OA\Post (
     *     path="/api/tags",
     *     tags={"Tags"},
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
     *                     "label":"Saved replies"
     *                }
     *             )
     *         )
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Success",
     *          @OA\JsonContent(
     *              @OA\Property(property="id", type="number", example=1),
     *              @OA\Property(property="name", type="string", example="Saved replies"),
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
    public function store(StoreTagRequest $request)
    {
        $this->authorize('isAdmin', User::class);

        $validated = $request->validated();
      
        $tag = Tag::create([
            'name'           => $validated['name'],
            'taggable_id'    => Auth::user()->id,
            'taggable_type'  => 'App\Models\User',
        ]);

        if (!$tag) {
            return $this->errorResponse('There was an issue while creating the tag, please try again later.');
        }

        return $this->successResponse('Tag created successfully.', new TagResource($tag));
    }

    /**
     * Update Tag
     * @OA\Put (
     *     path="/api/tags/update/{id}",
     *     tags={"Tags"},
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
     *                     "title":"Updated Tag"
     *                }
     *             )
     *         )
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Success",
     *          @OA\JsonContent(
     *              @OA\Property(property="id", type="number", example=1),
     *              @OA\Property(property="name", type="string", example="Updated Tag"),
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
    public function update(UpdateTeamRequest $request, Tag $tag)
    {
        $this->authorize('isAdmin', User::class);
        $this->authorize('canAccessTag', [User::class, $tag]);

        $tag->update($request->validated());

        return $this->successResponse('Tag updated successfully.', new TagResource($tag));
    }

    /**
     * Delete Tag
     * @OA\Delete (
     *     path="/api/tags/delete/{id}",
     *     tags={"Tags"},
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
     *             @OA\Property(property="msg", type="string", example="Tag is deleted successfully")
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
    public function destroy(Tag $tag)
    {
        $this->authorize('isAdmin', User::class);

        $this->authorize('canAccessTag', [User::class, $tag]);

        $tag->delete();

        return $this->successResponse('Tag deleted successfully.');
    }
}
