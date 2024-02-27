<?php

namespace App\Http\Controllers;

use App\Services\CampaignService;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
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
            'data' => $campaignData,
        ]);
    }

    public function redirect(Request $request, $brand, $campaign, $productId) {
        session(['brand_session' => $brand]);
        session(['campaign_session' => $campaign]);
        session(['product_id_session' => $productId]);

        if($request->has('utm_source')) {
            session(['utm_source_session' => $request->query('utm_source')]);
        }

        if ($request->has('partner')) {
            session(['partner_id' => $request->query('partner')]);
        }

        return Socialite::driver('google')
            ->with(['hd' => 'gmail.com'])
            ->redirect();
    }

    public function callback() {
        try {
            $arrayRoute = [
                'brand' => session('brand_session'),
                'campaign' => session('campaign_session'),
                'productId' => session('product_id_session'),
            ];

            if (session('utm_source_session') !== null) {
                $arrayRoute['utm_source'] = session('utm_source_session');
            }

            $googleUser = Socialite::driver('google')->user();
            if (!Str::endsWith($googleUser->getEmail(), '@gmail.com')) {
                return redirect()->route('product::show', $arrayRoute)->with('failed', 'Gunakan domain @gmail.com ya!');
            }

            $authGmailId = DB::table('auth_gmail')->insertGetId([
                'uuid' => Str::uuid(),
                'name' => $googleUser->getName(),
                'email' => $googleUser->getEmail(),
                'google_id' => $googleUser->getId(),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            $authGmailUuid = DB::table('auth_gmail')->where('id', $authGmailId)->value('uuid');
            Session::put('customer_user_gmail', $authGmailUuid, 60);

            return redirect()->route('voucher::claim', $arrayRoute);
        } catch (\Throwable $th) {
            return redirect()->back()->with('failed', 'terjadi kesalahan');
        }
    }
}
