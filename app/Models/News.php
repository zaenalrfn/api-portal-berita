<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class News extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'slug',
        'content',
        'thumbnail',
        'published_at',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    // News dimiliki oleh User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // News -> banyak Comment
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}
