<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'news_id',
        'user_id',
        'comment',
    ];

    // Comment -> User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Comment -> News
    public function news()
    {
        return $this->belongsTo(News::class);
    }
}
