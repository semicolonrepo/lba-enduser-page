<?php

namespace App\Http\Middleware;

use App\Services\CampaignService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Services\GoogleRecaptchaService;

class VerifyGoogleRecaptchaMiddleware
{

    public function __construct(
        private GoogleRecaptchaService $googleRecaptchaService,
        private CampaignService $campaignService,
    ) {}

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $brandSlug = $request->route('brand');
        $campaignSlug =  $request->route('campaign');
        $productId =  $request->route('productId');
        $utmSource = $request->query('utm_source');

        $campaign = $this->campaignService->getCampaign($brandSlug, $campaignSlug);
        if (!$campaign->enabled_recaptcha) {
            return $next($request);
        }

        $recaptcha = $this->googleRecaptchaService->verify(session('g_recaptcha_response'));
        if ($recaptcha->success) {
            return $next($request);
        }

        $routeParams = [
            'brand' => $brandSlug,
            'campaign' => $campaignSlug,
            'productId' => $productId,
        ];

        if ($utmSource) {
            $routeParams['utm_source'] = $utmSource;
        }

        return redirect()->route('product::show', $routeParams)->with([
            'partner' => session('partner_id'),
            'termStatus' => true,
            'failed' => 'Recaptcha Invalid! Silakan selesaikan reCAPTCHA dan coba lagi.',
        ]);
    }
}
