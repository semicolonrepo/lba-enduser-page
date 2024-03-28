<?php

namespace App\Http\Controllers;

use App\Services\CampaignService;
use App\Services\GoogleService;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    public function __construct(
        private CampaignService $campaignService,
        private GoogleService $googleService,
    ) {}

    public function login($brand, $campaign, $productId) {
        $campaignData = $this->campaignService->getCampaign($brand, $campaign);

        $viewTemplate = $campaignData->page_template_code . '.auth.login';
        return view($viewTemplate, [
            'brand' => $brand,
            'campaign' => $campaign,
            'productId' => $productId,
            'data' => $campaignData,
        ]);
    }

    public function redirect($brand, $campaign, $productId) {
        session(['brand_session' => $brand]);
        session(['campaign_session' => $campaign]);
        session(['product_id_session' => $productId]);

        return Socialite::driver('google')
            ->with(['hd' => 'gmail.com'])
            ->redirect();
    }

    public function callback() {
        try {
            $arrayRoute = [
                'brand' => session('brand_session'),
                'campaign' => session('campaign_session'),
            ];

            $redirectPageName = null;

            if (session('product_id_session') !== null) {
                $arrayRoute['productId'] = session('product_id_session');
                $redirectPageName = 'voucher';
            }

            if (session('voucher_code_session') !== null) {
                $arrayRoute['voucherCode'] = session('voucher_code_session');
                $redirectPageName = 'rating';
            }

            if (session('utm_source_session') !== null) {
                $arrayRoute['utm_source'] = session('utm_source_session');
            }

            $saveSession = $this->googleService->saveSession();

            if (!$saveSession && $redirectPageName === 'voucher') {
                return redirect()->route('product::show', $arrayRoute)->with('failed', 'Gunakan domain @gmail.com ya!');
            }

            if (!$saveSession && $redirectPageName === 'rating') {
                return redirect()->route('google::login::rating', $arrayRoute)->with('failed', 'Gunakan domain @gmail.com ya!');
            }

            if ($saveSession && $redirectPageName === 'voucher') {
                return redirect()->route('voucher::claim', $arrayRoute);
            }

            if ($saveSession && $redirectPageName === 'rating') {
                return redirect()->route('rating::show', $arrayRoute);
            }

        } catch (\Throwable $th) {
            return redirect()->back()->with('failed', 'terjadi kesalahan');
        }
    }
}
