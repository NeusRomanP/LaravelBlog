<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    public $timestamps = true;

    protected $fillable = [
        'title',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function texts()
    {
        return $this->hasMany(Text::class);
    }

    public function images()
    {
        return $this->hasMany(Image::class);
    }
}
