<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use App\Models\User;
use Illuminate\Http\Request;
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

        return TagResource::collection(
            Tag::
                where('user_id', '=', $the_admin)
                ->get()
        );
    }
    
    public function store(StoreTagRequest $request)
    {
        $user = Auth::user();

        $this->authorize('isAdmin', User::class);

        $validated = $request->validated();

        $tag = Tag::create([
            'name' => $validated['name'],
            'user_id' => $user->id
        ]);

        if (!$tag) {
            return $this->errorResponse('There was an issue while creating the tag, please try again later.');
        }

        return $this->successResponse('Tag created successfully.', new TagResource($tag));
    }

    public function update(UpdateTeamRequest $request, Tag $tag)
    {
        $this->authorize('isAdmin', User::class);
        $this->authorize('canAccessTag', [User::class, $tag]);
        
        $tag->update($request->validated());

        return $this->successResponse('Tag updated successfully.', new TagResource($tag));
    }

    public function destroy(Tag $tag)
    {
        $this->authorize('isAdmin', User::class);

        $this->authorize('canAccessTag', [User::class, $tag]);

        $tag->delete();

        return $this->successResponse('Tag deleted successfully.');
    }
}
