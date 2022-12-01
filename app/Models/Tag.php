<?php

namespace App\Models;

use App\Models\User;
use App\Models\Asset;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// This you will need a new use of tags
// Tagged objects so that you can have many to many relationship with tags
// something like a pivot table
class Tag extends Model
{

    use HasFactory;

    protected $fillable = ['name', 'user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function asset()
    {
        return $this->hasMany(Asset::class);
    }
}
