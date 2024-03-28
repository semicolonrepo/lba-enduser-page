<?php

namespace App\Http\Middleware;

use App\Services\VoucherService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RatingAuthMiddleware
{

    public function __construct(
        private VoucherService $voucherService,
    ) {}


    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $brand = $request->route('brand');
        $campaign =  $request->route('campaign');
        $voucherCode = $request->route('voucherCode');

        $validateAuth = $this->voucherService->validateAuth($brand, $campaign);

        if (!$validateAuth->isAuthGmail) {
            return redirect()->route('google::login::rating', [
                'brand' => $brand,
                'campaign' => $campaign,
                'voucherCode' => $voucherCode,
            ]);
        }

        if (!$validateAuth->isAuthWA && !$validateAuth->needAuthGmail) {
            return redirect()->route('otp::login::rating', [
                'brand' => $brand,
                'campaign' => $campaign,
                'voucherCode' => $voucherCode,
            ]);
        }

        return $next($request);
    }
}
