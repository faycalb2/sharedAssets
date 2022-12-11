<?php

namespace App\Models;

use App\Models\User;
use App\Models\Asset;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tag extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function user()
    {
        return $this->morphedByMany(User::class, 'taggable');
    }

    public function assets()
    {
        return $this->morphedByMany(Asset::class, 'taggable');
    }
}
