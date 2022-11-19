<?php

namespace App\Models;

use App\Models\Tag;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Asset extends Model
{
    use HasFactory;

    protected $fillable = ['label', 'content', 'tag_id', 'team_id', 'user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function tag()
    {
        return $this->belongsTo(Tag::class);
    }

    public function scopeSearch($query)
    {
        if (!empty(request('search'))) {
            $query
                ->where('label', 'like', '%' . request('search') . '%')
                ->Orwhere('content', 'like', '%' . request('search') . '%');
        }
    }
}
