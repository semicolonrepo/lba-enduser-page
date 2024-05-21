<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Services\GoogleRecaptchaService;

class VerifyGoogleRecaptchaMiddleware
{

    public function __construct(
        private GoogleRecaptchaService $googleRecaptchaService,
    ) {}

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $recaptcha = $this->googleRecaptchaService->verify(session('g_recaptcha_response'));

        if ($recaptcha->success) {
            return $next($request);
        }

        $brand = $request->route('brand');
        $campaign =  $request->route('campaign');
        $productId =  $request->route('productId');
        $utmSource = $request->query('utm_source');

        if ($utmSource) {
            return redirect()->route('product::show', [
                'brand' => $brand,
                'campaign' => $campaign,
                'productId' => $productId,
                'utm_source' => $utmSource
            ])->with([
                'partner' => session('partner_id'),
                'termStatus' => true,
                'failed' => 'Recaptcha Invalid! Silakan selesaikan reCAPTCHA dan coba lagi.',
            ]);
        } else {
            return redirect()->route('product::show', [
                'brand' => $brand,
                'campaign' => $campaign,
                'productId' => $productId,
            ])->with([
                'partner' => session('partner_id'),
                'termStatus' => true,
                'failed' => 'Recaptcha Invalid! Silakan selesaikan reCAPTCHA dan coba lagi.'
            ]);
        }
    }
}
