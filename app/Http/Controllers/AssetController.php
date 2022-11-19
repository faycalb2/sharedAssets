<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Models\Asset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\AssetResource;
use App\Http\Requests\StoreAssetRequest;

class AssetController extends Controller
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
        if ($this->canAccessTeam($request->team_id)) {
            return response()->json([
                'error' => 'You are not authorized to perform this operation.',
            ]);
        }

        $request->validated($request->all());

        $asset = Asset::create([
            'label' => $request->label,
            'content' => $request->content,
            'tag_id' => $request->tag_id,
            'team_id' => $request->team_id,
            'user_id' => Auth::user()->id,
        ]);

        if (!$asset) {
            return response()->json([
                'error' => 'There was an issue with creating the asset, please try again later.',
            ]);
        }

        return response()->json([
            'success' => 'Asset created successfully.',
            'asset' => new AssetResource($asset),
        ]);
    }

    public function update(Request $request, Asset $asset)
    {
        if ($this->canAccessTeam($request->team_id)) {
            return response()->json([
                'error' => 'You are not authorized to perform this operation.',
            ]);
        }

        $asset->update($request->all());

        return response()->json([
            'success' => 'Asset updated successfully.',
            'asset' => new AssetResource($asset),
        ]);
    }

    public function destroy(Asset $asset)
    {
        if ($this->canAccessTeam($asset->team_id)) {
            return response()->json([
                'error' => 'You are not authorized to perform this operation.',
            ]);
        }

        $asset->delete();

        return response()->json('Asset is deleted.');
    }

    private function canAccessTeam($assetTeamId)
    {
        $user = Auth::user();
        $userId = $user->id;
        $teams = Team::whereHas('users', function($q) use($userId) {
            $q->where('user_id', '=', $userId);  
        })->get();

        if ($teams->where('id', $assetTeamId)->count() === 0 || $user->role !== 0) {
            return true;
        }

        return false;
    }
}