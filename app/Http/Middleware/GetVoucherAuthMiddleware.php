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

        $utmSource = $request->query('utm_source');

        $validateAuth = $this->voucherService->validateAuth($brand, $campaign);
        $campaignData = $this->campaignService->getCampaign($brand, $campaign);
        $productOffer = $this->campaignService->getProductOffer($campaignData->id, $productId);

        if (!$validateAuth->isAuthGmail && $campaignData->page_template_id == 2) {
            if($utmSource) {
                return redirect()->route('product::show', [
                    'brand' => $brand,
                    'campaign' => $campaign,
                    'productId' => $productId,
                    'utm_source' => $utmSource
                ])->with([
                    'partner' => session('partner_id'),
                    'termStatus' => true
                ]);
            }
            else {
                return redirect()->route('product::show', [
                    'brand' => $brand,
                    'campaign' => $campaign,
                    'productId' => $productId,
                ])->with([
                    'partner' => session('partner_id'),
                    'termStatus' => true
                ]);
            }
        }

        if (!$validateAuth->isAuthWA && $campaignData->page_template_id == 2) {
            if($utmSource) {
                return redirect()->route('product::show', [
                    'brand' => $brand,
                    'campaign' => $campaign,
                    'productId' => $productId,
                    'utm_source' => $utmSource
                ])->with([
                    'partner' => session('partner_id'),
                    'termStatus' => true
                ]);
            }
            else {
                return redirect()->route('product::show', [
                    'brand' => $brand,
                    'campaign' => $campaign,
                    'productId' => $productId,
                ])->with([
                    'partner' => session('partner_id'),
                    'termStatus' => true
                ]);
            }
        }

        //check product type if PAID
        if($validateAuth->isAuthWA && $validateAuth->isAuthGmail && $productOffer->type=='Paid' && $campaignData->page_template_id == 2) {
            if($utmSource) {
                return redirect()->route('voucher::pay', [
                    'brand' => $brand,
                    'campaign' => $campaign,
                    'productId' => $productId,
                    'utm_source' => $utmSource
                ])->with([
                    'partner' => session('partner_id'),
                    'termStatus' => true
                ]);
            }
            else {
                return redirect()->route('voucher::pay', [
                    'brand' => $brand,
                    'campaign' => $campaign,
                    'productId' => $productId,
                ])->with([
                    'partner' => session('partner_id'),
                    'termStatus' => true
                ]);
            }
        }

        if (!$validateAuth->isAuthGmail) {
            if($utmSource) {
                return redirect()->route('google::login', [
                    'brand' => $brand,
                    'campaign' => $campaign,
                    'productId' => $productId,
                    'utm_source' => $utmSource
                ]);
            }
            else {
                return redirect()->route('google::login', [
                    'brand' => $brand,
                    'campaign' => $campaign,
                    'productId' => $productId,
                ]);
            }
        }

        if (!$validateAuth->isAuthWA) {
            if($utmSource) {
                return redirect()->route('otp::login', [
                    'brand' => $brand,
                    'campaign' => $campaign,
                    'productId' => $productId,
                    'utm_source' => $utmSource
                ]);
            }
            else {
                return redirect()->route('otp::login', [
                    'brand' => $brand,
                    'campaign' => $campaign,
                    'productId' => $productId,
                ]);
            }
        }

        return $next($request);
    }
}
