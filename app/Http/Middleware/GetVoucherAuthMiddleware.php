<?php

namespace App\Http\Middleware;

use App\Services\CampaignService;
use App\Services\VoucherService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class GetVoucherAuthMiddleware
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
        $partnerId = $request->input('partner');
        $brand = $request->route('brand');
        $campaign =  $request->route('campaign');
        $productId =  $request->route('productId');

        if (!session('partner_id')) {
            session(['partner_id' => $partnerId]);
        }

        $validateAuth = $this->voucherService->validateAuth($brand, $campaign);
        if (!$validateAuth->isAuthGmail) {
            return redirect()->route('google::login', [
                'brand' => $brand,
                'campaign' => $campaign,
                'productId' => $productId,
            ]);
        }

        if (!$validateAuth->isAuthWA) {
            return redirect()->route('otp::login', [
                'brand' => $brand,
                'campaign' => $campaign,
                'productId' => $productId,
            ]);
        }

        return $next($request);
    }
}
