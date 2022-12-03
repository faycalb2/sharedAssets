<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tag extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'taggable_id', 'taggable_type'];

    public function taggable()
    {
        return $this->morphTo();
    }
}
