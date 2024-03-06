<?php

namespace App\Http\Controllers;

use App\Services\CampaignService;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthRatingController extends Controller
{
    public function __construct(
        private CampaignService $campaignService,
    ) {}

    public function login($brand, $campaign, $voucherCode) {
        $campaignData = $this->campaignService->getCampaign($brand, $campaign);

        $viewTemplate = $campaignData->page_template_code . '.auth.login';
        return view($viewTemplate, [
            'brand' => $brand,
            'campaign' => $campaign,
            'voucherCode' => $voucherCode,
            'data' => $campaignData,
        ]);
    }

    public function redirect(Request $request, $brand, $campaign, $voucherCode) {
        session(['brand_session' => $brand]);
        session(['campaign_session' => $campaign]);
        session(['voucher_code_session' => $voucherCode]);

        if($request->has('utm_source')) {
            session(['utm_source_session' => $request->query('utm_source')]);
        }

        return Socialite::driver('google')
            ->with(['hd' => 'gmail.com'])
            ->redirect();
    }
}
