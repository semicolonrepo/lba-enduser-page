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
        private CampaignService $campaignService,
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

        if ($partnerId) {
            session(['partner_id' => $partnerId]);
        }

        $validateAuth = $this->voucherService->validateAuth($brand, $campaign);
        $campaignData = $this->campaignService->getCampaign($brand, $campaign);

        if (!$validateAuth->isAuthGmail && $campaignData->page_template_id == 2) {
            return redirect()->route('product::show', [
                'brand' => $brand,
                'campaign' => $campaign,
                'productId' => $productId,
            ])->with([
                'partner' => session('partner_id'),
                'termStatus' => true
            ]);
        }

        if (!$validateAuth->isAuthWA && $campaignData->page_template_id == 2) {
            return redirect()->route('product::show', [
                'brand' => $brand,
                'campaign' => $campaign,
                'productId' => $productId,
            ])->with([
                'partner' => session('partner_id'),
                'termStatus' => true
            ]);
        }

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
