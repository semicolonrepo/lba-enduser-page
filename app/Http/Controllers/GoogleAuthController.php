<?php

namespace App\Http\Controllers;

use App\Models\CustomerUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    public function redirect($brand, $campaign) {
        return Socialite::driver('google')
            ->with([
                'brand' => $brand,
                'campaign' => $campaign,
            ])
            ->redirect();
    }

    public function callback() {
        try {
            $googleUser = Socialite::driver('google')->user();

            $customerUser = CustomerUser::where('google_id', $googleUser->getId())->first();

            if (!$customerUser) {
                $newUser = CustomerUser::create([
                    'name' => $customerUser->getName(),
                    'email' => $customerUser->getEmail(),
                    'google_id' => $customerUser->getId(),
                ]);

                Auth::guard('customer_user')->login($newUser);

                return redirect()->route('voucher-redeem', [
                    'brand' => 'indomie',
                    'campaign' => 'indomie-indomie-selera-promo-akhir-tahun',
                ]);
            } else {
                Auth::guard('customer_user')->login($customerUser);

                return redirect()->route('voucher-redeem', [
                    'brand' => 'indomie',
                    'campaign' => 'indomie-indomie-selera-promo-akhir-tahun',
                ]);
            }

        } catch (\Throwable $th) {
            dd($th);
        }
    }
}
