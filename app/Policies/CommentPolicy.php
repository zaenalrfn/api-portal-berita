<?php

namespace App\Policies;

use App\Models\Comment;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CommentPolicy
{
    use HandlesAuthorization;

    // Admin bebas semuanya
    public function before(User $user, $ability)
    {
        if (method_exists($user, 'hasRole') && $user->hasRole('admin')) {
            return true;
        }
    }

    // User biasa hanya boleh update komentarnya sendiri
    public function update(User $user, Comment $comment)
    {
        return $user->id === $comment->user_id;
    }

    // User biasa hanya boleh menghapus komentarnya sendiri
    public function delete(User $user, Comment $comment)
    {
        return $user->id === $comment->user_id;
    }
}
