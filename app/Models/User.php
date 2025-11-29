<?php

namespace App\Models;

use Laravel\Passport\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * App\Models\User
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * ...
 *
 * // Optional: help Intelephense understand Spatie methods
 * @method bool hasRole(string|array $roles)
 * @method \Spatie\Permission\Contracts\Role|null getRoleNames()
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // User -> banyak News
    public function news()
    {
        return $this->hasMany(News::class);
    }

    // User -> banyak Comments
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}
