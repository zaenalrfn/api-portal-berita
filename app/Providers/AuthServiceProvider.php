<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Models\Comment;
use App\Policies\CommentPolicy;
use Laravel\Passport\Passport;
use Carbon\CarbonInterval;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Comment::class => CommentPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();

        // Access tokens untuk grant (mis. password grant)
        Passport::tokensExpireIn(now()->addMinutes(30));

        // Refresh token
        Passport::refreshTokensExpireIn(now()->addDays(7));

        // Personal Access Tokens (yang dibuat via createToken)
        Passport::personalAccessTokensExpireIn(CarbonInterval::days(1));
    }
}
