<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Asset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\AssetResource;
use App\Http\Requests\StoreAssetRequest;

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

    public function store(StoreAssetRequest $request)
    {
        $this->authorize('isAdmin', User::class);
        $this->authorize('canAccessTeam', [User::class, $request->team_id]);

        $validated = $request->validated();

        $asset = Asset::create([
            'label' => $validated['label'],
            'content' => $validated['content'],
            'tag_id' => $validated['tag_id'],
            'team_id' => $validated['team_id'],
            'user_id' => Auth::user()->id,
        ]);

        if (!$asset) {
            return $this->errorResponse('There was an issue with creating the asset, please try again later.');
        }

        return $this->successResponse('Asset created successfully.', new AssetResource($asset));
    }

    public function update(Request $request, Asset $asset)
    {
        $this->authorize('isAdmin', User::class);
        $this->authorize('canAccessTeam', [User::class, $asset->team_id]);

        $asset->update($request->validated());

        return $this->successResponse('Asset updated successfully.', new AssetResource($asset));
    }

    public function destroy(Asset $asset)
    {
        $this->authorize('isAdmin', User::class);
        $this->authorize('canAccessTeam', [User::class, $asset->team_id]);

        $asset->delete();

        return $this->successResponse('Asset deleted successfully.');
    }
}
