<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreTagRequest;

class TagController extends Controller
{
    public function store(StoreTagRequest $request)
    {
        $user = Auth::user();
        
        if ($user->role !== 0) {
            return response()->json([
                'error' => 'You are not authorized to create tags.',
            ]);
        }

        $request->validated($request->all());

        $tag = Tag::create([
            'name' => $request->name,
            'user_id' => $user->id
        ]);

        if (!$tag) {
            return response()->json([
                'error' => 'There was an issue while creating the tag, please try again later.',
            ]);
        }

        return response()->json([
            'success' => 'Tag created successfully.',
            'tag' => $tag,
        ]);
    }

    public function update(Request $request, Tag $tag)
    {
        $user = Auth::user();

        if ($user->role !== 0 || $tag->user->id !== $user->id) {
            return response()->json([
                'error' => 'You are not authorized to perform this operation.',
            ]);
        }

        $tag->update($request->all());

        return response()->json([
            'success' => 'Tag updated successfully.',
            'tag' => $tag,
        ]);
    }

    public function destroy(Tag $tag)
    {
        if (Auth::user()->id !== $tag->user_id) {
            return response()->json([
                'error' => 'You are not authorized to perform this action.',
            ]);
        }

        $tag->delete();

        return response()->json('Tag is deleted.');
    }
}
