<?php

namespace App\Http\Controllers;

use App\Models\CustomerUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    public function redirect($brand, $campaign) {
        session(['brand_session' => $brand]);
        session(['campaign_session' => $campaign]);

        return Socialite::driver('google')
            ->redirect();
    }

    public function callback() {
        try {
            $brandSession = session('brand_session');
            $campaignSession = session('campaign_session');

            $googleUser = Socialite::driver('google')->user();

            $customerUser = CustomerUser::where('google_id', $googleUser->getId())->first();

            if (!$customerUser) {
                $newUser = CustomerUser::create([
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
                ]);

                Auth::guard('customer_user')->login($newUser);

                return redirect()->route('voucher-redeem', [
                    'brand' => $brandSession,
                    'campaign' => $campaignSession,
                ]);
            } else {
                Auth::guard('customer_user')->login($customerUser);

                return redirect()->route('voucher-redeem', [
                    'brand' => $brandSession,
                    'campaign' => $campaignSession,
                ]);
            }

        } catch (\Throwable $th) {
            dd($th);
        }
    }
}
