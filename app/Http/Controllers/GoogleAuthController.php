<?php

namespace App\Http\Controllers;

use App\Models\CustomerUser;
use App\Services\CampaignService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    public function __construct(
        private CampaignService $campaignService,
    ) {}

    public function login($brand, $campaign, $productId) {
        $campaignData = $this->campaignService->getCampaign($brand, $campaign);

        $viewTemplate = $campaignData->page_template_code . '.auth.login';
        return view($viewTemplate, [
            'brand' => $brand,
            'campaign' => $campaign,
            'productId' => $productId,
        ]);
    }

    public function redirect($brand, $campaign, $productId) {
        session(['brand_session' => $brand]);
        session(['campaign_session' => $campaign]);
        session(['product_id_session' => $productId]);

        return Socialite::driver('google')
            ->redirect();
    }

    public function callback() {
        try {
            $brandSession = session('brand_session');
            $campaignSession = session('campaign_session');
            $productIdSession = session('product_id_session');

            $googleUser = Socialite::driver('google')->user();

            $authGmailId = DB::table('auth_gmail')->insertGetId([
                'uuid' => Str::uuid(),
                'name' => $googleUser->getName(),
                'email' => $googleUser->getEmail(),
                'google_id' => $googleUser->getId(),
            ]);

            $authGmailUuid = DB::table('auth_gmail')->where('id', $authGmailId)->value('uuid');
            Session::put('customer_user_gmail', $authGmailUuid, 60);

            return redirect()->route('voucher::claim', [
                'brand' => $brandSession,
                'campaign' => $campaignSession,
                'productId' => $productIdSession,
            ]);

        } catch (\Throwable $th) {
            return redirect()->back()->with('failed', 'terjadi kesalahan');
        }
    }
}
