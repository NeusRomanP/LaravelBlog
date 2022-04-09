<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Text extends Model
{
    use HasFactory;

    protected $fillable = [
        'content',
        'index',
    ];

    public function post()
    {
        return $this->belongsTo(Post::class);
    }
}
