<?php

namespace App\Policies;

use App\Models\Tag;
use App\Models\Team;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AuthPolicy
{
    use HandlesAuthorization;

    public function isAdmin(User $user)
    {
        return $user->role == 0;
    }

    public function canAccessTeam(User $user, $team_id)
    {
        $userId = $user->id;

        $teams = Team::
                    whereHas('users', function($q) use($userId) {
                        $q->where('user_id', '=', $userId);  
                    })
                    ->get();

        if ($teams->where('id', $team_id)->count() === 0) {
            return false;
        }

        return true;
    }

    public function canAccessTag(User $user, Tag $tag)
    {
        $owner = $tag->user()->first();

        if ($owner->id !== $user->id) {
            return false;
        }

        return true;
    }
}
