<?php

namespace App\Http;

use App\Http\Middleware\ActivityLogMiddleware;
use App\Http\Middleware\GetVoucherAuthMiddleware;
use App\Http\Middleware\RatingAuthMiddleware;
use App\Http\Middleware\SetVoucherClaimSessionMiddleware;
use App\Http\Middleware\ValidateCampaignMiddleware;
use App\Http\Middleware\VerifyCampaignHasRatingMiddleware;
use App\Http\Middleware\VerifyVoucherClaimedMiddleware;
use App\Http\Middleware\VerifyVoucherHasRatingMiddleware;
use App\Http\Middleware\VerifyVoucherPermissionMiddleware;
use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array<int, class-string|string>
     */
    protected $middleware = [
        // \App\Http\Middleware\TrustHosts::class,
        \App\Http\Middleware\TrustProxies::class,
        \Illuminate\Http\Middleware\HandleCors::class,
        \App\Http\Middleware\PreventRequestsDuringMaintenance::class,
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        \App\Http\Middleware\TrimStrings::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array<string, array<int, class-string|string>>
     */
    protected $middlewareGroups = [
        'web' => [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],

        'api' => [
            // \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
            \Illuminate\Routing\Middleware\ThrottleRequests::class.':api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],
    ];

    /**
     * The application's middleware aliases.
     *
     * Aliases may be used instead of class names to conveniently assign middleware to routes and groups.
     *
     * @var array<string, class-string|string>
     */
    protected $middlewareAliases = [
        'auth' => \App\Http\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'auth.session' => \Illuminate\Session\Middleware\AuthenticateSession::class,
        'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
        'can' => \Illuminate\Auth\Middleware\Authorize::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'password.confirm' => \Illuminate\Auth\Middleware\RequirePassword::class,
        'precognitive' => \Illuminate\Foundation\Http\Middleware\HandlePrecognitiveRequests::class,
        'signed' => \App\Http\Middleware\ValidateSignature::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
        'voucherAuth' => GetVoucherAuthMiddleware::class,
        'validate.campaign' => ValidateCampaignMiddleware::class,
        'activity.log' => ActivityLogMiddleware::class,
        'rating.auth' => RatingAuthMiddleware::class,
        'verify.voucher.claimed' => VerifyVoucherClaimedMiddleware::class,
        'verify.voucher.permission' => VerifyVoucherPermissionMiddleware::class,
        'verify.voucher.has.rating' => VerifyVoucherHasRatingMiddleware::class,
        'verify.campaign.has.rating' => VerifyCampaignHasRatingMiddleware::class,
        'set.voucher.claim.session' => SetVoucherClaimSessionMiddleware::class,
    ];
}
